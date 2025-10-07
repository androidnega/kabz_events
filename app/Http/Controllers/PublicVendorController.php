<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Vendor;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class PublicVendorController extends Controller
{
    /**
     * Show the public vendor registration form.
     */
    public function create(): View
    {
        return view('vendor.public_register');
    }

    /**
     * Handle public vendor registration.
     */
    public function store(Request $request): RedirectResponse
    {
        // Validate all fields (User + Vendor)
        $validated = $request->validate([
            // User fields
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|confirmed|min:8',
            
            // Vendor fields
            'business_name' => 'required|string|max:255|unique:vendors,business_name',
            'phone' => 'required|string|max:20',
            'description' => 'nullable|string|max:2000',
            'address' => 'nullable|string|max:255',
            'website' => 'nullable|url',
        ]);

        // Create the user account
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        // Assign vendor role
        $user->assignRole('vendor');

        // Create the vendor profile
        $vendor = Vendor::create([
            'user_id' => $user->id,
            'business_name' => $validated['business_name'],
            'phone' => $validated['phone'],
            'description' => $validated['description'] ?? null,
            'address' => $validated['address'] ?? null,
            'website' => $validated['website'] ?? null,
        ]);

        // Fire registered event
        event(new Registered($user));

        // Log the user in
        Auth::login($user);

        // Redirect to vendor dashboard with success message
        return redirect()
            ->route('vendor.dashboard')
            ->with('success', 'Welcome to KABZS EVENT! Your vendor account has been created successfully.');
    }
}
