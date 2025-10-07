<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class MessageRateLimiter
{
    /**
     * Handle an incoming request.
     * Limit: 5 messages per minute per user.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $key = 'msg_rate_' . $user->id;
        $maxMessages = 5;
        $windowSeconds = 60;

        $count = Cache::get($key, 0);

        if ($count >= $maxMessages) {
            return response()->json([
                'error' => 'Too many messages. Please wait a minute before sending again.'
            ], 429);
        }

        Cache::put($key, $count + 1, now()->addSeconds($windowSeconds));

        return $next($request);
    }
}
