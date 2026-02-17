<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\Plan;
use App\Models\User;
use App\Models\Subscription;
use App\Models\AdminLog;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class SuperAdminController extends Controller
{
    public function dashboard()
    {
        // Revenue Analytics
        $activeSubscriptions = Subscription::where('status', 'active')->with('plan')->get();
        $mrr = $activeSubscriptions->sum(function ($sub) {
            return $sub->plan ? $sub->plan->price : 0;
        });
        $arr = $mrr * 12;

        // Signup Growth
        $currentMonthSignups = Business::where('created_at', '>=', Carbon::now()->subDays(30))->count();
        $previousMonthSignups = Business::where('created_at', '>=', Carbon::now()->subDays(60))
            ->where('created_at', '<', Carbon::now()->subDays(30))
            ->count();
        
        $growth = 0;
        if ($previousMonthSignups > 0) {
            $growth = (($currentMonthSignups - $previousMonthSignups) / $previousMonthSignups) * 100;
        }

        // Signup Chart Data (Last 7 Days)
        $signupData = [];
        $signupLabels = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $signupLabels[] = $date->format('D, d M');
            $signupData[] = Business::whereDate('created_at', $date->toDateString())->count();
        }

        $stats = [
            'total_businesses' => Business::count(),
            'active_businesses' => Business::where('is_active', true)->count(),
            'total_users' => User::count(),
            'total_plans' => Plan::count(),
            'mrr' => $mrr / 100, // Convert cents to main currency
            'arr' => $arr / 100,
            'growth' => round($growth, 1),
            'signup_chart' => [
                'labels' => $signupLabels,
                'data' => $signupData
            ]
        ];

        return view('superadmin.dashboard', compact('stats'));
    }

    public function businesses()
    {
        $businesses = Business::with('plan')->get();
        return view('superadmin.businesses.index', compact('businesses'));
    }

    public function audit()
    {
        $logs = AdminLog::with('user')->latest()->paginate(25);
        return view('superadmin.audit.index', compact('logs'));
    }

    public function toggleBusinessStatus(Business $business)
    {
        $business->update(['is_active' => !$business->is_active]);
        
        $this->logAdminAction('toggle_business_status', $business->id, [
            'name' => $business->name,
            'active' => $business->is_active
        ]);

        return back()->with('success', 'Business status updated.');
    }

    public function changePlan(Request $request, Business $business)
    {
        $request->validate(['plan_id' => 'required|exists:plans,id']);
        $oldPlanId = $business->plan_id;
        $business->update(['plan_id' => $request->plan_id, 'trial_ends_at' => null]);

        $this->logAdminAction('change_business_plan', $business->id, [
            'old_plan' => $oldPlanId,
            'new_plan' => $request->plan_id
        ]);

        return back()->with('success', 'Business plan updated and trial ended.');
    }

    public function updateVerificationStatus(Request $request, Business $business)
    {
        $request->validate(['status' => 'required|in:pending,verified,suspended']);
        $oldStatus = $business->verification_status;
        $business->update(['verification_status' => $request->status]);

        $this->logAdminAction('update_verification_status', $business->id, [
            'old_status' => $oldStatus,
            'new_status' => $request->status
        ]);

        return back()->with('success', 'Business verification status updated to ' . ucfirst($request->status));
    }

    public function impersonate(Business $business)
    {
        $user = $business->users()->whereHas('roles', function($q) {
            $q->where('name', 'Business Owner');
        })->first();

        if (!$user) {
            return back()->with('error', 'No Business Owner found for this business.');
        }

        session(['original_superadmin_id' => auth()->id()]);
        
        $this->logAdminAction('impersonate_start', $business->id, [
            'business_name' => $business->name,
            'user_email' => $user->email
        ]);

        auth()->login($user);

        return redirect()->route('home')->with('success', 'Now impersonating ' . $business->name);
    }

    public function stopImpersonate()
    {
        $originalId = session('original_superadmin_id');
        if (!$originalId) {
            return redirect()->route('home');
        }

        session()->forget('original_superadmin_id');
        auth()->loginUsingId($originalId);

        return redirect()->route('saas.dashboard')->with('success', 'Returned to Master Dashboard');
    }

    public function cancelTrial(Business $business)
    {
        $business->update(['trial_ends_at' => now()]);

        $this->logAdminAction('terminate_trial', $business->id, [
            'business_name' => $business->name
        ]);

        return back()->with('success', 'Business trial has been terminated.');
    }

    public function users(Request $request)
    {
        $query = User::with('business');

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%")
                  ->orWhereHas('business', function($sq) use ($search) {
                      $sq->where('name', 'LIKE', "%{$search}%");
                  });
            });
        }

        $users = $query->paginate(20);
        return view('superadmin.users.index', compact('users'));
    }

    public function plans()
    {
        $plans = Plan::all();
        return view('superadmin.plans.index', compact('plans'));
    }

    public function editFeatures(Business $business)
    {
        // Define all possible platform features
        $availableFeatures = [
            'Supplier Management',
            'Customer Debt Tracking',
            'Expiry Date Alerts',
            'Login Logs Tracking',
            'Barcode Printing',
            'Expense Management',
            'Inter-store Transfers',
            'Advanced Reports',
            'Beta: AI Assistant',
            'Beta: Advanced Analytics',
            'Beta: WhatsApp Automation'
        ];

        return view('superadmin.businesses.features', compact('business', 'availableFeatures'));
    }

    public function updateFeatures(Request $request, Business $business)
    {
        $business->update([
            'feature_overrides' => $request->features ?? []
        ]);

        $this->logAdminAction('update_feature_overrides', $business->id, [
            'features' => $request->features ?? []
        ]);

        return back()->with('success', 'Feature overrides updated for ' . $business->name);
    }

    private function logAdminAction($action, $targetId = null, $details = [])
    {
        AdminLog::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'target_id' => $targetId,
            'details' => $details,
            'ip_address' => request()->ip()
        ]);
    }
}
