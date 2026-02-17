<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckSubscription
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $limit = null, $featureName = null): Response
    {
        if (!Auth::check()) {
            return $next($request);
        }

        // Exclude pricing routes from trial/plan gating to avoid circular redirects
        if ($request->routeIs('saas.pricing') || $request->routeIs('saas.pricing.select')) {
            return $next($request);
        }

        $user = Auth::user();

        // Super Admin bypass
        if ($user->hasRole('Super Admin')) {
            return $next($request);
        }

        $business = $user->business;

        if (!$business) {
            abort(403, 'Business context not found.');
        }

        // Check if business is active
        if (!$business->is_active) {
            return $this->deny($request, 'Your business account is suspended. Please contact support.');
        }

        $plan = $business->plan;

        // Check trial expiration if no plan is selected
        if (!$plan && $business->trial_ends_at) {
            if (now()->greaterThan($business->trial_ends_at)) {
                return response()->view('trial-expired', [
                    'business' => $business
                ], 403);
            }
        }

        if (!$plan) {
            return $next($request); // Should ideally select a plan first
        }

        // Check plan limits if specified
        if ($limit === 'store_limit') {
            if ($business->stores()->count() >= $plan->limit_stores) {
                return $this->deny($request, 'Store limit reached for your current plan. Please upgrade.');
            }
        }

        if ($limit === 'user_limit') {
            if ($business->users()->count() >= $plan->limit_users) {
                return $this->deny($request, 'User limit reached for your current plan. Please upgrade.');
            }
        }

        if ($limit === 'storage_limit') {
            if ($business->storageLimitReached()) {
                return $this->denyUpgrade($request, "Storage (Limit: {$plan->limit_storage}GB)", $plan);
            }
        }

        // Check feature access
        // Usage: middleware('subscribed:feature,suppliers')
        if ($limit === 'feature' && $featureName) {
            // Use the User model's hasFeature which handles plan inheritance
            if (!$user->hasFeature($featureName)) {
                $featureMap = [
                    'suppliers' => 'Supplier Management',
                    'debtors'    => 'Customer Debt Tracking',
                    'transfers'  => 'Inter-store Transfers',
                    'login_logs' => 'Login Logs Tracking',
                    'barcode_printing' => 'Barcode Printing',
                    'expenses'   => 'Expense Management',
                    'reports'    => 'Advanced Reports',
                ];
                $readableName = $featureMap[$featureName] ?? $featureName;
                return $this->denyUpgrade($request, $readableName, $plan);
            }
        }

        return $next($request);
    }

    protected function deny(Request $request, string $message): Response
    {
        if ($request->expectsJson()) {
            return response()->json(['message' => $message], 403);
        }

        return redirect()->back()->with('error', $message);
    }

    protected function denyUpgrade(Request $request, string $featureName, $plan): Response
    {
        if ($request->expectsJson()) {
            return response()->json([
                'message' => "Your plan ({$plan->name}) does not support {$featureName}. Please upgrade.",
                'upgrade_required' => true,
            ], 403);
        }

        // Determine recommended plan based on current plan
        $recommendedPlan = 'Business';
        if ($plan->name === 'Business') {
            $recommendedPlan = 'Enterprise';
        }

        return response()->view('upgrade-required', [
            'featureName' => $featureName,
            'currentPlan' => $plan->name,
            'recommendedPlan' => $recommendedPlan,
        ], 403);
    }
}
