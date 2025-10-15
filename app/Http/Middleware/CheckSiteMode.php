<?php

namespace App\Http\Middleware;

use App\Models\User;
use App\Services\SettingsService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSiteMode
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if site mode is enabled
        $siteMode = SettingsService::get('site_mode', 'live');
        $siteModeEnabled = SettingsService::get('site_mode_enabled', false);

        // Allow super admins to bypass maintenance mode
        $user = auth()->user();
        $isSuperAdmin = false;
        
        if ($user instanceof User) {
            $isSuperAdmin = $user->hasRole('super_admin');
        }

        // Allow access to login, logout, and super admin routes
        $allowedRoutes = [
            'login',
            'logout',
            'password.*',
            'superadmin.*',
        ];

        foreach ($allowedRoutes as $pattern) {
            if ($request->routeIs($pattern)) {
                return $next($request);
            }
        }

        // If site mode is enabled and user is not super admin
        if ($siteModeEnabled && !$isSuperAdmin) {
            switch ($siteMode) {
                case 'maintenance':
                    return redirect()->route('site.maintenance');
                
                case 'coming_soon':
                    return redirect()->route('site.coming-soon');
                
                case 'update':
                    return redirect()->route('site.update');
            }
        }

        return $next($request);
    }
}
