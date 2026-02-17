<?php

namespace Modules\Transfer\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class TransferController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */

    public function index(TransfersDataTable $dataTable) {
        // Permission check (using Spatie permissions if seeded, otherwise ensure role check)
        // abort_if(Gate::denies('access_transfers'), 403); 

        return $dataTable->render('transfer::index');
    }

    public function create() {
        // abort_if(Gate::denies('create_transfers'), 403);

        // Scope to business
        $user = auth()->user();
        if ($user->hasRole('Super Admin')) {
            $stores = \App\Models\Store::orderBy('name')->get();
        } else {
            $stores = \App\Models\Store::where('business_id', $user->business_id)->orderBy('name')->get();
        }
        
        return view('transfer::create', compact('stores'));
    }

    public function store(Request $request) {
        $request->validate([
            'date' => 'required|date',
            'from_store_id' => 'required|exists:stores,id',
            'to_store_id' => 'required|exists:stores,id|different:from_store_id',
            'status' => 'required|string',
            'product_ids' => 'required|array',
            'quantities' => 'required|array',
            'note' => 'nullable|string|max:1000',
        ]);

        \DB::transaction(function () use ($request) {
            $transfer = \Modules\Transfer\Entities\Transfer::create([
                'date' => $request->date,
                'from_store_id' => $request->from_store_id,
                'to_store_id' => $request->to_store_id,
                'status' => $request->status,
                'note' => $request->note,
                'item_count' => count($request->product_ids),
                'total_quantity' => array_sum($request->quantities),
                'user_id' => auth()->id(),
            ]);

            foreach ($request->product_ids as $key => $product_id) {
                $product = \Modules\Product\Entities\Product::findOrFail($product_id);

                \Modules\Transfer\Entities\TransferDetail::create([
                    'transfer_id' => $transfer->id,
                    'product_id' => $product->id,
                    'product_name' => $product->product_name,
                    'product_code' => $product->product_code,
                    'quantity' => $request->quantities[$key],
                    'unit_price' => $product->product_cost * 100,
                    'sub_total' => $product->product_cost * $request->quantities[$key] * 100,
                ]);

                if ($request->status == 'Completed') {
                    // Deduct from source product
                    $product->update([
                        'product_quantity' => $product->product_quantity - $request->quantities[$key]
                    ]);

                    // Find or create product in destination store
                    $destProduct = \Modules\Product\Entities\Product::where('product_code', $product->product_code)
                        ->where('store_id', $request->to_store_id)
                        ->first();

                    if ($destProduct) {
                        $destProduct->update([
                            'product_quantity' => $destProduct->product_quantity + $request->quantities[$key]
                        ]);
                    } else {
                        // Clone the product to the destination store
                        $newProduct = $product->replicate();
                        $newProduct->store_id = $request->to_store_id;
                        $newProduct->product_quantity = $request->quantities[$key];
                        $newProduct->save();
                    }
                }
            }
        });

        toast('Stock Transfer Created!', 'success');

        return redirect()->route('transfers.index');
    }

    public function show(\Modules\Transfer\Entities\Transfer $transfer) {
        // Scoping Check via Policy or manual check
        $user = auth()->user();
        if (!$user->hasRole('Super Admin') && $transfer->fromStore->business_id !== $user->business_id) {
             abort(403);
        }

        return view('transfer::show', compact('transfer'));
    }

    public function edit(\Modules\Transfer\Entities\Transfer $transfer) {
         $user = auth()->user();
         if (!$user->hasRole('Super Admin') && $transfer->fromStore->business_id !== $user->business_id) {
             abort(403);
         }

        if ($user->hasRole('Super Admin')) {
            $stores = \App\Models\Store::orderBy('name')->get();
        } else {
            $stores = \App\Models\Store::where('business_id', $user->business_id)->orderBy('name')->get();
        }

        return view('transfer::edit', compact('transfer', 'stores'));
    }

    public function update(Request $request, \Modules\Transfer\Entities\Transfer $transfer) {
        $request->validate([
            'date' => 'required|date',
            'status' => 'required|string',
            'note' => 'nullable|string|max:1000',
        ]);

        \DB::transaction(function () use ($request, $transfer) {
            // If status is changing to Completed, update stock
            if ($request->status == 'Completed' && $transfer->status != 'Completed') {
                foreach ($transfer->transferDetails as $detail) {
                    $product = \Modules\Product\Entities\Product::findOrFail($detail->product_id);

                    // Deduct from source product
                    $product->update([
                        'product_quantity' => $product->product_quantity - $detail->quantity
                    ]);

                    // Find or create product in destination store
                    $destProduct = \Modules\Product\Entities\Product::where('product_code', $product->product_code)
                        ->where('store_id', $transfer->to_store_id)
                        ->first();

                    if ($destProduct) {
                        $destProduct->update([
                            'product_quantity' => $destProduct->product_quantity + $detail->quantity
                        ]);
                    } else {
                        // Clone the product to the destination store
                        $newProduct = $product->replicate();
                        $newProduct->store_id = $transfer->to_store_id;
                        $newProduct->product_quantity = $detail->quantity;
                        $newProduct->save();
                    }
                }
            }

            $transfer->update([
                'date' => $request->date,
                'status' => $request->status,
                'note' => $request->note,
            ]);
        });

        toast('Stock Transfer Updated!', 'info');

        return redirect()->route('transfers.index');
    }

    public function destroy(\Modules\Transfer\Entities\Transfer $transfer) {
        $transfer->delete();

        toast('Stock Transfer Deleted!', 'warning');

        return redirect()->route('transfers.index');
    }
}
