<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class UserReservationsController extends Controller
{
    public function index()
    {
        $reservations = Reservation::with('table', 'payment')
            ->where('user_id', Auth::id())
            ->orderBy('reservation_time', 'desc')
            ->get()
            ->map(function ($reservation) {
                return [
                    'id' => $reservation->id,
                    'table_number' => $reservation->table->table_number,
                    'seats' => $reservation->table->seats,
                    'reservation_time' => $reservation->reservation_time->toDateTimeString(),
                    'status' => $reservation->status,
                    'deposit_amount' => $reservation->payment ? $reservation->payment->deposit_amount : 0,
                ];
            });

        return Inertia::render('Reservations/Index', [
            'reservations' => $reservations,
        ]);
    }
}
