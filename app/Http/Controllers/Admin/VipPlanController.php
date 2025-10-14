<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VipPlan;
use Illuminate\Http\Request;

class VipPlanController extends Controller
{
    /**
     * Display a listing of VIP plans.
     */
    public function index()
    {
        $plans = VipPlan::withCount('subscriptions')
            ->orderBy('price', 'asc')
            ->get();

        $stats = [
            'total_plans' => VipPlan::count(),
            'active_plans' => VipPlan::where('status', true)->count(),
            'total_subscriptions' => \App\Models\VipSubscription::count(),
            'active_subscriptions' => \App\Models\VipSubscription::where('status', 'active')->count(),
            'revenue' => VipPlan::join('vip_subscriptions', 'vip_plans.id', '=', 'vip_subscriptions.vip_plan_id')
                ->whereIn('vip_subscriptions.status', ['active', 'expired'])
                ->sum('vip_plans.price'),
        ];

        return view('admin.vip-plans.index', compact('plans', 'stats'));
    }

    /**
     * Show the form for creating a new VIP plan.
     */
    public function create()
    {
        return view('admin.vip-plans.create');
    }

    /**
     * Store a newly created VIP plan.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'duration_days' => 'required|integer|min:1',
            'image_limit' => 'required|integer|min:0',
            'free_ads' => 'required|integer|min:0',
            'priority_level' => 'required|integer|min:1|max:10',
            'description' => 'nullable|string',
            'status' => 'required|boolean',
        ]);

        VipPlan::create($validated);

        return redirect()->route('admin.vip-plans.index')
            ->with('success', 'VIP plan created successfully!');
    }

    /**
     * Display the specified VIP plan.
     */
    public function show($id)
    {
        $plan = VipPlan::withCount('subscriptions')->findOrFail($id);
        $subscriptions = $plan->subscriptions()
            ->with('vendor')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.vip-plans.show', compact('plan', 'subscriptions'));
    }

    /**
     * Show the form for editing the specified VIP plan.
     */
    public function edit($id)
    {
        $plan = VipPlan::findOrFail($id);

        return view('admin.vip-plans.edit', compact('plan'));
    }

    /**
     * Update the specified VIP plan.
     */
    public function update(Request $request, $id)
    {
        $plan = VipPlan::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'duration_days' => 'required|integer|min:1',
            'image_limit' => 'required|integer|min:0',
            'free_ads' => 'required|integer|min:0',
            'priority_level' => 'required|integer|min:1|max:10',
            'description' => 'nullable|string',
            'status' => 'required|boolean',
        ]);

        $plan->update($validated);

        return redirect()->route('admin.vip-plans.index')
            ->with('success', 'VIP plan updated successfully!');
    }

    /**
     * Toggle VIP plan status.
     */
    public function toggleStatus($id)
    {
        $plan = VipPlan::findOrFail($id);
        $plan->update(['status' => !$plan->status]);

        $status = $plan->status ? 'activated' : 'deactivated';

        return back()->with('success', "VIP plan {$status} successfully!");
    }

    /**
     * Remove the specified VIP plan.
     */
    public function destroy($id)
    {
        $plan = VipPlan::findOrFail($id);

        // Check if plan has active subscriptions
        $activeSubscriptions = $plan->subscriptions()->where('status', 'active')->count();

        if ($activeSubscriptions > 0) {
            return back()->with('error', 'Cannot delete plan with active subscriptions. Deactivate it instead.');
        }

        $plan->delete();

        return redirect()->route('admin.vip-plans.index')
            ->with('success', 'VIP plan deleted successfully.');
    }
}

