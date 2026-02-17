<?php

namespace Modules\Product\Http\Controllers;

use Modules\Product\DataTables\ProductDataTable;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Modules\Product\Entities\Product;
use Modules\Product\Http\Requests\StoreProductRequest;
use Modules\Product\Http\Requests\UpdateProductRequest;
use Modules\Upload\Entities\Upload;

class ProductController extends Controller
{

    public function index(ProductDataTable $dataTable) {
        abort_if(Gate::denies('access_products'), 403);

        $stores = \App\Models\Store::orderBy('name')->get();

        return $dataTable->render('product::products.index', compact('stores'));
    }


    public function create() {
        abort_if(Gate::denies('create_products'), 403);

        $stores = \App\Models\Store::where('is_active', true)->get();

        return view('product::products.create', compact('stores'));
    }


    public function store(StoreProductRequest $request) {
        $stores = $request->input('stores', []);
        
        // Validate and scope all provided stores
        $validStores = \App\Models\Store::whereIn('id', $stores)
            ->when(!auth()->user()->hasRole('Super Admin'), function ($query) {
                return $query->where('business_id', auth()->user()->business_id);
            })
            ->pluck('id')
            ->toArray();

        // If no valid stores found, fallback to user's assigned store if they are not an admin
        if (empty($validStores) && !auth()->user()->hasRole('Super Admin')) {
            $validStores = [auth()->user()->store_id];
        }

        foreach ($validStores as $storeId) {
            $payload = array_merge($request->except(['document', 'stores']), [
                'product_quantity' => 0,
                'store_id' => $storeId,
                'business_id' => auth()->user()->hasRole('Super Admin') ? (\App\Models\Store::find($storeId)->business_id ?? null) : auth()->user()->business_id
            ]);
            
            // Check for uniqueness within store to prevent duplicates
            // We use 'product_code' + 'store_id' unique index now, so we can check if it exists or let DB throw error.
            // Better to check to avoid 500.
            $exists = Product::where('store_id', $storeId)->where('product_code', $request->product_code)->exists();
            if ($exists) {
                continue; // Skip existing products in this store
            }

            $product = Product::create($payload);

            if ($request->has('document')) {
                // Pre-check storage limit
                $business = auth()->user()->business;
                if ($business && $business->storageLimitReached() && !auth()->user()->hasRole('Super Admin')) {
                    toast('Storage limit reached. Product created without images.', 'warning');
                } else {
                    foreach ($request->input('document', []) as $file) {
                        $product->addMedia(Storage::path('temp/dropzone/' . $file))
                            ->preservingOriginal() // Important to preserve for next iteration
                            ->toMediaCollection('images');
                    }
                }
            }
        }

        toast('Products Created Successfully!', 'success');

        return redirect()->route('products.index');
    }


    public function show(Product $product) {
        abort_if(Gate::denies('show_products'), 403);

        return view('product::products.show', compact('product'));
    }


    public function edit(Product $product) {
        abort_if(Gate::denies('edit_products'), 403);

        return view('product::products.edit', compact('product'));
    }


    public function update(UpdateProductRequest $request, Product $product) {
        $product->update($request->except('document'));

        if ($request->has('document')) {
            if (count($product->getMedia('images')) > 0) {
                foreach ($product->getMedia('images') as $media) {
                    if (!in_array($media->file_name, $request->input('document', []))) {
                        $media->delete();
                    }
                }
            }

            $media = $product->getMedia('images')->pluck('file_name')->toArray();

            foreach ($request->input('document', []) as $file) {
                if (count($media) === 0 || !in_array($file, $media)) {
                    $product->addMedia(Storage::path('temp/dropzone/' . $file))->toMediaCollection('images');
                }
            }
        }

        toast('Product Updated!', 'info');

        return redirect()->route('products.index');
    }


    public function destroy(Product $product) {
        abort_if(Gate::denies('delete_products'), 403);

        $product->delete();

        toast('Product Deleted!', 'warning');

        return redirect()->route('products.index');
    }
}
