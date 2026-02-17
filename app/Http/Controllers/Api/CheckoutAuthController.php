<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\User;
use App\Models\Business;
use App\Models\Subscription;
use App\Models\Plan;
use Carbon\Carbon;
use App\Notifications\AccountActivatedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;

class CheckoutAuthController extends Controller
{
    /**
     * Check if an email exists in the system.
     * POST /api/checkout/check-email
     */
    public function checkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', strtolower($request->email))
            ->whereNotNull('business_id')
            ->whereNull('store_id')
            ->first();

        $activePlanName = null;
        if ($user && $user->business_id) {
            $subscription = Subscription::where('business_id', $user->business_id)
                ->where('status', 'active')
                ->where('ends_at', '>', now())
                ->first();
            
            if ($subscription) {
                $activePlanName = $subscription->plan ? $subscription->plan->name : null;
            }
        }

        return response()->json([
            'exists' => !!$user,
            'active_plan_name' => $activePlanName,
        ]);
    }

    /**
     * Get all available subscription plans.
     * GET /api/checkout/plans
     */
    public function getPlans(Request $request)
    {
        $currencyInfo = $this->getCurrencyInfo($request->ip());
        $targetCurrency = $currencyInfo['currency'];
        
        $plans = Plan::all()->map(function ($plan) use ($targetCurrency) {
            return [
                'id' => $plan->id,
                'name' => $plan->name,
                'description' => $plan->description,
                'price' => (float) $this->convertFromUsd($plan->price, $targetCurrency),
                'price_annual' => (float) $this->convertFromUsd($plan->price_annual, $targetCurrency),
                'limit_users' => $plan->limit_users,
                'limit_stores' => $plan->limit_stores,
                'features' => $plan->features,
            ];
        });

        return response()->json([
            'success' => true,
            'plans' => $plans,
            'currency' => $targetCurrency,
            'country' => $currencyInfo['country'],
        ]);
    }

    /**
     * Login with email and password.
     * POST /api/checkout/login
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', strtolower($request->email))
            ->whereNotNull('business_id')
            ->whereNull('store_id')
            ->first();

        // If not a business user, maybe it's an affiliate user
        if (!$user) {
            $user = User::where('email', strtolower($request->email))->first();
        }

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'error' => 'Invalid email or password.',
            ], 401);
        }

        if ($user->is_active != 1) {
            return response()->json([
                'success' => false,
                'error' => 'Your account is deactivated. Please contact support.',
            ], 403);
        }

        $activePlanName = null;
        if ($user->business_id) {
            $subscription = Subscription::where('business_id', $user->business_id)
                ->where('status', 'active')
                ->where('ends_at', '>', now())
                ->first();
            
            if ($subscription) {
                $activePlanName = $subscription->plan ? $subscription->plan->name : null;
            }
        }

        return response()->json([
            'success' => true,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'business_id' => $user->business_id,
                'active_plan_name' => $activePlanName,
                'is_affiliate' => $user->hasRole('Affiliate'),
            ],
        ]);
    }

    /**
     * Register a new user account.
     * POST /api/checkout/register
     */
    public function register(Request $request)
    {
        $request->validate([
            'firstName'    => 'required|string|max:255',
            'lastName'     => 'required|string|max:255',
            'email'        => 'required|email|unique:users,email',
            'password'     => 'required|string|min:6|confirmed',
            'businessName' => 'required|string|max:255',
            'referral_code' => 'nullable|string|exists:affiliates,affiliate_code',
        ]);

        return DB::transaction(function () use ($request) {
            // 1. Create Business (Enabled for trial access by default)
            $business = Business::create([
                'name'          => $request->businessName,
                'email'         => strtolower($request->email),
                'is_active'     => true,
                'trial_ends_at' => now()->addDays(7),
            ]);

            // 2. Create User
            $user = User::create([
                'name'        => $request->firstName . ' ' . $request->lastName,
                'email'       => strtolower($request->email),
                'password'    => Hash::make($request->password),
                'is_active'   => 1,
                'business_id' => $business->id,
            ]);

            // 3. Assign Role (Required for permissions)
            $role = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'Business Owner']);
            $user->assignRole($role);

            // 4. Handle Referral Logic
            if ($request->referral_code) {
                $affiliate = \App\Models\Affiliate::where('affiliate_code', $request->referral_code)->first();
                if ($affiliate) {
                    \App\Models\Referral::create([
                        'affiliate_id'     => $affiliate->id,
                        'referred_user_id' => $user->id,
                        'status'           => 'pending',
                    ]);
                    $affiliate->increment('referral_count');
                }
            }

            return response()->json([
                'success' => true,
                'user' => [
                    'id'          => $user->id,
                    'name'        => $user->name,
                    'email'       => $user->email,
                    'business_id' => $user->business_id,
                ],
            ], 201);
        });
    }

    /**
     * Validate a coupon code.
     * POST /api/checkout/validate-coupon
     */
    public function validateCoupon(Request $request)
    {
        $request->validate(['code' => 'required|string']);

        $coupon = Coupon::where('code', strtoupper($request->code))->first();

        if (!$coupon) {
            return response()->json([
                'success' => false,
                'error' => 'Coupon code does not exist.',
            ], 404);
        }

        if (!$coupon->is_active) {
            return response()->json([
                'success' => false,
                'error' => 'This coupon is no longer active.',
            ], 400);
        }

        if ($coupon->expires_at && $coupon->expires_at->isPast()) {
            return response()->json([
                'success' => false,
                'error' => 'This coupon has expired.',
            ], 400);
        }

        if (!is_null($coupon->usage_limit) && $coupon->times_used >= $coupon->usage_limit) {
            return response()->json([
                'success' => false,
                'error' => 'This coupon has reached its usage limit.',
            ], 400);
        }

        return response()->json([
            'success' => true,
            'coupon' => [
                'code' => $coupon->code,
                'type' => $coupon->type,
                'value' => $coupon->value,
            ],
        ]);
    }

    /**
     * Record a subscription after successful payment.
     * POST /api/checkout/subscribe
     */
    public function subscribe(Request $request)
    {
        $request->validate([
            'user_id'        => 'required|integer|exists:users,id',
            'plan_name'      => 'required|string',
            'period_months'  => 'required|integer|in:1,12,24',
            'amount'         => 'required|integer|min:0',
            'currency'       => 'required|string|size:3',
            'payment_method' => 'required|string|in:opay,google_pay',
        ]);

        $plan = Plan::where('name', $request->plan_name)->first();
        if (!$plan) return response()->json(['success' => false, 'error' => 'Plan not found.'], 404);

        $user = User::findOrFail($request->user_id);
        $reference = 'SUB-' . time() . '-' . $user->id;

        // OPay API Logic
        $merchantId = trim(config('services.opay.merchant_id'));
        $publicKey = trim(config('services.opay.public_key'));
        $baseUrl = config('services.opay.base_url', 'https://testapi.opaycheckout.com');

        // Shadow Conversion Logic: Convert back to NGN major units then to kobo
        $finalNgnAmount = $this->convertToNgn($request->amount, $request->currency);
        
        // Final Gateway Parameters (Fixed to NGN/NG for compatibility)
        $gatewayCurrency = 'NGN';
        $gatewayCountry = 'NG';

        \Illuminate\Support\Facades\Log::info('OPay Shadow Currency Conversion', [
            'ip' => $request->ip(),
            'user_sent_currency' => $request->currency,
            'user_sent_amount' => $request->amount,
            'converted_to_ngn_kobo' => $finalNgnAmount,
        ]);

        $payload = [
            'country' => $gatewayCountry,
            'reference' => $reference,
            'amount' => [
                'total' => (string)$finalNgnAmount, 
                'currency' => $gatewayCurrency,
            ],
            'returnUrl' => config('app.frontend_url', 'http://localhost:5173') . '/success',
            'callbackUrl' => route('checkout.webhook.opay'),
            'cancelUrl' => config('app.frontend_url', 'http://localhost:5173') . '/checkout',
            'evokeOpay' => true,
            'customerVisitSource' => 'WEB',
            'userInfo' => [
                'userEmail' => $user->email,
                'userId' => (string)$user->id,
                'userName' => $user->name,
                'userMobile' => $user->phone ?? '0000000000',
            ],
            'product' => [
                'name' => $plan->name . ' Subscription',
                'description' => $plan->name . ' Subscription (' . $request->period_months . ' months)',
            ],
        ];

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $publicKey,
                'MerchantId' => $merchantId,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->post($baseUrl . '/api/v1/international/cashier/create', $payload);

            if ($response->successful()) {
                $data = $response->json();
                if ($data['code'] === '00000') {
                    // Record pending subscription/payment in DB if needed
                    // For now we just return the URL
                    return response()->json([
                        'success' => true,
                        'checkout_url' => $data['data']['cashierUrl'],
                        'reference' => $reference
                    ]);
                }
                \Illuminate\Support\Facades\Log::error('OPay API Error Detail', [
                    'response' => $data,
                    'payload' => $payload
                ]);
                return response()->json(['success' => false, 'error' => $data['message'] ?? 'OPay error'], 400);
            }
            \Illuminate\Support\Facades\Log::error('OPay Connection Error', [
                'status' => $response->status(),
                'body' => $response->body(),
                'payload' => $payload
            ]);
            return response()->json(['success' => false, 'error' => 'Could not initialize OPay transaction.'], 500);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * OPay Webhook Handler
     */
    public function handleWebhook(Request $request)
    {
        $payload = $request->getContent();
        $headerSignature = $request->header('Signature');
        $secretKey = config('services.opay.secret_key');

        // 1. Verify Signature
        $computedSignature = hash_hmac('sha512', $payload, $secretKey);

        if (!hash_equals($computedSignature, $headerSignature)) {
            return response()->json(['success' => false, 'error' => 'Invalid signature'], 401);
        }

        $data = json_decode($payload, true);
        if (!$data || !isset($data['payload'])) {
            return response()->json(['success' => false, 'error' => 'Invalid payload'], 400);
        }

        $innerPayload = $data['payload'];
        $orderId = $innerPayload['orderId']; // e.g., SUB-12345678-1
        $status = $innerPayload['status'];

        if ($status !== 'SUCCESSFUL') {
            return response()->json(['success' => true, 'message' => 'Status not successful']);
        }

        // 2. Extract User ID from Reference (SUB-timestamp-userId)
        $parts = explode('-', $orderId);
        $userId = end($parts);
        $user = User::find($userId);

        if (!$user) {
            return response()->json(['success' => false, 'error' => 'User not found'], 404);
        }

        // 3. Activate Subscription
        // Note: For a real production app, we would store the pending subscription 
        // with the reference and amount to verify here. 
        // For this implementation, we'll use the amount from the webhook.
        
        return DB::transaction(function () use ($user, $innerPayload) {
            // Find plan based on product description or stored pending sub
            // Here we'll derive it from productDesc if possible, or use a default
            $planName = explode(' ', $innerPayload['productDesc'] ?? 'Basic')[0];
            $plan = Plan::where('name', $planName)->first() ?? Plan::first();
            
            // Extract period from productDesc "Plan Subscription (X months)"
            preg_match('/\((\d+) months\)/', $innerPayload['productDesc'] ?? '', $matches);
            $months = isset($matches[1]) ? (int)$matches[1] : 1;

            if (!$user->business_id) {
                $business = Business::create([
                    'name'     => $user->name . "'s Business",
                    'plan_id'  => $plan->id,
                    'is_active' => true,
                ]);
                $user->update(['business_id' => $business->id]);
            } else {
                $business = Business::find($user->business_id);
                $business->update(['plan_id' => $plan->id]);
            }

            // Deactivate any existing active subscription
            Subscription::where('business_id', $business->id)
                ->where('status', 'active')
                ->update(['status' => 'expired']);

            // Create new subscription
            $startsAt = Carbon::now();
            $endsAt   = Carbon::now()->addMonths($months);

            Subscription::create([
                'business_id' => $business->id,
                'plan_id'     => $plan->id,
                'status'      => 'active',
                'starts_at'   => $startsAt,
                'ends_at'     => $endsAt,
            ]);

            // 4. Handle Affiliate Commission
            $referral = \App\Models\Referral::where('referred_user_id', $user->id)
                ->where('status', 'pending')
                ->first();

            if ($referral) {
                $affiliate = $referral->affiliate;
                if ($affiliate) {
                    // Calculate commission percentage based on plan name
                    $commissionRate = 0;
                    if ($plan->name === 'Starter') {
                        $commissionRate = 0.005; // 0.5%
                    } elseif ($plan->name === 'Business') {
                        $commissionRate = 0.01;  // 1.0%
                    } elseif ($plan->name === 'Enterprise') {
                        $commissionRate = 0.015; // 1.5%
                    }

                    if ($commissionRate > 0) {
                        // OPay amount is in kobo (cents), convert to major units for balance update
                        $paymentAmountKobo = (int)$innerPayload['amount'];
                        $commissionAmount = ($paymentAmountKobo / 100) * $commissionRate;
                        
                        $affiliate->increment('balance', $commissionAmount);
                        $referral->update(['status' => 'active']);

                        \Illuminate\Support\Facades\Log::info('Affiliate Commission Awarded', [
                            'affiliate_id' => $affiliate->id,
                            'referred_user_id' => $user->id,
                            'plan' => $plan->name,
                            'payment_amount_kobo' => $paymentAmountKobo,
                            'commission_amount' => $commissionAmount,
                        ]);
                    }
                }
            }

            return response()->json(['success' => true]);
        });
    }
    private function getCurrencyInfo($ip)
    {
        if ($ip === '127.0.0.1' || $ip === '::1') {
            return ['country' => 'NG', 'currency' => 'NGN'];
        }

        try {
            $response = Http::timeout(5)->get("http://ip-api.com/json/{$ip}");
            if ($response->successful()) {
                $data = $response->json();
                $detectedCountry = $data['countryCode'] ?? 'NG';

                $currencyMap = [
                    'NG' => 'NGN',
                    'EG' => 'EGP',
                    'GH' => 'GHS',
                    'GB' => 'GBP',
                    'FR' => 'EUR',
                    'DE' => 'EUR',
                    'KE' => 'KES',
                    'ZA' => 'ZAR',
                    'US' => 'USD',
                ];

                return [
                    'country' => $detectedCountry,
                    'currency' => $currencyMap[$detectedCountry] ?? 'USD',
                ];
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('GeoIP Error: ' . $e->getMessage());
        }

        return ['country' => 'NG', 'currency' => 'NGN'];
    }

    private function convertFromUsd($amountCents, $toCurrency)
    {
        $majorUnits = $amountCents / 100;
        
        if ($toCurrency === 'USD') {
            return number_format($majorUnits, 2, '.', '');
        }

        try {
            // Fetch rate: 1 USD = X LocalCurrency
            $response = Http::timeout(5)->get("https://api.exchangerate-api.com/v4/latest/USD");
            if ($response->successful()) {
                $rates = $response->json()['rates'];
                if (isset($rates[$toCurrency])) {
                    $rate = $rates[$toCurrency];
                    $converted = $majorUnits * $rate;
                    return number_format($converted, 2, '.', '');
                }
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('USD Conversion Error: ' . $e->getMessage());
        }

        return number_format($majorUnits, 2, '.', '');
    }

    private function convertToNgn($amount, $fromCurrency)
    {
        // If it's already NGN, return as is (assuming it's already in kobo)
        if ($fromCurrency === 'NGN') {
            return (string)$amount;
        }

        try {
            // Fetch rate: 1 NGN = X LocalCurrency
            $response = Http::timeout(5)->get("https://api.exchangerate-api.com/v4/latest/NGN");
            if ($response->successful()) {
                $rates = $response->json()['rates'];
                if (isset($rates[$fromCurrency])) {
                    $ngnPerUnit = 1 / $rates[$fromCurrency]; // 1 LocalUnit = X NGN
                    // amount is in local minor units (cents), convert to kobo
                    $ngnTotal = round($amount * $ngnPerUnit);
                    return (string)$ngnTotal;
                }
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Reverse Conversion Error: ' . $e->getMessage());
        }

        // Fallback: If conversion fails, treat as NGN kobo to avoid charging 0
        return (string)$amount;
    }
}
