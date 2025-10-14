<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FeaturedAd;
use App\Models\Vendor;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FeaturedAdController extends Controller
{
    /**
     * Display a listing of all featured ads.
     */
    public function index(Request $request)
    {
        $query = FeaturedAd::with(['vendor', 'service']);

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Placement filter
        if ($request->filled('placement')) {
            $query->where('placement', $request->placement);
        }

        // Search by vendor or service
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->whereHas('vendor', function ($vendorQuery) use ($request) {
                    $vendorQuery->where('business_name', 'like', '%' . $request->search . '%');
                })
                ->orWhereHas('service', function ($serviceQuery) use ($request) {
                    $serviceQuery->where('title', 'like', '%' . $request->search . '%');
                })
                ->orWhere('headline', 'like', '%' . $request->search . '%');
            });
        }

        $featuredAds = $query->orderBy('created_at', 'desc')
            ->paginate(20)
            ->appends($request->query());

        // Statistics
        $stats = [
            'total' => FeaturedAd::count(),
            'active' => FeaturedAd::where('status', 'active')->count(),
            'pending' => FeaturedAd::where('status', 'pending')->count(),
            'expired' => FeaturedAd::where('status', 'expired')->count(),
            'revenue' => FeaturedAd::whereIn('status', ['active', 'expired'])->sum('price'),
        ];

        return view('admin.featured-ads.index', compact('featuredAds', 'stats'));
    }

    /**
     * Show the form for creating a new featured ad (admin-initiated).
     */
    public function create()
    {
        $vendors = Vendor::with('services')
            ->where('is_verified', true)
            ->orderBy('business_name')
            ->get();

        return view('admin.featured-ads.create', compact('vendors'));
    }

    /**
     * Store a newly created featured ad (admin-initiated).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'vendor_id' => 'required|exists:vendors,id',
            'service_id' => 'required|exists:services,id',
            'placement' => 'required|in:homepage,category,search',
            'headline' => 'required|string|max:100',
            'description' => 'nullable|string|max:500',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'status' => 'required|in:pending,active,expired,suspended',
            'price' => 'required|numeric|min:0',
            'payment_ref' => 'nullable|string|max:255',
            'admin_notes' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Verify service belongs to vendor
        $service = Service::where('id', $validated['service_id'])
            ->where('vendor_id', $validated['vendor_id'])
            ->firstOrFail();

        // Handle image upload
        if ($request->hasFile('image')) {
            $validated['image_path'] = $request->file('image')->store('featured-ads', 'public');
        }

        FeaturedAd::create($validated);

        return redirect()->route('admin.featured-ads.index')
            ->with('success', 'Featured ad created successfully!');
    }

    /**
     * Display the specified featured ad.
     */
    public function show($id)
    {
        $featuredAd = FeaturedAd::with(['vendor.user', 'service'])->findOrFail($id);

        return view('admin.featured-ads.show', compact('featuredAd'));
    }

    /**
     * Show the form for editing the specified featured ad.
     */
    public function edit($id)
    {
        $featuredAd = FeaturedAd::with(['vendor.services'])->findOrFail($id);
        $vendors = Vendor::with('services')
            ->where('is_verified', true)
            ->orderBy('business_name')
            ->get();

        return view('admin.featured-ads.edit', compact('featuredAd', 'vendors'));
    }

    /**
     * Update the specified featured ad.
     */
    public function update(Request $request, $id)
    {
        $featuredAd = FeaturedAd::findOrFail($id);

        $validated = $request->validate([
            'placement' => 'required|in:homepage,category,search',
            'headline' => 'required|string|max:100',
            'description' => 'nullable|string|max:500',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'status' => 'required|in:pending,active,expired,suspended',
            'price' => 'required|numeric|min:0',
            'payment_ref' => 'nullable|string|max:255',
            'admin_notes' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image
            if ($featuredAd->image_path) {
                Storage::disk('public')->delete($featuredAd->image_path);
            }
            $validated['image_path'] = $request->file('image')->store('featured-ads', 'public');
        }

        $featuredAd->update($validated);

        return redirect()->route('admin.featured-ads.index')
            ->with('success', 'Featured ad updated successfully!');
    }

    /**
     * Approve a pending featured ad.
     */
    public function approve($id)
    {
        $featuredAd = FeaturedAd::findOrFail($id);

        if ($featuredAd->status !== 'pending') {
            return back()->with('error', 'Only pending ads can be approved.');
        }

        $featuredAd->update(['status' => 'active']);

        return back()->with('success', 'Featured ad approved and activated!');
    }

    /**
     * Reject a pending featured ad.
     */
    public function reject($id)
    {
        $featuredAd = FeaturedAd::findOrFail($id);

        if ($featuredAd->status !== 'pending') {
            return back()->with('error', 'Only pending ads can be rejected.');
        }

        $featuredAd->update(['status' => 'suspended']);

        return back()->with('success', 'Featured ad rejected.');
    }

    /**
     * Suspend an active featured ad.
     */
    public function suspend($id)
    {
        $featuredAd = FeaturedAd::findOrFail($id);

        $featuredAd->update(['status' => 'suspended']);

        return back()->with('success', 'Featured ad suspended.');
    }

    /**
     * Remove the specified featured ad.
     */
    public function destroy($id)
    {
        $featuredAd = FeaturedAd::findOrFail($id);

        // Delete image
        if ($featuredAd->image_path) {
            Storage::disk('public')->delete($featuredAd->image_path);
        }

        $featuredAd->delete();

        return redirect()->route('admin.featured-ads.index')
            ->with('success', 'Featured ad deleted successfully.');
    }

    /**
     * Get services for a specific vendor (AJAX).
     */
    public function getVendorServices($vendorId)
    {
        $services = Service::where('vendor_id', $vendorId)
            ->where('is_active', true)
            ->get(['id', 'title']);

        return response()->json($services);
    }

    /**
     * Export featured ads data to CSV.
     */
    public function export(Request $request)
    {
        $query = FeaturedAd::with(['vendor', 'service']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $featuredAds = $query->orderBy('created_at', 'desc')->get();

        $filename = 'featured-ads-' . date('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($featuredAds) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'Vendor', 'Service', 'Placement', 'Status', 'Start Date', 'End Date', 'Price', 'Views', 'Clicks', 'CTR']);

            foreach ($featuredAds as $ad) {
                fputcsv($file, [
                    $ad->id,
                    $ad->vendor->business_name,
                    $ad->service->title,
                    ucfirst($ad->placement),
                    ucfirst($ad->status),
                    $ad->start_date->format('Y-m-d'),
                    $ad->end_date->format('Y-m-d'),
                    'GH₵ ' . number_format($ad->price, 2),
                    $ad->views,
                    $ad->clicks,
                    $ad->getCTR() . '%',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}

