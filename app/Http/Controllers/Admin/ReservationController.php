<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    public function index()
    {
        $reservations = Reservation::with('user')->latest()->get();
        return inertia('Admin/Reservation/Index', ['reservations' => $reservations]);
    }

    public function show(Reservation $reservation)
    {
        return inertia('Admin/Reservation/Show', ['reservation' => $reservation->load('user')]);
    }

    public function update(Request $request, Reservation $reservation)
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,cancelled,completed',
        ]);

        $reservation->update(['status' => $request->status]);

        return redirect()->route('admin.reservations.index')->with('success', 'Reservation updated successfully.');
    }

    public function destroy(Reservation $reservation)
    {
        $reservation->delete();
        return redirect()->route('admin.reservations.index')->with('success', 'Reservation deleted successfully.');
    }
}
