<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Services\CloudinaryService;
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
        $user = Auth::user();
        
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
            'profile_photo' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
        ]);

        // Check if business name is being changed
        if ($validated['business_name'] !== $vendor->business_name) {
            // Check if vendor is verified
            if ($vendor->is_verified) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'You cannot change your business name after verification. Changing it will require re-verification. Please contact support if you need to update your business name.');
            }

            // Check if vendor can change name
            if (!$vendor->canChangeBusinessName()) {
                $nextDate = $vendor->last_business_name_change_at ? $vendor->last_business_name_change_at->addYear()->format('M d, Y') : 'N/A';
                return redirect()->back()
                    ->withInput()
                    ->with('error', "You have reached the maximum number of business name changes (3 per year). Next change available on: {$nextDate}");
            }

            // Update name change tracking
            $vendor->business_name_changes_count++;
            $vendor->last_business_name_change_at = now();
        }

        // Handle profile photo upload
        if ($request->hasFile('profile_photo')) {
            $cloudinaryService = new CloudinaryService();
            $result = $cloudinaryService->uploadImage(
                $request->file('profile_photo'),
                'profile_photos'
            );
            
            if ($result['success']) {
                // Store the URL (Cloudinary) or path (local)
                $validated['profile_photo'] = $result['url'] ?? $result['path'];
            }
        }

        $vendor->update($validated);

        return redirect()->route('vendor.profile')
            ->with('success', 'Profile updated successfully!');
    }
}

