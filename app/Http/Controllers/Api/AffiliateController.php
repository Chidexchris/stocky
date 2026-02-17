<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Affiliate;
use App\Models\Referral;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AffiliateController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'firstName' => 'required|string|max:255',
            'lastName'  => 'required|string|max:255',
            'email'     => 'required|email|unique:users,email',
            'password'  => 'required|string|min:6|confirmed',
        ]);

        return DB::transaction(function () use ($request) {
            // 1. Create User
            $user = User::create([
                'name'      => $request->firstName . ' ' . $request->lastName,
                'email'     => strtolower($request->email),
                'password'  => Hash::make($request->password),
                'is_active' => 1,
            ]);

            // 2. Assign Affiliate Role
            $role = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'Affiliate']);
            $user->assignRole($role);

            // 3. Create Affiliate Record
            $affiliate = Affiliate::create([
                'user_id'        => $user->id,
                'affiliate_code' => $this->generateUniqueCode(),
            ]);

            return response()->json([
                'success' => true,
                'user' => [
                    'id'    => $user->id,
                    'name'  => $user->name,
                    'email' => $user->email,
                    'is_affiliate' => true,
                    'affiliate_code' => $affiliate->affiliate_code,
                ],
            ], 201);
        });
    }

    public function getStats(Request $request)
    {
        $request->validate(['user_id' => 'required|exists:users,id']);
        $affiliate = Affiliate::where('user_id', $request->user_id)->first();
        if (!$affiliate) return response()->json(['success' => false, 'error' => 'Affiliate not found']);

        return response()->json([
            'success' => true,
            'stats' => [
                'affiliate_code' => $affiliate->affiliate_code,
                'referral_count' => $affiliate->referral_count,
                'balance' => $affiliate->balance,
                'referrals' => $affiliate->referrals()->with('referredUser')->latest()->get()
            ]
        ]);
    }

    public function withdraw(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:100',
            'bank_name' => 'required|string',
            'bank_code' => 'required|string',
            'account_number' => 'required|string',
            'account_name' => 'required|string',
        ]);

        $affiliate = Affiliate::where('user_id', $request->user_id)->first();
        if (!$affiliate) return response()->json(['success' => false, 'error' => 'Affiliate not found']);

        if ($affiliate->balance < $request->amount) {
            return response()->json(['success' => false, 'error' => 'Insufficient balance']);
        }

        // Generate a unique reference for the withdrawal
        $merchantReference = 'WD-' . time() . '-' . $affiliate->id;

        // 1. Initiate transfer via OPay
        $transferResult = $this->initiateOpayTransfer([
            'reference'      => $merchantReference,
            'amount'         => (int)($request->amount * 100), // convert to kobo
            'bankCode'       => $request->bank_code,
            'bankAccountNo'  => $request->account_number,
            'receiverName'   => $request->account_name,
            'reason'         => 'Affiliate Withdrawal - ' . $user->name,
        ]);

        if (!$transferResult['success']) {
            return response()->json(['success' => false, 'error' => $transferResult['error'] ?? 'Transfer failed']);
        }

        // Create withdrawal request record
        \App\Models\WithdrawalRequest::create([
            'affiliate_id' => $affiliate->id,
            'amount' => $request->amount,
            'bank_name' => $request->bank_name,
            'bank_code' => $request->bank_code,
            'account_number' => $request->account_number,
            'account_name' => $request->account_name,
            'status' => 'completed', // For now we assume successful transfer means completed
            'reference' => $transferResult['orderId'] ?? $merchantReference,
        ]);

        // Deduct balance
        $affiliate->decrement('balance', $request->amount);

        return response()->json(['success' => true, 'message' => 'Withdrawal processed successfully']);
    }

    private function initiateOpayTransfer($data)
    {
        $merchantId = config('services.opay.merchant_id');
        $secretKey  = config('services.opay.secret_key');
        $baseUrl    = config('services.opay.base_url');

        $payload = [
            'reference' => $data['reference'],
            'amount' => [
                'currency' => 'NGN',
                'total' => $data['amount']
            ],
            'receiver' => [
                'bankCode' => $data['bankCode'],
                'bankAccountNo' => $data['bankAccountNo'],
                'receiverName' => $data['receiverName'],
                'type' => 'INDIVIDUAL'
            ],
            'reason' => $data['reason'],
            'country' => 'NG',
        ];

        $jsonPayload = json_encode($payload);
        $signature = hash_hmac('sha512', $jsonPayload, $secretKey);

        try {
            $response = \Illuminate\Support\Facades\Http::withHeaders([
                'Authorization' => 'Bearer ' . $signature,
                'MerchantId' => $merchantId,
                'Content-Type' => 'application/json',
            ])->post($baseUrl . '/api/v1/international/transfer/toBank', $payload);

            $result = $response->json();

            if ($response->successful() && isset($result['code']) && $result['code'] === '00000') {
                return [
                    'success' => true,
                    'orderId' => $result['data']['orderId'] ?? null
                ];
            }

            \Illuminate\Support\Facades\Log::error('OPay Transfer Failed', [
                'payload' => $payload,
                'response' => $result
            ]);

            return [
                'success' => false,
                'error' => $result['message'] ?? 'Transfer failed'
            ];
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('OPay Transfer Exception', [
                'message' => $e->getMessage()
            ]);
            return [
                'success' => false,
                'error' => 'An error occurred while processing the transfer'
            ];
        }
    }

    private function generateUniqueCode()
    {
        do {
            $code = strtoupper(Str::random(8));
        } while (Affiliate::where('affiliate_code', $code)->exists());

        return $code;
    }
}
