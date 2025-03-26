<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::guard('admin')->check() && in_array(Auth::guard('admin')->user()->role, ['admin', 'waiter', 'cashier','chef'])) {
            return $next($request);
        }

        return redirect()->route('admin.login')->with('error', 'Unauthorized access. Admins only.');
    }
}