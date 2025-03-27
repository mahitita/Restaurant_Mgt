<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Waitlist;
use App\Models\Table;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class WaitlistController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'party_size' => 'required|integer|min:1',
            'reservation_time' => 'required|date|after:now',
            'preferred_table_id' => 'nullable|exists:tables,id', // Optional preferred table
        ]);

        $reservationTime = \Carbon\Carbon::parse($request->reservation_time);
        $partySize = $request->party_size;
        $preferredTableId = $request->preferred_table_id;

        $availableTables = Table::where('seats', '>=', $partySize)
            ->whereDoesntHave('reservations', function ($query) use ($reservationTime) {
                $query->where('status', 'confirmed')
                      ->whereDate('reservation_time', $reservationTime->toDateString());
            })
            ->get();

        if ($availableTables->isNotEmpty()) {
            return redirect()->back()->with('error', 'Tables are available! Please reserve one instead.');
        }

        // Check if user is already on waitlist for this date
        $existingWaitlist = Waitlist::where('user_id', Auth::id())
            ->whereDate('added_at', $reservationTime->toDateString())
            ->where('status', 'waiting')
            ->exists();
        if ($existingWaitlist) {
            return redirect()->back()->with('error', 'You’re already on the waitlist for this date.');
        }

        $waitlist = Waitlist::create([
            'user_id' => Auth::id(),
            'party_size' => $partySize,
            'table_id' => $preferredTableId,
            'estimated_wait_minutes' => $this->calculateEstimatedWaitTime($partySize, $reservationTime),
            'status' => 'waiting',
        ]);

        return redirect()->route('reservations.index')
            ->with('success', "Added to waitlist! Estimated wait: {$waitlist->estimated_wait_minutes} minutes.");
    }

    private function calculateEstimatedWaitTime($partySize, $reservationTime)
    {
        $currentReservations = Reservation::where('status', 'confirmed')
            ->whereDate('reservation_time', $reservationTime->toDateString())
            ->count();

        $tablesForPartySize = Table::where('seats', '>=', $partySize)->count();
        $waitFactor = $currentReservations / max(1, $tablesForPartySize);
        $baseWait = 15;
        $additionalWait = $waitFactor * 10;
        return round($baseWait + $additionalWait);
    }

    public function notifyNext(Request $request)
    {
        $nextWaitlist = Waitlist::where('status', 'waiting')
            ->orderBy('added_at', 'asc')
            ->first();

        if ($nextWaitlist) {
            $nextWaitlist->update([
                'notified_at' => now(),
                'status' => 'seated',
            ]);
            return response()->json(['message' => "Notified user {$nextWaitlist->user_id}"]);
        }

        return response()->json(['message' => 'No one on waitlist']);
    }

    public function destroy(Waitlist $waitlist)
{
    if ($waitlist->user_id !== Auth::id()) {
        return redirect()->back()->with('error', 'Unauthorized action.');
    }

    $waitlist->update(['status' => 'cancelled']);
    return redirect()->back()->with('success', 'Waitlist entry cancelled.');
}
}