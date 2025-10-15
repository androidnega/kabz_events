<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\CallbackRequest;
use App\Models\Vendor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CallbackRequestController extends Controller
{
    /**
     * Display callback requests for the authenticated vendor.
     */
    public function index(): View
    {
        $vendor = Auth::user()->vendor;
        
        $callbackRequests = $vendor->callbackRequests()
            ->latest()
            ->paginate(20);
        
        $pendingCount = $vendor->callbackRequests()->where('status', 'pending')->count();
        
        return view('vendor.callbacks.index', compact('callbackRequests', 'pendingCount'));
    }

    /**
     * Store a callback request from a client.
     */
    public function store(Request $request, Vendor $vendor): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
        ]);

        $callbackRequest = CallbackRequest::create([
            'vendor_id' => $vendor->id,
            'client_name' => $validated['name'],
            'client_phone' => $validated['phone'],
            'status' => 'pending',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Callback request sent successfully!',
            'data' => $callbackRequest,
        ]);
    }

    /**
     * Mark a callback request as completed.
     */
    public function complete(Request $request, CallbackRequest $callbackRequest): JsonResponse
    {
        // Ensure the callback belongs to the authenticated vendor
        if ($callbackRequest->vendor_id !== Auth::user()->vendor?->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized action.',
            ], 403);
        }

        $callbackRequest->markAsCompleted($request->input('notes'));

        return response()->json([
            'success' => true,
            'message' => 'Callback request marked as completed.',
        ]);
    }

    /**
     * Mark a callback request as cancelled.
     */
    public function cancel(Request $request, CallbackRequest $callbackRequest): JsonResponse
    {
        // Ensure the callback belongs to the authenticated vendor
        if ($callbackRequest->vendor_id !== Auth::user()->vendor?->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized action.',
            ], 403);
        }

        $callbackRequest->markAsCancelled($request->input('notes'));

        return response()->json([
            'success' => true,
            'message' => 'Callback request cancelled.',
        ]);
    }
}
