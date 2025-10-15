<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\VerificationRequest;
use App\Services\CloudinaryService;
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
        
        // Load decidedBy relationship if request exists
        if ($request) {
            $request->load('decidedBy');
        }
        
        // Load Ghana regions with districts and towns for location selection
        $regions = \App\Models\Region::with(['districts.towns'])->orderBy('name')->get();
        
        // Get vendor's primary category from their first service
        $vendorCategory = 'Other Services'; // Default
        $firstService = $vendor->services()->with('category')->first();
        if ($firstService && $firstService->category) {
            $vendorCategory = $firstService->category->name;
        }
        
        return view('vendor.verification', compact('vendor', 'request', 'regions', 'vendorCategory'));
    }

    /**
     * Submit verification request.
     */
    public function store(Request $request)
    {
        $vendor = Auth::user()->vendor;

        // Validate comprehensive form data
        $validated = $request->validate([
            // Page 1: Business Information
            'business_category' => 'required|string|max:255',
            'business_registration_number' => 'nullable|string|max:255',
            'business_description' => 'required|string|min:50',
            'years_in_operation' => 'required|integer|min:0|max:100',
            'business_region' => 'required|string|max:255',
            'business_district' => 'required|string|max:255',
            'business_town' => 'required|string|max:255',
            'business_logo' => 'required|file|mimes:jpg,jpeg,png|max:2048',
            
            // Page 2: Contact and Identity
            'contact_full_name' => 'required|string|max:255',
            'contact_role' => 'required|string|max:255',
            'contact_phone' => 'required|string|max:20',
            'contact_email' => 'required|email|max:255',
            'national_id_type' => 'required|string|in:Ghana Card,Passport,Voter ID,Driver\'s License',
            'national_id_number' => 'required|string|max:255',
            'id_document' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'profile_picture' => 'required|file|mimes:jpg,jpeg,png|max:2048',
            
            // Page 3: Verification Evidence
            'social_links' => 'nullable|string',
            'website_url' => 'nullable|url|max:255',
            'proof_of_events.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'reference_letter' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'verification_reason' => 'required|string|min:20',
            'details_confirmed' => 'required|accepted',
            'terms_agreed' => 'required|accepted',
        ]);

        // Check if already submitted
        if ($vendor->verificationRequest) {
            return response()->json(['message' => 'You have already submitted a verification request.'], 400);
        }

        // Store files using CloudinaryService
        $cloudinaryService = new CloudinaryService();
        
        // Upload ID document
        $idResult = $cloudinaryService->uploadImage($request->file('id_document'), 'Ghana_card_verifications');
        $idDocPath = $idResult['url'] ?? $idResult['path'];
        
        // Upload business logo
        $logoResult = $cloudinaryService->uploadImage($request->file('business_logo'), 'profile_photos');
        $logoPath = $logoResult['url'] ?? $logoResult['path'];
        
        // Upload profile picture
        $profileResult = $cloudinaryService->uploadImage($request->file('profile_picture'), 'profile_photos');
        $profilePicPath = $profileResult['url'] ?? $profileResult['path'];
        
        // Upload reference letter (if provided)
        $referenceLetterPath = null;
        if ($request->hasFile('reference_letter')) {
            $refResult = $cloudinaryService->uploadImage($request->file('reference_letter'), 'Ghana_card_verifications');
            $referenceLetterPath = $refResult['url'] ?? $refResult['path'];
        }

        // Upload proof of events
        $proofPaths = [];
        if ($request->hasFile('proof_of_events')) {
            foreach ($request->file('proof_of_events') as $file) {
                $proofResult = $cloudinaryService->uploadImage($file, 'Ghana_card_verifications');
                $proofPaths[] = $proofResult['url'] ?? $proofResult['path'];
            }
        }

        // Create comprehensive verification request
        VerificationRequest::create([
            'vendor_id' => $vendor->id,
            
            // Page 1
            'business_category' => $validated['business_category'],
            'business_registration_number' => $validated['business_registration_number'] ?? null,
            'business_description' => $validated['business_description'],
            'years_in_operation' => $validated['years_in_operation'],
            'business_location' => $validated['business_town'] . ', ' . $validated['business_district'] . ', ' . $validated['business_region'],
            'business_logo_path' => $logoPath,
            
            // Page 2
            'contact_full_name' => $validated['contact_full_name'],
            'contact_role' => $validated['contact_role'],
            'contact_phone' => $validated['contact_phone'],
            'contact_email' => $validated['contact_email'],
            'national_id_type' => $validated['national_id_type'],
            'national_id_number' => $validated['national_id_number'],
            'id_document_path' => $idDocPath,
            'profile_picture_path' => $profilePicPath,
            
            // Page 3
            'social_links' => $request->social_links ? json_decode($request->social_links, true) : [],
            'website_url' => $validated['website_url'] ?? null,
            'proof_of_events' => $proofPaths,
            'reference_letter_path' => $referenceLetterPath,
            'verification_reason' => $validated['verification_reason'],
            'terms_agreed' => true,
            'details_confirmed' => true,
            
            'status' => 'pending',
            'submitted_at' => now(),
        ]);

        return response()->json(['success' => true, 'message' => 'Verification request submitted successfully!']);
    }
}
