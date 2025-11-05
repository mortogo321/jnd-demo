<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateWithSessionFirst
{
    /**
     * Handle an incoming request.
     * Checks session authentication first, then falls back to token authentication.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // First, try session authentication (web guard)
        if (Auth::guard('web')->check()) {
            // User is authenticated via session, set them as the current user
            $request->setUserResolver(fn () => Auth::guard('web')->user());
            return $next($request);
        }

        // Second, try Sanctum token authentication
        $token = $request->bearerToken();
        if ($token) {
            $accessToken = PersonalAccessToken::findToken($token);
            if ($accessToken) {
                // User is authenticated via token
                $request->setUserResolver(fn () => $accessToken->tokenable);
                return $next($request);
            }
        }

        // No authentication found
        return response()->json(['message' => 'Unauthenticated.'], 401);
    }
}
