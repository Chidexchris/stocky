<?php

namespace Modules\People\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Modules\People\Entities\Debtor;

class DebtorsController extends Controller
{
    /** List all debtors (UI view) */
    public function index() {
        abort_if(Gate::denies('access_customers'), 403);
        $debtors = Debtor::orderByDesc('updated_at')->get();
        return view('people::debtors.index', compact('debtors'));
    }

    /** Show single debtor (UI view) */
    public function show(Debtor $debtor) {
        abort_if(Gate::denies('access_customers'), 403);
        return view('people::debtors.show', compact('debtor'));
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
