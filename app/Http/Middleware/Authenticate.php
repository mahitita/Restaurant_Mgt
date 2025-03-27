<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    protected function redirectTo(Request $request): ?string
    {
        if ($request->expectsJson()) {
            return null;
        }

        $path = $request->getPathInfo();
        if (str_starts_with($path, '/admin')) {
            return route('admin.login');
        }

        return route('user.login');
    }

    protected function authenticate($request, array $guards)
    {
        if (empty($guards)) {
            $guards = [null]; // Default guard
        }

        foreach ($guards as $guard) {
            if ($this->auth->guard($guard)->check()) {
                return $this->auth->shouldUse($guard);
            }
        }

        $this->unauthenticated($request, $guards);
    }
}