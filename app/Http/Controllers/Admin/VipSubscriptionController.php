<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VipSubscription;
use App\Models\VipPlan;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Carbon\Carbon;

class VipSubscriptionController extends Controller
{
    /**
     * Display a listing of VIP subscriptions.
     */
    public function index(Request $request)
    {
        $query = VipSubscription::with(['vendor', 'vipPlan']);

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Plan filter
        if ($request->filled('plan_id')) {
            $query->where('vip_plan_id', $request->plan_id);
        }

        // Search by vendor
        if ($request->filled('search')) {
            $query->whereHas('vendor', function ($q) use ($request) {
                $q->where('business_name', 'like', '%' . $request->search . '%');
            });
        }

        $subscriptions = $query->orderBy('created_at', 'desc')
            ->paginate(20)
            ->appends($request->query());

        $plans = VipPlan::all();

        $stats = [
            'total' => VipSubscription::count(),
            'active' => VipSubscription::where('status', 'active')->count(),
            'expired' => VipSubscription::where('status', 'expired')->count(),
            'cancelled' => VipSubscription::where('status', 'cancelled')->count(),
        ];

        return view('admin.vip-subscriptions.index', compact('subscriptions', 'plans', 'stats'));
    }

    /**
     * Show the form for creating a new VIP subscription (admin-initiated).
     */
    public function create()
    {
        $vendors = Vendor::where('is_verified', true)
            ->orderBy('business_name')
            ->get();
        $plans = VipPlan::where('status', true)->get();

        return view('admin.vip-subscriptions.create', compact('vendors', 'plans'));
    }

    /**
     * Store a newly created VIP subscription (admin-initiated).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'vendor_id' => 'required|exists:vendors,id',
            'vip_plan_id' => 'required|exists:vip_plans,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'status' => 'required|in:active,expired,cancelled',
            'payment_ref' => 'nullable|string|max:255',
        ]);

        VipSubscription::create($validated);

        return redirect()->route('admin.vip-subscriptions.index')
            ->with('success', 'VIP subscription created successfully!');
    }

    /**
     * Display the specified VIP subscription.
     */
    public function show($id)
    {
        $subscription = VipSubscription::with(['vendor.user', 'vipPlan'])->findOrFail($id);

        return view('admin.vip-subscriptions.show', compact('subscription'));
    }

    /**
     * Show the form for editing the specified VIP subscription.
     */
    public function edit($id)
    {
        $subscription = VipSubscription::with(['vendor', 'vipPlan'])->findOrFail($id);
        $vendors = Vendor::where('is_verified', true)->orderBy('business_name')->get();
        $plans = VipPlan::where('status', true)->get();

        return view('admin.vip-subscriptions.edit', compact('subscription', 'vendors', 'plans'));
    }

    /**
     * Update the specified VIP subscription.
     */
    public function update(Request $request, $id)
    {
        $subscription = VipSubscription::findOrFail($id);

        $validated = $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'status' => 'required|in:active,expired,cancelled',
            'payment_ref' => 'nullable|string|max:255',
        ]);

        $subscription->update($validated);

        return redirect()->route('admin.vip-subscriptions.index')
            ->with('success', 'VIP subscription updated successfully!');
    }

    /**
     * Cancel a VIP subscription.
     */
    public function cancel($id)
    {
        $subscription = VipSubscription::findOrFail($id);

        if ($subscription->status === 'cancelled') {
            return back()->with('info', 'Subscription is already cancelled.');
        }

        $subscription->update(['status' => 'cancelled']);

        return back()->with('success', 'VIP subscription cancelled successfully.');
    }

    /**
     * Reactivate a cancelled VIP subscription.
     */
    public function reactivate($id)
    {
        $subscription = VipSubscription::findOrFail($id);

        if ($subscription->status === 'active') {
            return back()->with('info', 'Subscription is already active.');
        }

        $subscription->update(['status' => 'active']);

        return back()->with('success', 'VIP subscription reactivated successfully.');
    }

    /**
     * Extend VIP subscription duration.
     */
    public function extend(Request $request, $id)
    {
        $subscription = VipSubscription::findOrFail($id);

        $validated = $request->validate([
            'extend_days' => 'required|integer|min:1',
        ]);

        $newEndDate = Carbon::parse($subscription->end_date)->addDays($validated['extend_days']);

        $subscription->update([
            'end_date' => $newEndDate,
        ]);

        return back()->with('success', "VIP subscription extended by {$validated['extend_days']} days!");
    }

    /**
     * Remove the specified VIP subscription.
     */
    public function destroy($id)
    {
        $subscription = VipSubscription::findOrFail($id);

        // Only delete expired or cancelled subscriptions
        if ($subscription->status === 'active') {
            return back()->with('error', 'Cannot delete active subscriptions. Cancel it first.');
        }

        $subscription->delete();

        return redirect()->route('admin.vip-subscriptions.index')
            ->with('success', 'VIP subscription deleted successfully.');
    }

    /**
     * Export VIP subscriptions data to CSV.
     */
    public function export(Request $request)
    {
        $query = VipSubscription::with(['vendor', 'vipPlan']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $subscriptions = $query->orderBy('created_at', 'desc')->get();

        $filename = 'vip-subscriptions-' . date('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($subscriptions) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'Vendor', 'Plan', 'Status', 'Start Date', 'End Date', 'Payment Ref', 'Ads Used']);

            foreach ($subscriptions as $sub) {
                fputcsv($file, [
                    $sub->id,
                    $sub->vendor->business_name,
                    $sub->vipPlan->name,
                    ucfirst($sub->status),
                    $sub->start_date->format('Y-m-d'),
                    $sub->end_date->format('Y-m-d'),
                    $sub->payment_ref ?? 'N/A',
                    $sub->ads_used . ' / ' . $sub->vipPlan->free_ads,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}

