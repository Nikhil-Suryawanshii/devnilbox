<?php

namespace App\Http\Middleware;

use App\Enums\Roles;
use App\Support\CustomerSellerSetup;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureCanSell
{
    /**
     * Allow shop sellers and customers (auto shop + shop role on first product API use).
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        if ($user->hasRole(Roles::ROOT->value) || $user->hasRole(Roles::SHOP->value)) {
            return $next($request);
        }

        if ($user->hasRole(Roles::CUSTOMER->value)) {
            CustomerSellerSetup::ensure($user);

            return $next($request);
        }

        return response()->json(['message' => 'User does not have the right roles.'], 403);
    }
}
