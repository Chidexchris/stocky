<?php

namespace Modules\People\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Modules\Purchase\Entities\Purchase;
use Modules\SalesReturn\Entities\SaleReturn;
use App\Models\Store;

class CreditorsController extends Controller
{
    public function index(Request $request) {
        abort_if(Gate::denies('access_suppliers'), 403);

        $purchases = collect();
        $saleReturns = collect();

        if (!$request->filled('type') || $request->type == 'supplier') {
            $purchases = Purchase::where('due_amount', '>', 0)
                ->when($request->filled('store_id'), function ($query) use ($request) {
                    $query->where('store_id', $request->store_id);
                })
                ->orderByDesc('updated_at')
                ->get();
        }

        if (!$request->filled('type') || $request->type == 'customer') {
            $saleReturns = SaleReturn::where('due_amount', '>', 0)
                ->when($request->filled('store_id'), function ($query) use ($request) {
                    $query->where('store_id', $request->store_id);
                })
                ->orderByDesc('updated_at')
                ->get();
        }

        $creditors = $purchases->concat($saleReturns);
        $stores = Store::all();

        return view('people::creditors.index', compact('creditors', 'stores'));
    }

    public function show($id) {
        abort_if(Gate::denies('access_suppliers'), 403);
        
        $purchase = Purchase::findOrFail($id);
        $supplier = $purchase->supplier;
        
        return view('people::creditors.show', compact('purchase', 'supplier'));
    }
}
