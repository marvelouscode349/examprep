<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePremium
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // If user is not logged in
        if (!$user) {
            return response()->json([
                'message' => 'Authentication required.',
            ], 401);
        }

        // If user is NOT premium
        if (!$user->isPremium()) {
            return response()->json([
                'message' => 'Your plan does not allow access. Please upgrade to continue.',
                'code' => 'UPGRADE_REQUIRED'
            ], 403);
        }

        return $next($request);
    }
}