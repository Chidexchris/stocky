<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubscriptionController extends Controller
{
    /**
     * Display the pricing table.
     */
    public function pricing()
    {
        return view('pricing');
    }

    /**
     * Handle plan selection for a business.
     */
    public function selectPlan(Request $request, Plan $plan)
    {
        $user = Auth::user();
        $business = $user->business;

        if (!$business) {
            abort(403, 'Business context not found.');
        }

        // Update business plan
        $business->update([
            'plan_id' => $plan->id,
            'trial_ends_at' => null, // End trial mode
        ]);

        return redirect()->route('home')->with('success', "Plan '{$plan->name}' successfully initialized for your warehouse.");
    }
}
