<?php

namespace App\Http\Controllers;

use App\Models\Vendor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class VendorRegistrationController extends Controller
{
    /**
     * Show the vendor registration form.
     */
    public function create(): View|RedirectResponse
    {
        // Check if user already has a vendor profile
        if (Auth::user()->vendor) {
            return redirect()
                ->route('vendor.dashboard')
                ->with('info', 'You already have a vendor profile.');
        }

        return view('vendor.register');
    }

    /**
     * Handle vendor registration.
     */
    public function store(Request $request): RedirectResponse
    {
        // Check if user already has a vendor profile
        if (Auth::user()->vendor) {
            return redirect()
                ->route('vendor.dashboard')
                ->with('info', 'You already have a vendor profile.');
        }

        // Validate the request
        $validated = $request->validate([
            'business_name' => 'required|string|max:255|unique:vendors,business_name',
            'description' => 'nullable|string|max:2000',
            'phone' => 'required|string|max:20',
            'whatsapp' => 'nullable|string|max:20',
            'website' => 'nullable|url',
            'address' => 'nullable|string|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ]);

        // Create the vendor profile
        $vendor = Vendor::create([
            'user_id' => Auth::id(),
            'business_name' => $validated['business_name'],
            'description' => $validated['description'] ?? null,
            'phone' => $validated['phone'],
            'whatsapp' => $validated['whatsapp'] ?? null,
            'website' => $validated['website'] ?? null,
            'address' => $validated['address'] ?? null,
            'latitude' => $validated['latitude'] ?? null,
            'longitude' => $validated['longitude'] ?? null,
        ]);

        // Assign vendor role to user
        Auth::user()->assignRole('vendor');

        // Redirect to vendor dashboard with success message
        return redirect()
            ->route('vendor.dashboard')
            ->with('success', 'Congratulations! Your vendor profile has been created successfully.');
    }
}
