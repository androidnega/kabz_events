<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\SuperAdmin\DashboardController as SuperAdminDashboardController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Vendor\DashboardController as VendorDashboardController;
use App\Http\Controllers\Client\DashboardController as ClientDashboardController;

class DashboardController extends Controller
{
    /**
     * Display the appropriate dashboard based on user's role.
     * 
     * This is a unified dashboard route where all authenticated users
     * visit /dashboard, and the system automatically detects their role
     * and delegates to the appropriate role-specific controller.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response|\Illuminate\View\View
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     */
    public function index(Request $request)
    {
        $user = $request->user();

        // Detect role dynamically and delegate to role-specific controller
        if ($user->hasRole('super_admin')) {
            $controller = app(SuperAdminDashboardController::class);
            return $controller->index($request);
        }

        if ($user->hasRole('admin')) {
            $controller = app(AdminDashboardController::class);
            return $controller->index($request);
        }

        if ($user->hasRole('vendor')) {
            $controller = app(VendorDashboardController::class);
            return $controller->index($request);
        }

        if ($user->hasRole('client')) {
            $controller = app(ClientDashboardController::class);
            return $controller->index($request);
        }

        // Default fallback - user has no recognized role
        abort(403, 'Unauthorized. No valid role assigned to this user.');
    }
}
