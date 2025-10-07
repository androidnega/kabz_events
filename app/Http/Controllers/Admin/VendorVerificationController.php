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
    public function index()
    {
        $requests = VerificationRequest::with('vendor')
            ->latest()
            ->paginate(15);
        
        return view('admin.verifications.index', compact('requests'));
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
            'admin_note' => 'Approved by admin',
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
            'admin_note' => $request->input('admin_note', 'Rejected by admin'),
            'decided_at' => now(),
        ]);

        return back()->with('error', 'Vendor verification rejected.');
    }
}
