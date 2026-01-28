<?php

namespace Modules\Purchase\Http\Controllers;

use Modules\Purchase\DataTables\PurchasePaymentsDataTable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Modules\Purchase\Entities\Purchase;
use Modules\Purchase\Entities\PurchasePayment;
use Modules\People\Entities\Creditor;

class PurchasePaymentsController extends Controller
{

    public function index($purchase_id, PurchasePaymentsDataTable $dataTable) {
        abort_if(Gate::denies('access_purchase_payments'), 403);

        $purchase = Purchase::findOrFail($purchase_id);

        return $dataTable->render('purchase::payments.index', compact('purchase'));
    }


    public function create($purchase_id) {
        abort_if(Gate::denies('access_purchase_payments'), 403);

        $purchase = Purchase::findOrFail($purchase_id);

        return view('purchase::payments.create', compact('purchase'));
    }


    public function store(Request $request) {
        abort_if(Gate::denies('access_purchase_payments'), 403);

        $request->validate([
            'date' => 'required|date',
            'reference' => 'required|string|max:255',
            'amount' => 'required|numeric',
            'note' => 'nullable|string|max:1000',
            'purchase_id' => 'required',
            'payment_method' => 'required|string|max:255'
        ]);

        DB::transaction(function () use ($request) {
            PurchasePayment::create([
                'date' => $request->date,
                'reference' => $request->reference,
                'amount' => $request->amount,
                'note' => $request->note,
                'purchase_id' => $request->purchase_id,
                'payment_method' => $request->payment_method
            ]);

            $purchase = Purchase::findOrFail($request->purchase_id);

            $due_amount = $purchase->due_amount - $request->amount;

            if ($due_amount == $purchase->total_amount) {
                $payment_status = 'Unpaid';
            } elseif ($due_amount > 0) {
                $payment_status = 'Partial';
            } else {
                $payment_status = 'Paid';
            }

            $purchase->update([
                'paid_amount' => ($purchase->paid_amount + $request->amount) * 100,
                'due_amount' => $due_amount * 100,
                'payment_status' => $payment_status
            ]);

            /** Reduce creditor balance and settle when zero */
            $supplier = Supplier::findOrFail($purchase->supplier_id);
            $creditor = Creditor::firstOrCreate(
                ['name' => $supplier->supplier_name, 'email' => $supplier->supplier_email],
                ['amount_owed' => 0, 'due_date' => now()->addDays(30)]
            );
            $creditor->adjustBalance((int) round(-$request->amount * 100));
        });

        toast('Purchase Payment Created!', 'success');

        return redirect()->route('purchases.index');
    }


    public function edit($purchase_id, PurchasePayment $purchasePayment) {
        abort_if(Gate::denies('access_purchase_payments'), 403);

        $purchase = Purchase::findOrFail($purchase_id);

        return view('purchase::payments.edit', compact('purchasePayment', 'purchase'));
    }


    public function update(Request $request, PurchasePayment $purchasePayment) {
        abort_if(Gate::denies('access_purchase_payments'), 403);

        $request->validate([
            'date' => 'required|date',
            'reference' => 'required|string|max:255',
            'amount' => 'required|numeric',
            'note' => 'nullable|string|max:1000',
            'purchase_id' => 'required',
            'payment_method' => 'required|string|max:255'
        ]);

        DB::transaction(function () use ($request, $purchasePayment) {
            $purchase = $purchasePayment->purchase;

            $due_amount = ($purchase->due_amount + $purchasePayment->amount) - $request->amount;

            if ($due_amount == $purchase->total_amount) {
                $payment_status = 'Unpaid';
            } elseif ($due_amount > 0) {
                $payment_status = 'Partial';
            } else {
                $payment_status = 'Paid';
            }

            $purchase->update([
                'paid_amount' => (($purchase->paid_amount - $purchasePayment->amount) + $request->amount) * 100,
                'due_amount' => $due_amount * 100,
                'payment_status' => $payment_status
            ]);

            $purchasePayment->update([
                'date' => $request->date,
                'reference' => $request->reference,
                'amount' => $request->amount,
                'note' => $request->note,
                'purchase_id' => $request->purchase_id,
                'payment_method' => $request->payment_method
            ]);

            /** Adjust creditor delta on payment edit */
            $supplier = Supplier::findOrFail($purchase->supplier_id);
            $creditor = Creditor::firstOrCreate(
                ['name' => $supplier->supplier_name, 'email' => $supplier->supplier_email],
                ['amount_owed' => 0, 'due_date' => now()->addDays(30)]
            );
            $deltaCents = (int) round(($purchasePayment->amount - $request->amount) * 100);
            if ($deltaCents !== 0) {
                $creditor->adjustBalance($deltaCents);
            }
        });

        toast('Purchase Payment Updated!', 'info');

        return redirect()->route('purchases.index');
    }


    public function destroy(PurchasePayment $purchasePayment) {
        abort_if(Gate::denies('access_purchase_payments'), 403);

        $purchasePayment->delete();

        toast('Purchase Payment Deleted!', 'warning');

        return redirect()->route('purchases.index');
    }
}
