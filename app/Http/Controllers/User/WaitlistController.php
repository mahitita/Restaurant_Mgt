<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Waitlist;
use App\Models\Table;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class WaitlistController extends Controller
{
    public function index()
    {
        $waitlist = Waitlist::where('user_id', Auth::id())->where('status', 'waiting')->first();
        $availableTables = Table::all()->filter(fn($t) => $t->isAvailable(now()))->count();
        $averageTurnover = 30; // Assume 30 minutes per table turnover (adjust based on data)

        return Inertia::render('Waitlist/Index', [
            'waitlist' => $waitlist ? [
                'id' => $waitlist->id,
                'party_size' => $waitlist->party_size,
                'estimated_wait_minutes' => $waitlist->estimated_wait_minutes,
            ] : null,
            'availableTables' => $availableTables,
            'averageTurnover' => $averageTurnover,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'party_size' => 'required|integer|min:1',
        ]);

        $existing = Waitlist::where('user_id', Auth::id())->where('status', 'waiting')->first();
        if ($existing) {
            return redirect()->back()->with('error', 'You are already on the waitlist.');
        }

        $tablesInUse = Table::where('status', 'occupied')->count();
        $totalTables = Table::count();
        $waitlistCount = Waitlist::where('status', 'waiting')->count();
        $estimatedWait = $totalTables > 0 ? max(0, ($tablesInUse + $waitlistCount) * 30 / $totalTables) : 30;

        $waitlist = Waitlist::create([
            'user_id' => Auth::id(),
            'party_size' => $request->party_size,
            'estimated_wait_minutes' => $estimatedWait,
        ]);

        return redirect()->route('waitlist.index')->with('success', 'Added to waitlist!');
    }
}
