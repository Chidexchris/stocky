<?php

namespace Modules\Sale\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Sale\Entities\Sale;
use Modules\Sale\Entities\SaleDetails;
use Modules\Sale\Entities\SalePayment;
use Modules\People\Entities\Customer;
use Modules\Product\Entities\Product;

class SyncController extends Controller
{
    public function getProductsForCache() {
        $user = auth()->user();
        $query = Product::query();

        if (!$user->hasRole('Super Admin')) {
            $query->where('business_id', $user->business_id);
            if (!$user->hasRole('Business Owner') && !$user->hasRole('Admin')) {
                $query->where('store_id', $user->store_id);
            }
        }

        return response()->json($query->select('id', 'product_name', 'product_code', 'product_price', 'product_quantity', 'product_unit')->get());
    }

    public function syncOfflineData(Request $request) {
        $item = $request->all();
        
        if ($item['type'] === 'sale') {
            return $this->processSale($item['data']);
        }

        return response()->json(['message' => 'Unknown sync type'], 400);
    }

    protected function processSale($data) {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        // Validate store_id: must belong to the user's business
        $targetStore = \App\Models\Store::where('id', $data['store_id'])
            ->where('business_id', $user->business_id)
            ->first();

        if (!$targetStore) {
            return response()->json(['message' => 'Unauthorized store access'], 403);
        }

        // If user is not an admin, they can ONLY push to their own store
        if (!$user->hasRole('Super Admin') && !$user->hasRole('Business Owner') && !$user->hasRole('Admin')) {
            if ($data['store_id'] != $user->store_id) {
                return response()->json(['message' => 'Unauthorized store access'], 403);
            }
        }

        return DB::transaction(function () use ($data, $user, $targetStore) {
            // We use explicit data mapping instead of just passing the array to avoid mass assignment risks
            $sale = Sale::create([
                'date' => date('Y-m-d', ($data['timestamp'] ?? now()->getTimestamp() * 1000) / 1000),
                'reference' => 'PSL-OFFLINE',
                'customer_id' => $data['customer_id'] ?? null,
                'customer_name' => $data['customer_name'] ?? 'Walk-in Customer',
                'store_id' => $targetStore->id,
                'business_id' => $user->business_id, // Explicitly set from auth user
                'tax_percentage' => (float)($data['tax_percentage'] ?? 0),
                'discount_percentage' => (float)($data['discount_percentage'] ?? 0),
                'shipping_amount' => (float)($data['shipping_amount'] ?? 0) * 100,
                'paid_amount' => (float)($data['paid_amount'] ?? 0) * 100,
                'total_amount' => (float)($data['total_amount'] ?? 0) * 100,
                'due_amount' => (float)($data['due_amount'] ?? 0) * 100,
                'status' => 'Completed',
                'payment_status' => $data['payment_status'] ?? 'Paid',
                'payment_method' => $data['payment_method'] ?? 'Cash',
                'note' => ($data['note'] ?? '') . ' (Created Offline)',
                'tax_amount' => (float)($data['tax_amount'] ?? 0) * 100,
                'discount_amount' => (float)($data['discount_amount'] ?? 0) * 100,
                'user_id' => $user->id,
            ]);

            if (isset($data['items']) && is_array($data['items'])) {
                foreach ($data['items'] as $item) {
                    // Re-fetch product to ensure it belongs to the same business/store
                    $product = Product::where('id', $item['id'])
                        ->where('business_id', $user->business_id)
                        ->where('store_id', $targetStore->id)
                        ->first();

                    if (!$product) continue; // Skip invalid products

                    SaleDetails::create([
                        'sale_id' => $sale->id,
                        'product_id' => $product->id,
                        'product_name' => $product->product_name, // Trust DB, not client name
                        'product_code' => $product->product_code,
                        'quantity' => (int)$item['qty'],
                        'price' => (float)$item['price'] * 100,
                        'unit_price' => (float)$item['unit_price'] * 100,
                        'sub_total' => (float)$item['sub_total'] * 100,
                        'product_discount_amount' => (float)($item['discount'] ?? 0) * 100,
                        'product_discount_type' => $item['discount_type'] ?? 'fixed',
                        'product_tax_amount' => (float)($item['tax'] ?? 0) * 100,
                    ]);

                    $product->update([
                        'product_quantity' => $product->product_quantity - (int)$item['qty']
                    ]);
                }
            }

            if ($sale->paid_amount > 0) {
                SalePayment::create([
                    'date' => $sale->date,
                    'reference' => 'INV/'.$sale->reference,
                    'amount' => $sale->paid_amount,
                    'sale_id' => $sale->id,
                    'payment_method' => $sale->payment_method,
                    'business_id' => $user->business_id,
                ]);
            }

            return response()->json(['message' => 'Sale synced successfully', 'id' => $sale->id]);
        });
    }
}
