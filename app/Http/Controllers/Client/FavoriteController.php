<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Bookmark;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    /**
     * Display the client's favorite vendors.
     */
    public function index()
    {
        $user = Auth::user();
        
        // Get all bookmarked vendors
        $favorites = Bookmark::where('user_id', $user->id)
            ->with(['vendor' => function ($query) {
                $query->with('category');
            }])
            ->latest()
            ->paginate(12);

        return view('client.favorites.index', compact('favorites'));
    }

    /**
     * Add a vendor to favorites.
     */
    public function store(Request $request, $vendorId)
    {
        $user = Auth::user();
        $vendor = Vendor::findOrFail($vendorId);

        // Check if already bookmarked
        $exists = Bookmark::where('user_id', $user->id)
            ->where('vendor_id', $vendorId)
            ->exists();

        if ($exists) {
            return back()->with('info', 'Vendor is already in your favorites.');
        }

        Bookmark::create([
            'user_id' => $user->id,
            'vendor_id' => $vendorId,
        ]);

        return back()->with('success', 'Vendor added to favorites!');
    }

    /**
     * Remove a vendor from favorites.
     */
    public function destroy($vendorId)
    {
        $user = Auth::user();

        Bookmark::where('user_id', $user->id)
            ->where('vendor_id', $vendorId)
            ->delete();

        return back()->with('success', 'Vendor removed from favorites.');
    }
}

