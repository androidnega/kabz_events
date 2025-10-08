<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display the appropriate dashboard based on user's role.
     * 
     * This is a unified dashboard route where all authenticated users
     * visit /dashboard, and the system automatically detects their role
     * and serves the correct dashboard view.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     */
    public function index(Request $request)
    {
        $user = $request->user();

        // Detect role dynamically and render appropriate dashboard
        if ($user->hasRole('super_admin')) {
            return view('superadmin.dashboard');
        }

        if ($user->hasRole('admin')) {
            return view('admin.dashboard');
        }

        if ($user->hasRole('vendor')) {
            return view('vendor.dashboard');
        }

        if ($user->hasRole('client')) {
            return view('client.dashboard');
        }

        // Default fallback - user has no recognized role
        abort(403, 'Unauthorized. No valid role assigned to this user.');
    }
}
