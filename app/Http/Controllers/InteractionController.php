<?php

namespace App\Http\Controllers;

use App\Models\UserActivityLog;
use App\Models\UserInteraction;
use App\Models\Vendor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InteractionController extends Controller
{
    /**
     * Log vendor view interaction.
     */
    public function logView(Request $request, Vendor $vendor): JsonResponse
    {
        $userId = auth()->id();
        
        UserActivityLog::create([
            'user_id' => $userId,
            'vendor_id' => $vendor->id,
            'action' => 'viewed_vendor',
            'meta' => $request->input('meta'),
        ]);

        return response()->json(['ok' => true]);
    }

    /**
     * Log search interaction.
     */
    public function logSearch(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'query' => 'required|string|max:255',
            'category_id' => 'nullable|integer',
            'location' => 'nullable|string|max:255',
        ]);

        UserActivityLog::create([
            'user_id' => auth()->id(),
            'vendor_id' => null,
            'action' => 'searched',
            'meta' => json_encode($validated),
        ]);

        return response()->json(['ok' => true]);
    }

    /**
     * Log category view interaction.
     */
    public function logCategoryView(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'category_id' => 'required|integer',
            'category_name' => 'nullable|string|max:255',
        ]);

        UserActivityLog::create([
            'user_id' => auth()->id(),
            'vendor_id' => null,
            'action' => 'viewed_category',
            'meta' => json_encode($validated),
        ]);

        return response()->json(['ok' => true]);
    }

    /**
     * Log recommendation click.
     */
    public function logRecommendationClick(Request $request, Vendor $vendor): JsonResponse
    {
        UserActivityLog::create([
            'user_id' => auth()->id(),
            'vendor_id' => $vendor->id,
            'action' => 'clicked_recommendation',
            'meta' => $request->input('source', 'homepage'),
        ]);

        return response()->json(['ok' => true]);
    }

    /**
     * Log weighted interaction action.
     * Supports: view, search, click_contact, bookmark, book, review
     */
    public function logAction(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'vendor_id' => 'required|exists:vendors,id',
            'action' => 'required|string|in:view,search,click_contact,bookmark,book,review',
            'meta' => 'nullable|string',
        ]);

        // Action weight mapping
        $weights = [
            'view' => 1,
            'search' => 1,
            'click_contact' => 3,
            'bookmark' => 4,
            'book' => 8,
            'review' => 6,
        ];

        $action = $validated['action'];
        $weight = $weights[$action] ?? 1;

        UserInteraction::create([
            'user_id' => auth()->id(),
            'vendor_id' => $validated['vendor_id'],
            'action' => $action,
            'weight' => $weight,
            'meta' => $validated['meta'] ?? null,
        ]);

        return response()->json(['ok' => true, 'weight' => $weight]);
    }
}

