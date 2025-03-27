<?php

namespace App\Http\Controllers\User\Auth;

use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function create(Request $request)
    {
        Log::info('Login page accessed', ['return_to' => $request->query('return_to')]);
        return Inertia::render('User/Login', [
            'return_to' => $request->query('return_to', route('home')),
        ]);
    }

    public function store(Request $request)
    {
        Log::info('Login attempt', ['phone' => $request->phone, 'return_to' => $request->query('return_to')]);

        $credentials = $request->validate([
            'phone' => 'required|string',
            'password' => 'required',
        ]);

        if (Auth::guard('web')->attempt($credentials)) {
            $user = Auth::guard('web')->user();
            if ($user->role === 'customer') {
                $request->session()->regenerate();

                // Get the return_to URL from the request or session
                $returnTo = $request->query('return_to', session('url.intended', route('home')));
                Log::info('Login successful, redirecting', [
                    'user_id' => $user->id,
                    'return_to' => $returnTo,
                    'session' => $request->session()->all(),
                ]);

                session()->forget('url.intended');

                return Inertia::location($returnTo);
            }

            Log::warning('User is not a customer, logging out', ['user_id' => $user->id]);
            Auth::guard('web')->logout();
            return back()->withErrors(['phone' => 'Only customers can log in here.']);
        }

        Log::warning('Login failed: Invalid credentials', ['phone' => $request->phone]);
        return back()->withErrors(['phone' => 'Invalid credentials.']);
    }

    public function destroy(Request $request)
    {
        Log::info('Logout initiated');
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('user.login')->setStatusCode(303);
    }
}