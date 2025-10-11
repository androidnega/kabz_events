<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    /**
     * Display vendor profile
     */
    public function index()
    {
        $vendor = Auth::user()->vendor;
        
        if (!$vendor) {
            return redirect()->route('dashboard')
                ->with('error', 'Vendor profile not found.');
        }

        // Load relationships
        $vendor->load(['services', 'region', 'district', 'town']);

        return view('vendor.profile', compact('vendor'));
    }

    /**
     * Show edit form
     */
    public function edit()
    {
        $vendor = Auth::user()->vendor;
        
        if (!$vendor) {
            return redirect()->route('dashboard')
                ->with('error', 'Vendor profile not found.');
        }

        return view('vendor.profile-edit', compact('vendor'));
    }

    /**
     * Update vendor profile
     */
    public function update(Request $request)
    {
        $vendor = Auth::user()->vendor;
        
        if (!$vendor) {
            return redirect()->route('dashboard')
                ->with('error', 'Vendor profile not found.');
        }

        $validated = $request->validate([
            'business_name' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'phone' => 'required|string|max:20',
            'whatsapp' => 'nullable|string|max:20',
            'website' => 'nullable|url|max:255',
            'address' => 'nullable|string|max:500',
        ]);

        $vendor->update($validated);

        return redirect()->route('vendor.profile')
            ->with('success', 'Profile updated successfully!');
    }
}

