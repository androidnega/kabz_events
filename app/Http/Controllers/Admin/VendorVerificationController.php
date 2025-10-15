<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VerificationRequest;
use Illuminate\Http\Request;

class VendorVerificationController extends Controller
{
    /**
     * Display list of verification requests.
     */
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'pending');
        
        if ($tab === 'verified') {
            // Show all verified vendors
            $vendors = \App\Models\Vendor::where('is_verified', true)
                ->with(['user', 'services'])
                ->latest('verified_at')
                ->paginate(15);
            
            return view('admin.verifications.index', compact('vendors', 'tab'));
        } else {
            // Show pending and reviewed verification requests with admin info
            $requests = VerificationRequest::with(['vendor', 'decidedBy'])
                ->where('status', 'pending')
                ->latest()
                ->paginate(15);
            
            return view('admin.verifications.index', compact('requests', 'tab'));
        }
    }

    /**
     * Approve verification request.
     */
    public function approve($id)
    {
        $verificationRequest = VerificationRequest::findOrFail($id);
        
        $verificationRequest->update([
            'status' => 'approved',
            'decided_at' => now(),
            'decided_by' => auth()->id(),
            'admin_note' => 'Approved by ' . auth()->user()->name,
        ]);

        // Update vendor verification status
        $verificationRequest->vendor->update([
            'is_verified' => true,
            'verified_at' => now(),
        ]);

        return back()->with('success', 'Vendor approved successfully!');
    }

    /**
     * Reject verification request.
     */
    public function reject(Request $request, $id)
    {
        $verificationRequest = VerificationRequest::findOrFail($id);
        
        $verificationRequest->update([
            'status' => 'rejected',
            'decided_by' => auth()->id(),
            'admin_note' => $request->input('admin_note', 'Rejected by admin'),
            'decided_at' => now(),
        ]);

        return back()->with('error', 'Vendor verification rejected.');
    }

    /**
     * Suspend a verified vendor (remove verification).
     */
    public function suspend($vendorId)
    {
        $vendor = \App\Models\Vendor::findOrFail($vendorId);
        
        $vendor->update([
            'is_verified' => false,
            'verified_at' => null,
        ]);

        // Create a note in verification_requests if exists
        VerificationRequest::where('vendor_id', $vendorId)
            ->where('status', 'approved')
            ->update([
                'status' => 'suspended',
                'admin_note' => 'Verification suspended by admin',
                'decided_at' => now(),
            ]);

        return back()->with('success', 'Vendor verification suspended successfully!');
    }

    /**
     * Cancel verification (permanent removal).
     */
    public function cancelVerification($vendorId)
    {
        $vendor = \App\Models\Vendor::findOrFail($vendorId);
        
        $vendor->update([
            'is_verified' => false,
            'verified_at' => null,
        ]);

        // Update all verification requests for this vendor
        VerificationRequest::where('vendor_id', $vendorId)
            ->update([
                'status' => 'cancelled',
                'admin_note' => 'Verification cancelled by admin',
                'decided_at' => now(),
            ]);

        return back()->with('success', 'Vendor verification cancelled permanently!');
    }
}
