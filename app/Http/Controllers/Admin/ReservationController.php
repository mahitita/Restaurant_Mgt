<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\Table;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReservationController extends Controller
{
    // Show all reservations
    public function index()
    {
        $reservations = Reservation::with('user', 'table')->latest()->get();
        return inertia('admin/reservations/Index', compact('reservations'));
    }

    // Store a new reservation
    public function store(Request $request)
    {
        $request->validate([
            'table_id' => 'required|exists:tables,id',
            'reservation_time' => 'required|date|after:now',
        ]);

        $table = Table::find($request->table_id);

        if ($table->status === 'reserved') {
            return back()->with('error', 'This table is already reserved.');
        }

        $reservation = Reservation::create([
            'user_id' => Auth::id(),
            'table_id' => $request->table_id,
            'reservation_time' => $request->reservation_time,
            'status' => 'pending',
        ]);

        // Mark table as reserved
        $table->update(['status' => 'reserved']);

        return redirect()->route('admin.reservations.index')->with('success', 'Reservation placed successfully!');
    }

    // Update reservation status
    public function updateStatus(Request $request, Reservation $reservation)
    {
        $request->validate(['status' => 'required|in:pending,confirmed,cancelled']);
        $reservation->update(['status' => $request->status]);

        // Update table status if reservation is cancelled
        if ($request->status === 'cancelled') {
            $reservation->table->update(['status' => 'available']);
        }

        return back()->with('success', 'Reservation status updated.');
    }

    // Delete a reservation
    public function destroy(Reservation $reservation)
    {
        $reservation->table->update(['status' => 'available']);
        $reservation->delete();
        return back()->with('success', 'Reservation deleted.');
    }
}

