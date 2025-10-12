<?php

namespace App\Http\Middleware;

use App\Models\UserOnlineStatus;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UpdateUserOnlineStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();
            
            // Determine user type
            $userType = 'client';
            if ($user->hasRole('vendor')) {
                $userType = 'vendor';
            } elseif ($user->hasRole('super_admin')) {
                $userType = 'admin';
            }
            
            // Update online status
            UserOnlineStatus::updateStatus(
                $user->id,
                $userType,
                true
            );
        }

        return $next($request);
    }
}

