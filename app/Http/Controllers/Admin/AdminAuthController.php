<?php

namespace App\Http\Controllers\Admin;

use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class AdminAuthController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware('guest:admin')->except('logout');
    // }
    public function showLoginForm()
    {
        return inertia('Admin/Auth/AdminLogin');
    }


    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::guard('admin')->attempt($credentials)) {
            $user = Auth::guard('admin')->user();
            if (in_array($user->role, ['admin', 'waiter', 'cashier', 'chef'])) {
                $request->session()->regenerate();
                Log::info('Admin login successful', [
                    'user' => $user->id,
                    'session_id' => $request->session()->getId(),
                ]);

                return Inertia::location(route('admin.dashboard')); 
            }
            Auth::guard('admin')->logout();
            return back()->withErrors(['email' => 'Only admins can log in here.']);
        }

        Log::info('Admin login failed', ['credentials' => $credentials]);
        return back()->withErrors(['email' => 'Invalid credentials']);
    }

    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('admin.login');
    }
}