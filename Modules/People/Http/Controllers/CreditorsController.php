<?php

namespace Modules\People\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Modules\People\Entities\Creditor;

class CreditorsController extends Controller
{
    /** List all creditors (UI view) */
    public function index() {
        abort_if(Gate::denies('access_suppliers'), 403);
        $creditors = Creditor::orderByDesc('updated_at')->get();
        return view('people::creditors.index', compact('creditors'));
    }

    /** Show single creditor (UI view) */
    public function show(Creditor $creditor) {
        abort_if(Gate::denies('access_suppliers'), 403);
        return view('people::creditors.show', compact('creditor'));
    }

    /** Manually update creditor (e.g., set due_date) */
    public function update(Request $request, Creditor $creditor) {
        abort_if(Gate::denies('access_suppliers'), 403);
        $request->validate([
            'due_date' => 'nullable|date',
        ]);
        $creditor->update([
            'due_date' => $request->due_date,
        ]);
        toast('Creditor updated', 'info');
        return redirect()->route('creditors.show', $creditor);
    }

    /** Mark settled (delete) */
    public function destroy(Creditor $creditor) {
        abort_if(Gate::denies('access_suppliers'), 403);
        $creditor->delete();
        toast('Creditor settled', 'success');
        return redirect()->route('creditors.index');
    }
}
