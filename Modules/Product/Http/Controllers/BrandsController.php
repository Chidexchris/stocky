<?php

namespace Modules\Product\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Modules\Product\Entities\Brand;
use Modules\Product\DataTables\ProductBrandsDataTable;

class BrandsController extends Controller
{
    public function index(ProductBrandsDataTable $dataTable) {
        abort_if(Gate::denies('access_product_categories'), 403);

        return $dataTable->render('product::brands.index');
    }

    public function store(Request $request) {
        abort_if(Gate::denies('access_product_categories'), 403);

        $request->validate([
            'brand_code' => 'required|unique:brands,brand_code',
            'brand_name' => 'required'
        ]);

        Brand::create([
            'brand_code' => $request->brand_code,
            'brand_name' => $request->brand_name,
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

        $request->validate([
            'brand_code' => 'required|unique:brands,brand_code,' . $product_brand->id,
            'brand_name' => 'required'
        ]);

        $product_brand->update([
            'brand_code' => $request->brand_code,
            'brand_name' => $request->brand_name,
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
