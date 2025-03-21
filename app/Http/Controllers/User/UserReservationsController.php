<?php

namespace App\Http\Controllers\User;

use Inertia\Inertia;
use App\Models\Table;
use App\Models\Waitlist;
use App\Models\Reservation;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

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

        $waitlists = Waitlist::where('user_id', Auth::id())
            ->orderBy('added_at', 'desc')
            ->get()
            ->map(function ($waitlist) {
                return [
                    'id' => $waitlist->id,
                    'party_size' => $waitlist->party_size,
                    'added_at' => $waitlist->added_at->toDateTimeString(),
                    'estimated_wait_minutes' => $waitlist->estimated_wait_minutes,
                    'status' => $waitlist->status,
                    'table_id' => $waitlist->table_id,
                ];
            });

        $tables = Table::all()->map(function ($table) {
            return [
                'id' => $table->id,
                'table_number' => $table->table_number,
                'seats' => $table->seats,
            ];
        });

        return Inertia::render('Reservations/Index', [
            'reservations' => $reservations,
            'waitlists' => $waitlists,
            'tables' => $tables,
        ]);
    }
    public function confirmFromWaitlist(Request $request, $waitlistId)
    {
        Log::info('confirmFromWaitlist called with method: ' . $request->method() . ' for waitlist ID: ' . $waitlistId);
        $waitlist = Waitlist::where('user_id', Auth::id())->findOrFail($waitlistId);
    
        if ($waitlist->status !== 'seated') {
            return redirect()->back()->with('error', 'This waitlist entry is not ready to be confirmed.');
        }
    
        $tableId = $waitlist->table_id ?? Table::where('seats', '>=', $waitlist->party_size)
            ->whereDoesntHave('reservations', function ($query) use ($waitlist) {
                $query->where('status', 'confirmed')
                      ->whereDate('reservation_time', \Carbon\Carbon::parse($waitlist->added_at)->toDateString());
            })
            ->firstOrFail()->id;
    
        $table = Table::findOrFail($tableId);
    
        return Inertia::render('Reservations/ConfirmFromWaitlist', [
            'waitlist' => [
                'id' => $waitlist->id,
                'party_size' => $waitlist->party_size,
                'reservation_time' => $waitlist->added_at->toDateTimeString(),
            ],
            'table' => [
                'id' => $table->id,
                'table_number' => $table->table_number,
                'seats' => $table->seats,
            ],
        ]);
    }

    public function storeFromWaitlist(Request $request, $waitlistId)
    {
        $waitlist = Waitlist::where('user_id', Auth::id())->findOrFail($waitlistId);
    
        if ($waitlist->status !== 'seated') {
            return redirect()->back()->with('error', 'This waitlist entry is not ready to be confirmed.');
        }
    
        $request->validate([
            'table_id' => 'required|exists:tables,id',
            'payment.paymentType' => 'required|in:card,bank_transfer',
            'payment.accountNumber' => 'required|string',
        ]);
    
        $table = Table::findOrFail($request->table_id);
        $isTableAvailable = !$table->reservations()
            ->where('status', 'confirmed')
            ->whereDate('reservation_time', \Carbon\Carbon::parse($waitlist->added_at)->toDateString())
            ->exists();
    
        if (!$isTableAvailable) {
            return redirect()->back()->with('error', "Table {$table->table_number} is no longer available.");
        }
    
        $reservation = Reservation::create([
            'user_id' => Auth::id(),
            'table_id' => $request->table_id,
            'reservation_time' => $waitlist->added_at,
            'status' => 'confirmed',
        ]);
    
        $reservation->payment()->create([
            'deposit_amount' => 10,
            'payment_type' => $request->input('payment.paymentType'),
            'account_number' => $request->input('payment.accountNumber'),
        ]);
    
        $waitlist->update(['status' => 'cancelled']);
    
        return redirect()->route('reservations.index')->with('success', 'Table reserved successfully!');
    }
}
