<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Models\AdminLog;
use Illuminate\Http\Request;
use Carbon\Carbon;

class SubscriptionsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $subscriptions = Subscription::with(['business', 'plan'])->latest()->paginate(20);
        return view('superadmin.subscriptions.index', compact('subscriptions'));
    }

    /**
     * Cancel the specified subscription.
     */
    public function cancel(Subscription $subscription)
    {
        $subscription->update(['status' => 'canceled']);
        
        AdminLog::create([
            'user_id' => auth()->id(),
            'action' => 'cancel_subscription',
            'target_id' => $subscription->business_id,
            'details' => [
                'subscription_id' => $subscription->id,
                'business_name' => $subscription->business->name
            ],
            'ip_address' => request()->ip()
        ]);

        return back()->with('success', 'Subscription for ' . $subscription->business->name . ' has been canceled.');
    }

    /**
     * Activate/Resume the specified subscription.
     */
    public function activate(Subscription $subscription)
    {
        $subscription->update(['status' => 'active']);

        AdminLog::create([
            'user_id' => auth()->id(),
            'action' => 'activate_subscription',
            'target_id' => $subscription->business_id,
            'details' => [
                'subscription_id' => $subscription->id,
                'business_name' => $subscription->business->name
            ],
            'ip_address' => request()->ip()
        ]);

        return back()->with('success', 'Subscription for ' . $subscription->business->name . ' is now active.');
    }

    /**
     * Extend the trial period for the specified subscription.
     */
    public function extendTrial(Request $request, Subscription $subscription)
    {
        $request->validate([
            'days' => 'required|integer|min:1|max:90'
        ]);

        $currentEnd = $subscription->ends_at ?? Carbon::now();
        $newEnd = $currentEnd->addDays($request->days);

        $subscription->update([
            'ends_at' => $newEnd,
            'status' => 'trial' // Ensure it's in trial status if extending trial
        ]);

        AdminLog::create([
            'user_id' => auth()->id(),
            'action' => 'extend_trial',
            'target_id' => $subscription->business_id,
            'details' => [
                'subscription_id' => $subscription->id,
                'business_name' => $subscription->business->name,
                'days_added' => $request->days
            ],
            'ip_address' => request()->ip()
        ]);

        return back()->with('success', 'Trial for ' . $subscription->business->name . ' extended by ' . $request->days . ' days.');
    }
}
