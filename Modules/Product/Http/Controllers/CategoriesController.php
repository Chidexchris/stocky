<?php

namespace Modules\Product\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;
use Modules\Product\Entities\Category;
use Modules\Product\DataTables\ProductCategoriesDataTable;

class CategoriesController extends Controller
{

    public function index(ProductCategoriesDataTable $dataTable) {
        abort_if(Gate::denies('access_product_categories'), 403);

        $stores = \App\Models\Store::where('is_active', true)->get();

        return $dataTable->render('product::categories.index', compact('stores'));
    }


    public function store(Request $request) {
        abort_if(Gate::denies('access_product_categories'), 403);

        $validated = $request->validate([
            'category_code' => ['required', \Illuminate\Validation\Rule::unique('categories')->where(function ($query) use ($request) {
                return $query->where('store_id', $request->store_id);
            })],
            'category_name' => 'required',
            'store_id'      => 'required|numeric'
        ]);

        $store = \App\Models\Store::when(!auth()->user()->hasRole('Super Admin'), function ($query) {
            return $query->where('business_id', auth()->user()->business_id);
        })->findOrFail($request->store_id);

        Category::create([
            'category_code' => $request->category_code,
            'category_name' => $request->category_name,
            'store_id'      => $store->id,
            'business_id'   => $store->business_id
        ]);

        toast('Product Category Created!', 'success');

        return redirect()->back();
    }


    public function edit($id) {
        abort_if(Gate::denies('access_product_categories'), 403);

        $category = Category::findOrFail($id);
        $stores = \App\Models\Store::where('is_active', true)->get();

        return view('product::categories.edit', compact('category', 'stores'));
    }


    public function update(Request $request, $id) {
        abort_if(Gate::denies('access_product_categories'), 403);

        $request->validate([
            'category_code' => ['required', \Illuminate\Validation\Rule::unique('categories')->ignore($id)->where(function ($query) use ($request) {
                return $query->where('store_id', $request->store_id);
            })],
            'category_name' => 'required',
            'store_id'      => 'required|numeric'
        ]);

        Category::findOrFail($id)->update([
            'category_code' => $request->category_code,
            'category_name' => $request->category_name,
            'store_id'      => $request->store_id,
        ]);

        toast('Product Category Updated!', 'info');

        return redirect()->route('product-categories.index');
    }


    public function destroy($id) {
        abort_if(Gate::denies('access_product_categories'), 403);

        $category = Category::findOrFail($id);

        if ($category->products()->exists()) {
            return back()->withErrors('Can\'t delete because there are products associated with this category.');
        }

        $category->delete();

        toast('Product Category Deleted!', 'warning');

        return redirect()->route('product-categories.index');
    }
}
