<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Region;
use App\Models\Service;
use App\Models\User;
use App\Models\Vendor;
use App\Models\VendorSubscription;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class PublicVendorController extends Controller
{
    /**
     * Show the public vendor registration form.
     */
    public function create(): View
    {
        $categories = Category::orderBy('display_order')->get();
        $regions = Region::orderBy('name')->get();
        
        return view('vendor.public_register', compact('categories', 'regions'));
    }

    /**
     * Handle public vendor registration.
     */
    public function store(Request $request): RedirectResponse
    {
        // Validate all fields (User + Vendor + Service)
        $validated = $request->validate([
            // Step 1: Account Information
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|max:20',
            'password' => 'required|confirmed|min:8',
            
            // Step 2: Business Information
            'business_name' => 'required|string|max:255|unique:vendors,business_name',
            'category_id' => 'required|exists:categories,id',
            'description' => 'required|string|max:2000',
            'address' => 'required|string|max:255',
            'region' => 'required|string|max:100',
            'city' => 'required|string|max:100',
            'website' => 'nullable|url|max:255',
            'whatsapp' => 'nullable|string|max:20',
            
            // Step 3: Service Information
            'service_name' => 'required|string|max:255',
            'service_type' => 'required|in:fixed,hourly,package,quote',
            'service_price' => 'nullable|numeric|min:0',
            'service_description' => 'required|string|max:1000',
            'portfolio_images.*' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:5120',
            
            // Step 4: Terms
            'terms' => 'required|accepted',
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
            'whatsapp' => $validated['whatsapp'] ?? $validated['phone'],
            'description' => $validated['description'],
            'address' => $validated['address'] . ', ' . $validated['city'] . ', ' . $validated['region'],
            'website' => $validated['website'] ?? null,
        ]);

        // Create default Free Plan subscription
        VendorSubscription::create([
            'vendor_id' => $vendor->id,
            'plan' => 'Free',
            'price_amount' => 0.00,
            'currency' => 'GHS',
            'status' => 'active',
            'started_at' => now(),
            'ends_at' => null, // Lifetime
            'payment_reference' => null,
        ]);

        // Create the first service listing
        $service = Service::create([
            'vendor_id' => $vendor->id,
            'category_id' => $validated['category_id'],
            'name' => $validated['service_name'],
            'description' => $validated['service_description'],
            'price_type' => $validated['service_type'],
            'price_min' => $validated['service_type'] !== 'quote' ? $validated['service_price'] : null,
            'is_available' => true,
        ]);

        // Handle portfolio image uploads
        if ($request->hasFile('portfolio_images')) {
            $imagePaths = [];
            foreach ($request->file('portfolio_images') as $index => $image) {
                $path = $image->store('vendor-portfolios/' . $vendor->id, 'public');
                $imagePaths[] = $path;
                
                // Store only first 3 images for now
                if ($index >= 2) break;
            }
            
            // Store images in vendor's verification_doc_path as JSON for now
            // (In production, you'd have a separate portfolio_images table)
            if (!empty($imagePaths)) {
                $vendor->update([
                    'verification_doc_path' => json_encode($imagePaths)
                ]);
            }
        }

        // Fire registered event
        event(new Registered($user));

        // Log the user in
        Auth::login($user);

        // Redirect to unified dashboard with success message
        return redirect()
            ->route('dashboard')
            ->with('success', 'Welcome to KABZS EVENT! Your vendor account has been created successfully. You can now add more services and apply for verification.');
    }
}
