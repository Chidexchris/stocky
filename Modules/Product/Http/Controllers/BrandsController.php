<?php

namespace Modules\Product\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Modules\Product\Entities\Brand;
use Modules\Product\DataTables\ProductBrandsDataTable;
use App\Models\Store;

class BrandsController extends Controller
{
    public function index(ProductBrandsDataTable $dataTable) {
        abort_if(Gate::denies('access_product_categories'), 403);

        $stores = Store::orderBy('name')->get();

        return $dataTable->render('product::brands.index', compact('stores'));
    }

    public function store(Request $request) {
        abort_if(Gate::denies('access_product_categories'), 403);

        $storeId = $request->store_id ?? auth()->user()->store_id;

        $request->validate([
            'brand_code' => 'required|unique:brands,brand_code,NULL,id,store_id,' . $storeId,
            'brand_name' => 'required',
            'store_id' => 'required'
        ]);

        $store = \App\Models\Store::when(!auth()->user()->hasRole('Super Admin'), function ($query) {
            return $query->where('business_id', auth()->user()->business_id);
        })->findOrFail($request->store_id);

        Brand::create([
            'brand_code' => $request->brand_code,
            'brand_name' => $request->brand_name,
            'store_id' => $store->id,
            'business_id' => $store->business_id, // Ensure business_id is set
        ]);

        toast('Product Brand Created!', 'success');

        return redirect()->back();
    }

    public function edit(Brand $product_brand) {
        abort_if(Gate::denies('access_product_categories'), 403);

        return view('product::brands.edit', ['brand' => $product_brand]);
    }

    public function update(Request $request, Brand $product_brand) {
        abort_if(Gate::denies('access_product_categories'), 403);

        $storeId = $request->store_id ?? $product_brand->store_id;

        $request->validate([
            'brand_code' => 'required|unique:brands,brand_code,' . $product_brand->id . ',id,store_id,' . $storeId,
            'brand_name' => 'required',
            'store_id' => 'required'
        ]);

        $product_brand->update([
            'brand_code' => $request->brand_code,
            'brand_name' => $request->brand_name,
            'store_id' => $request->store_id,
        ]);

        toast('Product Brand Updated!', 'success');

        return redirect()->route('product-brands.index');
    }

    public function destroy(Brand $product_brand) {
        abort_if(Gate::denies('access_product_categories'), 403);

        $product_brand->delete();

        toast('Product Brand Deleted!', 'success');

        return redirect()->back();
    }

    public function show(Brand $product_brand) {
        abort_if(Gate::denies('access_product_categories'), 403);

        $product_brand->load(['products']);

        return view('product::brands.show', ['brand' => $product_brand, 'products' => $product_brand->products]);
    }
}
