<?php

namespace Modules\People\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Modules\People\Entities\Debtor;
use Modules\Sale\Entities\Sale;
use Modules\PurchasesReturn\Entities\PurchaseReturn;
use App\Models\Store;

class DebtorsController extends Controller
{
    /** List all debtors (customers/suppliers who owe us) */
    public function index(Request $request) {
        abort_if(Gate::denies('access_customers'), 403);

        $sales = collect();
        $purchaseReturns = collect();

        if (!$request->filled('type') || $request->type == 'customer') {
            $sales = Sale::where('due_amount', '>', 0)
                ->when($request->filled('store_id'), function ($query) use ($request) {
                    $query->where('store_id', $request->store_id);
                })
                ->orderByDesc('updated_at')
                ->get();
        }

        if (!$request->filled('type') || $request->type == 'supplier') {
            $purchaseReturns = PurchaseReturn::where('due_amount', '>', 0)
                ->when($request->filled('store_id'), function ($query) use ($request) {
                    $query->where('store_id', $request->store_id);
                })
                ->orderByDesc('updated_at')
                ->get();
        }

        $debtors = $sales->concat($purchaseReturns);

        $stores = Store::all();
            
        return view('people::debtors.index', compact('debtors', 'stores'));
    }

    /** Show single debtor order details */
    public function show($id) {
        abort_if(Gate::denies('access_customers'), 403);
        
        $sale = Sale::findOrFail($id);
        $customer = $sale->customer;
        
        return view('people::debtors.show', compact('sale', 'customer'));
    }

    /** Manually update debtor (e.g., set due_date) */
    public function update(Request $request, Debtor $debtor) {
        abort_if(Gate::denies('access_customers'), 403);
        $request->validate([
            'due_date' => 'nullable|date',
        ]);
        $debtor->update([
            'due_date' => $request->due_date,
        ]);
        toast('Debtor updated', 'info');
        return redirect()->route('debtors.show', $debtor);
    }

    /** Mark settled (delete) */
    public function destroy(Debtor $debtor) {
        abort_if(Gate::denies('access_customers'), 403);
        $debtor->delete();
        toast('Debtor settled', 'success');
        return redirect()->route('debtors.index');
    }
}
