<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class SettingsController extends Controller
{
    /**
     * Display client settings.
     */
    public function index()
    {
        $user = Auth::user();
        return view('client.settings.index', compact('user'));
    }

    /**
     * Update notification preferences.
     */
    public function updateNotifications(Request $request)
    {
        $user = Auth::user();

        // Placeholder: Store notification preferences
        // This would typically update a user_preferences table
        
        return back()->with('success', 'Notification preferences updated successfully!');
    }

    /**
     * Update privacy settings.
     */
    public function updatePrivacy(Request $request)
    {
        $request->validate([
            'profile_visibility' => 'required|in:public,private',
            'show_email' => 'boolean',
        ]);

        // Placeholder: Store privacy settings
        
        return back()->with('success', 'Privacy settings updated successfully!');
    }

    /**
     * Update password.
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        return back()->with('success', 'Password updated successfully!');
    }
}

