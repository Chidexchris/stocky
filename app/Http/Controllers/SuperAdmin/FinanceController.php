<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Coupon;
use Illuminate\Http\Request;

class FinanceController extends Controller
{
    public function transactions()
    {
        $transactions = Transaction::with(['business', 'plan'])->latest()->paginate(20);
        return view('superadmin.finance.transactions', compact('transactions'));
    }

    public function coupons()
    {
        $coupons = Coupon::latest()->get();
        return view('superadmin.finance.coupons', compact('coupons'));
    }

    public function storeCoupon(Request $request)
    {
        $request->validate([
            'code' => 'required|string|unique:coupons,code',
            'type' => 'required|in:fixed,percentage',
            'value' => 'required|integer|min:1',
            'expires_at' => 'nullable|date',
            'usage_limit' => 'nullable|integer|min:1',
        ]);

        Coupon::create($request->all());

        return back()->with('success', 'Coupon created successfully.');
    }

    public function destroyCoupon(Coupon $coupon)
    {
        $coupon->delete();
        return back()->with('success', 'Coupon deleted successfully.');
    }

    public function refund(Transaction $transaction)
    {
        $transaction->update(['status' => 'refunded']);
        return back()->with('success', 'Transaction marked as refunded.');
    }
}
