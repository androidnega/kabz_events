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
     * and redirects to the appropriate role-specific dashboard.
     * 
     * Phase K5: Unified URL Structure
     * All dashboards now live under /dashboard/* prefix
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     */
    public function index(Request $request)
    {
        $user = $request->user();

        // Detect role dynamically and redirect to role-specific dashboard
        if ($user->hasRole('super_admin')) {
            return redirect()->route('superadmin.dashboard');
        }

        if ($user->hasRole('admin')) {
            return redirect()->route('admin.dashboard');
        }

        if ($user->hasRole('vendor')) {
            return redirect()->route('vendor.dashboard');
        }

        if ($user->hasRole('client')) {
            return redirect()->route('client.dashboard');
        }

        // Default fallback - user has no recognized role
        abort(403, 'Unauthorized. No valid role assigned to this user.');
    }
}
