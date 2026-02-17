<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\WithdrawalRequest;
use Illuminate\Http\Request;

class WithdrawalController extends Controller
{
    public function index(Request $request)
    {
        $query = WithdrawalRequest::with('affiliate.user')->latest();

        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $withdrawals = $query->paginate(20);
        return view('superadmin.withdrawals.index', compact('withdrawals'));
    }

    public function update(Request $request, WithdrawalRequest $withdrawal)
    {
        $request->validate([
            'status' => 'required|in:pending,completed,rejected',
        ]);

        $withdrawal->update(['status' => $request->status]);

        return back()->with('success', 'Withdrawal status updated successfully.');
    }
}
