<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\AdminLog;
use Illuminate\Http\Request;

class PlansController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $plans = Plan::withCount('businesses')->get();
        return view('superadmin.plans.index', compact('plans'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('superadmin.plans.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'price_annual' => 'required|numeric|min:0',
            'limit_stores' => 'required|integer|min:1',
            'limit_users' => 'required|integer|min:1',
            'limit_storage' => 'required|integer|min:1',
            'limit_currencies' => 'required|integer|min:1',
            'description' => 'nullable|string',
            'features' => 'nullable|string',
        ]);

        $features = $request->features 
            ? array_filter(array_map('trim', explode("\n", $request->features))) 
            : [];

        $plan = Plan::create([
            'name' => $request->name,
            'price' => $request->price * 100, // Store in cents
            'price_annual' => $request->price_annual * 100, // Store in cents
            'limit_stores' => $request->limit_stores,
            'limit_users' => $request->limit_users,
            'limit_storage' => $request->limit_storage,
            'limit_currencies' => $request->limit_currencies,
            'description' => $request->description,
            'features' => $features,
        ]);

        AdminLog::create([
            'user_id' => auth()->id(),
            'action' => 'create_plan',
            'target_id' => $plan->id,
            'details' => ['name' => $plan->name],
            'ip_address' => request()->ip()
        ]);

        return redirect()->route('saas.plans.index')->with('success', 'Plan created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $plan = Plan::findOrFail($id);
        return view('superadmin.plans.edit', compact('plan'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'price_annual' => 'required|numeric|min:0',
            'limit_stores' => 'required|integer|min:1',
            'limit_users' => 'required|integer|min:1',
            'limit_storage' => 'required|integer|min:1',
            'limit_currencies' => 'required|integer|min:1',
            'description' => 'nullable|string',
            'features' => 'nullable|string',
        ]);

        $features = $request->features 
            ? array_filter(array_map('trim', explode("\n", $request->features))) 
            : [];

        $plan = Plan::findOrFail($id);
        $plan->update([
            'name' => $request->name,
            'price' => $request->price * 100, // Store in cents
            'price_annual' => $request->price_annual * 100, // Store in cents
            'limit_stores' => $request->limit_stores,
            'limit_users' => $request->limit_users,
            'limit_storage' => $request->limit_storage,
            'limit_currencies' => $request->limit_currencies,
            'description' => $request->description,
            'features' => $features,
        ]);

        AdminLog::create([
            'user_id' => auth()->id(),
            'action' => 'update_plan',
            'target_id' => $plan->id,
            'details' => ['name' => $plan->name],
            'ip_address' => request()->ip()
        ]);

        return redirect()->route('saas.plans.index')->with('success', 'Plan updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $plan = Plan::findOrFail($id);

        if ($plan->businesses()->exists()) {
            return back()->with('error', 'Cannot delete plan as it is currently assigned to active businesses. Archive it instead (coming soon).');
        }

        $planId = $plan->id;
        $planName = $plan->name;
        $plan->delete();

        AdminLog::create([
            'user_id' => auth()->id(),
            'action' => 'delete_plan',
            'target_id' => $planId,
            'details' => ['name' => $planName],
            'ip_address' => request()->ip()
        ]);

        return redirect()->route('saas.plans.index')->with('success', 'Plan deleted permanently.');
    }
}
