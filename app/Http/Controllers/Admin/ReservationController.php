<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ReservationController extends Controller
{
    public function index(Request $request)
    {
        $date = $request->input('date', now()->toDateString());
        $reservations = Reservation::with(['table', 'user', 'payment'])
            ->orderBy('reservation_time', 'asc')
            ->get()
            ->map(function ($reservation) {
                return [
                    'id' => $reservation->id,
                    'table_number' => $reservation->table->table_number,
                    'user_name' => $reservation->user->name ?? 'Unknown',
                    'reservation_time' => $reservation->reservation_time->toDateTimeString(),
                    'status' => $reservation->status,
                    'deposit_amount' => $reservation->payment ? $reservation->payment->deposit_amount : 0,
                ];
            });

        return Inertia::render('Admin/Reservations/Index', [
            'reservations' => $reservations,
            'selectedDate' => $date,
        ]);
    }

    public function updateStatus(Request $request, Reservation $reservation)
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,cancelled',
        ]);

        $reservation->status = $request->status;
        $reservation->save();

        return redirect()->route('admin.reservations.index')->with('success', 'Reservation status updated.');
    }
}
