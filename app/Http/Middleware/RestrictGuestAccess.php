<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class RestrictGuestAccess
{
    public function handle(Request $request, Closure $next)
    {
        $allowedRoutes = [
            'home',
            'menu.index',
            'tables.index',
            'orders.cart',
            'user.login',
            'user.register',
            'user.login.store',
        ];

        $isAuthenticated = Auth::guard('web')->check();
        $currentRoute = $request->route() ? $request->route()->getName() : null;

        Log::info('RestrictGuestAccess middleware', [
            'is_authenticated' => $isAuthenticated,
            'user_id' => Auth::guard('web')->id(),
            'route' => $currentRoute,
            'url' => $request->fullUrl(),
        ]);

        if (!$isAuthenticated && !in_array($currentRoute, $allowedRoutes)) {
            Log::info('Redirecting to user login', ['return_to' => $request->fullUrl()]);
            return redirect()->route('user.login', ['return_to' => $request->fullUrl()]);
        }

        return $next($request);
    }
}