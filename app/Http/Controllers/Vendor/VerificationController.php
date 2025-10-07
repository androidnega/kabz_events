<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\VerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VerificationController extends Controller
{
    /**
     * Display verification request form or status.
     */
    public function index()
    {
        $vendor = Auth::user()->vendor;
        $request = $vendor->verificationRequest;
        
        return view('vendor.verification', compact('vendor', 'request'));
    }

    /**
     * Submit verification request.
     */
    public function store(Request $request)
    {
        $vendor = Auth::user()->vendor;

        $request->validate([
            'id_document' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'social_links.facebook' => 'nullable|url',
            'social_links.instagram' => 'nullable|url',
        ]);

        // Check if already submitted
        if ($vendor->verificationRequest) {
            return back()->with('error', 'You have already submitted a verification request.');
        }

        // Store document
        $path = $request->file('id_document')->store('verification_docs', 'public');

        // Create verification request
        VerificationRequest::create([
            'vendor_id' => $vendor->id,
            'id_document_path' => $path,
            'social_links' => $request->social_links ?? [],
            'status' => 'pending',
            'submitted_at' => now(),
        ]);

        return back()->with('success', 'Verification request submitted successfully! We will review it soon.');
    }
}
