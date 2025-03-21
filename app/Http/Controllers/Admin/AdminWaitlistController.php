<?php

namespace App\Http\Controllers\Admin;

use Inertia\Inertia;
use App\Models\Table;
use App\Models\Waitlist;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AdminWaitlistController extends Controller
{
    public function index()
    {
        $waitlists = Waitlist::with(['user', 'table'])
            ->orderBy('added_at', 'asc')
            ->get()
            ->map(function ($waitlist) {
                return [
                    'id' => $waitlist->id,
                    'user_name' => $waitlist->user ? $waitlist->user->name : 'Guest',
                    'party_size' => $waitlist->party_size,
                    'preferred_table' => $waitlist->table ? $waitlist->table->table_number : 'Any',
                    'added_at' => $waitlist->added_at->toDateTimeString(),
                    'status' => $waitlist->status,
                    'notified_at' => $waitlist->notified_at ? $waitlist->notified_at->toDateTimeString() : null,
                ];
            });

        $tables = Table::all()->map(function ($table) {
            return [
                'id' => $table->id,
                'table_number' => $table->table_number,
                'seats' => $table->seats,
            ];
        });

        return Inertia::render('Admin/Waitlists/Index', [
            'waitlists' => $waitlists,
            'tables' => $tables,
        ]);
    }

    public function update(Request $request, Waitlist $waitlist)
    {
        $request->validate([
            'status' => 'required|in:waiting,seated,cancelled',
        ]);

        $waitlist->update([
            'status' => $request->status,
            'notified_at' => $request->status === 'seated' ? now() : $waitlist->notified_at,
        ]);

        return redirect()->route('admin.waitlists.index')
            ->with('success', "Waitlist for {$waitlist->user->name} updated to {$request->status}.");
    }
}
