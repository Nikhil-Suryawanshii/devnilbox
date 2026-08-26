<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateOptionalSanctum
{
    /**
     * Authenticate API user when a valid Bearer token is present (optional).
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->bearerToken()) {
            $accessToken = PersonalAccessToken::findToken($request->bearerToken());

            if ($accessToken?->tokenable) {
                Auth::guard('api')->setUser($accessToken->tokenable);
            }
        }

        return $next($request);
    }
}
