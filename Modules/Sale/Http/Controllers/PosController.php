<?php

namespace Modules\Sale\Http\Controllers;

use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\People\Entities\Customer;
use Modules\Product\Entities\Category;
use Modules\Product\Entities\Product;
use Modules\Sale\Entities\Sale;
use Modules\Sale\Entities\SaleDetails;
use Modules\Sale\Entities\SalePayment;
use Modules\Sale\Http\Requests\StorePosSaleRequest;

class PosController extends Controller
{

    public function index() {
        Cart::instance('sale')->destroy();

        $customers = Customer::all();
        $product_categories = Category::all();

        return view('sale::pos.index', compact('product_categories', 'customers'));
    }


    public function store(StorePosSaleRequest $request) {
        DB::transaction(function () use ($request) {
            $due_amount = $request->total_amount - $request->paid_amount;

            if ($due_amount == $request->total_amount) {
                $payment_status = 'Unpaid';
            } elseif ($due_amount > 0) {
                $payment_status = 'Partial';
            } else {
                $payment_status = 'Paid';
            }

            $user = auth()->user();
            
            // Validate and scope store_id
            $store = \App\Models\Store::when(!$user->hasRole('Super Admin'), function ($query) use ($user) {
                return $query->where('business_id', $user->business_id);
            })->findOrFail($request->store_id);

            // Validate and scope customer_id if provided
            $customer = null;
            if ($request->customer_id) {
                $customer = Customer::when(!$user->hasRole('Super Admin'), function ($query) use ($user) {
                    return $query->where('business_id', $user->business_id);
                })->findOrFail($request->customer_id);
            }

            $sale = Sale::create([
                'date' => now()->format('Y-m-d'),
                'reference' => 'PSL',
                'customer_id' => $customer ? $customer->id : null,
                'customer_name' => $customer ? $customer->customer_name : 'Walk-in Customer',
                'store_id' => $store->id,
                'business_id' => $store->business_id, // Explicitly set
                'tax_percentage' => $request->tax_percentage,
                'discount_percentage' => $request->discount_percentage,
                'shipping_amount' => $request->shipping_amount * 100,
                'paid_amount' => $request->paid_amount * 100,
                'total_amount' => $request->total_amount * 100,
                'due_amount' => $due_amount * 100,
                'status' => 'Completed',
                'payment_status' => $payment_status,
                'payment_method' => $request->payment_method,
                'note' => $request->note,
                'tax_amount' => Cart::instance('sale')->tax() * 100,
                'discount_amount' => Cart::instance('sale')->discount() * 100,
            ]);

            foreach (Cart::instance('sale')->content() as $cart_item) {
                SaleDetails::create([
                    'sale_id' => $sale->id,
                    'product_id' => $cart_item->id,
                    'product_name' => $cart_item->name,
                    'product_code' => $cart_item->options->code,
                    'quantity' => $cart_item->qty,
                    'price' => $cart_item->price * 100,
                    'unit_price' => $cart_item->options->unit_price * 100,
                    'sub_total' => $cart_item->options->sub_total * 100,
                    'product_discount_amount' => $cart_item->options->product_discount * 100,
                    'product_discount_type' => $cart_item->options->product_discount_type,
                    'product_tax_amount' => $cart_item->options->product_tax * 100,
                ]);

                $product = Product::findOrFail($cart_item->id);
                $product->update([
                    'product_quantity' => $product->product_quantity - $cart_item->qty
                ]);
            }

            Cart::instance('sale')->destroy();

            if ($request->has('payments') && is_array($request->payments)) {
                foreach ($request->payments as $payment) {
                    if ($payment['amount'] > 0) {
                        SalePayment::create([
                            'date' => now()->format('Y-m-d'),
                            'reference' => 'INV/'.$sale->reference,
                            'amount' => $payment['amount'], // already unmasked in JS or handled as float
                            'sale_id' => $sale->id,
                            'payment_method' => $payment['method']
                        ]);
                    }
                }
            } elseif ($sale->paid_amount > 0) {
                SalePayment::create([
                    'date' => now()->format('Y-m-d'),
                    'reference' => 'INV/'.$sale->reference,
                    'amount' => $sale->paid_amount,
                    'sale_id' => $sale->id,
                    'payment_method' => $request->payment_method
                ]);
            }
        });

        toast('POS Sale Created!', 'success');

        return redirect()->route('sales.index');
    }
}
