<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    public function index(): Response
    {
        $users = User::all();
        return Inertia::render('Admin/Users/Index', ['users' => $users]);
    }

    public function update(Request $request, User $user)
    {
        $request->validate(['role' => 'required|in:customer,manager,waiter,cashier']);
        $user->update(['role' => $request->role]);

        return redirect()->route('admin.users.index')->with('success', 'User role updated!');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'User removed!');
    }
}
