<?php

namespace Modules\Expense\Http\Controllers;

use Modules\Expense\DataTables\ExpenseCategoriesDataTable;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;
use Modules\Expense\Entities\ExpenseCategory;

class ExpenseCategoriesController extends Controller
{

    public function index(ExpenseCategoriesDataTable $dataTable) {
        abort_if(Gate::denies('access_expense_categories'), 403);

        $stores = \App\Models\Store::where('is_active', true)->get();

        return $dataTable->render('expense::categories.index', compact('stores'));
    }

    public function store(Request $request) {
        abort_if(Gate::denies('access_expense_categories'), 403);

        $validated = $request->validate([
            'category_name' => ['required', 'string', 'max:255', \Illuminate\Validation\Rule::unique('expense_categories')->where(function ($query) use ($request) {
                return $query->where('store_id', $request->store_id);
            })],
            'category_description' => 'nullable|string|max:1000',
            'store_id'      => 'required|numeric'
        ]);

        $store = \App\Models\Store::find($request->store_id);

        ExpenseCategory::create([
            'category_name' => $request->category_name,
            'category_description' => $request->category_description,
            'store_id'      => $request->store_id,
            'business_id'   => $store->business_id
        ]);

        toast('Expense Category Created!', 'success');

        return redirect()->route('expense-categories.index');
    }


    public function edit(ExpenseCategory $expenseCategory) {
        abort_if(Gate::denies('access_expense_categories'), 403);

        $stores = \App\Models\Store::where('is_active', true)->get();

        return view('expense::categories.edit', compact('expenseCategory', 'stores'));
    }


    public function update(Request $request, ExpenseCategory $expenseCategory) {
        abort_if(Gate::denies('access_expense_categories'), 403);

        $request->validate([
            'category_name' => ['required', 'string', 'max:255', \Illuminate\Validation\Rule::unique('expense_categories')->ignore($expenseCategory->id)->where(function ($query) use ($request) {
                return $query->where('store_id', $request->store_id);
            })],
            'category_description' => 'nullable|string|max:1000',
            'store_id'      => 'required|numeric'
        ]);

        $expenseCategory->update([
            'category_name' => $request->category_name,
            'category_description' => $request->category_description,
            'store_id'      => $request->store_id,
        ]);

        toast('Expense Category Updated!', 'info');

        return redirect()->route('expense-categories.index');
    }


    public function destroy(ExpenseCategory $expenseCategory) {
        abort_if(Gate::denies('access_expense_categories'), 403);

        if ($expenseCategory->expenses()->isNotEmpty()) {
            return back()->withErrors('Can\'t delete beacuse there are expenses associated with this category.');
        }

        $expenseCategory->delete();

        toast('Expense Category Deleted!', 'warning');

        return redirect()->route('expense-categories.index');
    }
}
