<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Vendor;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class VendorController extends Controller
{
    /**
     * Display a listing of all vendors for admin management.
     */
    public function index(Request $request)
    {
        $query = Vendor::with(['user', 'services']);

        // Search filter
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('business_name', 'like', '%' . $request->search . '%')
                  ->orWhereHas('user', function($userQuery) use ($request) {
                      $userQuery->where('name', 'like', '%' . $request->search . '%')
                               ->orWhere('email', 'like', '%' . $request->search . '%');
                  });
            });
        }

        // Verification status filter
        if ($request->filled('status')) {
            if ($request->status === 'verified') {
                $query->where('is_verified', true);
            } elseif ($request->status === 'unverified') {
                $query->where('is_verified', false);
            }
        }

        // Active/Inactive filter
        if ($request->filled('active')) {
            $query->whereHas('user', function($q) use ($request) {
                if ($request->active === 'active') {
                    $q->whereNotNull('email_verified_at');
                } else {
                    $q->whereNull('email_verified_at');
                }
            });
        }

        $vendors = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();
        
        return view('admin.vendors.index', compact('vendors'));
    }

    /**
     * Display the specified vendor details.
     */
    public function show($id)
    {
        $vendor = Vendor::with(['user', 'services', 'reviews'])->findOrFail($id);
        $services = $vendor->services()->latest()->get();
        $reviews = $vendor->reviews()->with('user')->latest()->take(10)->get();
        
        return view('admin.vendors.show', compact('vendor', 'services', 'reviews'));
    }

    /**
     * Verify a vendor.
     */
    public function verify($id)
    {
        $vendor = Vendor::findOrFail($id);
        
        $vendor->update([
            'is_verified' => true,
            'verified_at' => now(),
        ]);
        
        return back()->with('success', 'Vendor verified successfully! 🇬🇭');
    }

    /**
     * Unverify a vendor.
     */
    public function unverify($id)
    {
        $vendor = Vendor::findOrFail($id);
        
        $vendor->update([
            'is_verified' => false,
            'verified_at' => null,
        ]);
        
        return back()->with('success', 'Vendor verification removed.');
    }

    /**
     * Deactivate a vendor account.
     */
    public function deactivate($id)
    {
        $vendor = Vendor::findOrFail($id);
        
        // Deactivate the user account
        $vendor->user->update([
            'email_verified_at' => null,
        ]);
        
        // Deactivate all services
        $vendor->services()->update(['is_active' => false]);
        
        return back()->with('success', 'Vendor account deactivated.');
    }

    /**
     * Activate a vendor account.
     */
    public function activate($id)
    {
        $vendor = Vendor::findOrFail($id);
        
        // Activate the user account
        $vendor->user->update([
            'email_verified_at' => now(),
        ]);
        
        return back()->with('success', 'Vendor account activated successfully.');
    }

    /**
     * Reset vendor's password.
     */
    public function resetPassword($id)
    {
        $vendor = Vendor::findOrFail($id);
        $vendor->user->update(['password' => Hash::make('12345678')]);
        
        return back()->with('success', 'Password reset to: 12345678');
    }

    /**
     * Delete a vendor and their account.
     */
    public function destroy($id)
    {
        $vendor = Vendor::findOrFail($id);
        $user = $vendor->user;
        
        // Delete vendor (cascade will handle related records)
        $vendor->delete();
        
        // Delete user account
        $user->delete();
        
        return redirect()->route('admin.vendors.index')
                        ->with('success', 'Vendor and associated account deleted successfully.');
    }
}
