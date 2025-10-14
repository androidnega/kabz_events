<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Service;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ServiceController extends Controller
{
    /**
     * Display a listing of the vendor's services.
     */
    public function index(): View|RedirectResponse
    {
        $vendor = Auth::user()->vendor;

        if (!$vendor) {
            return redirect()
                ->route('vendor.register')
                ->with('error', 'Please create your vendor profile first.');
        }

        $services = $vendor->services()
            ->with('category')
            ->latest()
            ->get();

        return view('vendor.services.index', compact('services', 'vendor'));
    }

    /**
     * Show the form for creating a new service.
     */
    public function create(): View|RedirectResponse
    {
        $vendor = Auth::user()->vendor;

        if (!$vendor) {
            return redirect()
                ->route('vendor.register')
                ->with('error', 'Please create your vendor profile first.');
        }

        $categories = Category::orderBy('name')->get();
        
        // Determine default category intelligently
        $defaultCategoryId = null;
        
        // 1. Check if vendor has existing services - use that category
        if ($vendor->services()->count() > 0) {
            $defaultCategoryId = $vendor->services()->first()->category_id;
        } 
        // 2. Map from verification business_category to category_id
        elseif ($vendor->verificationRequest && $vendor->verificationRequest->business_category) {
            $businessCategory = $vendor->verificationRequest->business_category;
            $category = Category::where('name', 'LIKE', "%{$businessCategory}%")->first();
            $defaultCategoryId = $category?->id;
        }
        
        // 3. Fallback to "Event Planning & Coordination" (id: 6)
        if (!$defaultCategoryId) {
            $defaultCategoryId = 6; // Event Planning & Coordination
        }

        return view('vendor.services.create', compact('categories', 'vendor', 'defaultCategoryId'));
    }

    /**
     * Store a newly created service in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $vendor = Auth::user()->vendor;

        if (!$vendor) {
            return redirect()
                ->route('vendor.register')
                ->with('error', 'Please create your vendor profile first.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string|max:2000',
            'price_min' => 'nullable|numeric|min:0',
            'price_max' => 'nullable|numeric|gte:price_min',
            'pricing_type' => 'required|in:fixed,hourly,package,quote',
            'is_active' => 'boolean',
        ]);

        $service = Service::create([
            'vendor_id' => $vendor->id,
            'category_id' => $validated['category_id'],
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'price_min' => $validated['price_min'] ?? null,
            'price_max' => $validated['price_max'] ?? null,
            'pricing_type' => $validated['pricing_type'],
            'is_active' => $request->has('is_active') ? true : false,
        ]);

        return redirect()
            ->route('vendor.services.index')
            ->with('success', 'Service added successfully!');
    }

    /**
     * Display the specified service.
     */
    public function show(Service $service): View|RedirectResponse
    {
        // Ensure vendor owns this service
        if ($service->vendor_id !== Auth::user()->vendor?->id) {
            abort(403, 'Unauthorized action.');
        }

        return view('vendor.services.show', compact('service'));
    }

    /**
     * Show the form for editing the specified service.
     */
    public function edit(Service $service): View|RedirectResponse
    {
        // Ensure vendor owns this service
        if ($service->vendor_id !== Auth::user()->vendor?->id) {
            abort(403, 'Unauthorized action.');
        }

        $categories = Category::orderBy('name')->get();

        return view('vendor.services.edit', compact('service', 'categories'));
    }

    /**
     * Update the specified service in storage.
     */
    public function update(Request $request, Service $service): RedirectResponse
    {
        // Ensure vendor owns this service
        if ($service->vendor_id !== Auth::user()->vendor?->id) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string|max:2000',
            'price_min' => 'nullable|numeric|min:0',
            'price_max' => 'nullable|numeric|gte:price_min',
            'pricing_type' => 'required|in:fixed,hourly,package,quote',
            'is_active' => 'boolean',
        ]);

        $service->update([
            'category_id' => $validated['category_id'],
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'price_min' => $validated['price_min'] ?? null,
            'price_max' => $validated['price_max'] ?? null,
            'pricing_type' => $validated['pricing_type'],
            'is_active' => $request->has('is_active') ? true : false,
        ]);

        return redirect()
            ->route('vendor.services.index')
            ->with('success', 'Service updated successfully!');
    }

    /**
     * Remove the specified service from storage.
     */
    public function destroy(Service $service): RedirectResponse
    {
        // Ensure vendor owns this service
        if ($service->vendor_id !== Auth::user()->vendor?->id) {
            abort(403, 'Unauthorized action.');
        }

        $service->delete();

        return redirect()
            ->route('vendor.services.index')
            ->with('success', 'Service deleted successfully!');
    }
}
