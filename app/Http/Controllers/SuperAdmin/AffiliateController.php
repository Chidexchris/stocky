<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Affiliate;
use App\Models\Referral;
use Illuminate\Http\Request;

class AffiliateController extends Controller
{
    public function index()
    {
        $affiliates = Affiliate::with('user')->latest()->paginate(20);
        return view('superadmin.affiliates.index', compact('affiliates'));
    }

    public function show(Affiliate $affiliate)
    {
        $referrals = $affiliate->referrals()->with('referredUser')->latest()->paginate(25);
        return view('superadmin.affiliates.show', compact('affiliate', 'referrals'));
    }

    public function update(Request $request, Affiliate $affiliate)
    {
        $request->validate([
            'balance' => 'required|numeric|min:0',
        ]);

        $affiliate->update([
            'balance' => $request->balance,
        ]);

        return back()->with('success', 'Affiliate balance updated successfully.');
    }

    public function toggleStatus(Affiliate $affiliate)
    {
        $newStatus = $affiliate->status === 'active' ? 'banned' : 'active';
        $affiliate->update(['status' => $newStatus]);

        $message = $newStatus === 'banned' ? 'Partner has been suspended.' : 'Partner has been reactivated.';
        return back()->with('success', $message);
    }

    public function destroy(Affiliate $affiliate)
    {
        // We might want to just remove the affiliate status/role rather than deleting the user
        $user = $affiliate->user;
        $user->removeRole('Affiliate');
        $affiliate->delete();

        return redirect()->route('saas.affiliates.index')->with('success', 'Affiliate record removed successfully.');
    }
}
