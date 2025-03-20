<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\Table;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;

class ReservationController extends Controller
{
    public function index(Request $request)
    {
        $reservations = Reservation::with(['table', 'user'])
            ->orderBy('reservation_time', 'desc')
            ->get()
            ->map(function ($reservation) {
                return [
                    'id' => $reservation->id,
                    'table_number' => $reservation->table->table_number,
                    'user_name' => $reservation->user ? $reservation->user->name : 'Guest',
                    'reservation_time' => $reservation->reservation_time->toDateTimeString(),
                    'status' => $reservation->status,
                ];
            });

        return Inertia::render('Admin/Reservations/Index', [
            'reservations' => $reservations,
        ]);
    }

    public function edit(Reservation $reservation)
    {
        $reservation->load('table', 'user');
        $tables = Table::all()->map(function ($table) {
            return [
                'id' => $table->id,
                'table_number' => $table->table_number,
            ];
        });

        return Inertia::render('Admin/Reservations/Edit', [
            'reservation' => [
                'id' => $reservation->id,
                'table_id' => $reservation->table->id,
                'table_number' => $reservation->table->table_number,
                'user_name' => $reservation->user ? $reservation->user->name : 'Guest',
                'reservation_time' => $reservation->reservation_time->toDateTimeString(),
                'status' => $reservation->status,
            ],
            'tables' => $tables,
        ]);
    }

    public function update(Request $request, Reservation $reservation)
    {
        $validated = $request->validate([
            'table_id' => 'required|exists:tables,id',
            'reservation_time' => 'required|date|after:now',
            'status' => 'required|in:pending,confirmed,cancelled',
        ]);

        $newTable = Table::find($validated['table_id']);
        $reservationDate = Carbon::parse($validated['reservation_time'])->toDateString();

        // Check availability of new table for the date
        if ($newTable->id !== $reservation->table_id) {
            $existingReservation = $newTable->reservations()
                ->where('status', 'confirmed')
                ->where('id', '!=', $reservation->id)
                ->whereDate('reservation_time', $reservationDate)
                ->exists();
            if ($existingReservation) {
                return redirect()->back()->with('error', "Table {$newTable->table_number} is already reserved on {$reservationDate}.");
            }
        }

        $reservation->update($validated);

        // Update table status based on reservation
        $this->syncTableStatus($reservation->table, $reservationDate);

        return redirect()->route('admin.reservations.index')->with('success', 'Reservation updated successfully.');
    }

    public function destroy(Reservation $reservation)
    {
        $reservationDate = $reservation->reservation_time->toDateString();
        $reservation->status = 'cancelled';
        $reservation->save();

        $this->syncTableStatus($reservation->table, $reservationDate);

        return redirect()->route('admin.reservations.index')->with('success', 'Reservation cancelled successfully.');
    }

    protected function syncTableStatus(Table $table, $date)
    {
        $hasConfirmedReservation = $table->reservations()
            ->where('status', 'confirmed')
            ->whereDate('reservation_time', $date)
            ->exists();

        $table->status = $hasConfirmedReservation ? 'reserved' : 'available';
        $table->save();
    }
}