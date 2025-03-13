<?php

namespace App\Http\Controllers\User;

use Carbon\Carbon;
use Inertia\Inertia;
use App\Models\Table;
use App\Models\Payment;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class UserTableController extends Controller
{
    public function index(Request $request)
    {
        $dateTime = Carbon::parse($request->input('date_time', now()->addHour()));
        $tables = Table::all()->map(function ($table) use ($dateTime) {
            $table->available = $table->isAvailable($dateTime);
            return $table;
        });

        Log::info("Tables for {$dateTime->toDateTimeString()}: " . $tables->toJson());

        return Inertia::render('Tables', [
            'tables' => $tables,
            'selectedDateTime' => $dateTime->toDateTimeString(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'table_id' => 'required|exists:tables,id',
            'reservation_time' => 'required|date|after:now',
            'payment.paymentType' => 'required|in:card,bank_transfer',
            'payment.accountNumber' => 'required|string|max:255',
        ]);

        $table = Table::findOrFail($request->table_id);
        $reservationTime = Carbon::parse($request->reservation_time);

        if (!$table->isAvailable($reservationTime)) {
            return redirect()->back()->with('error', 'Table is already reserved for this date.');
        }

        return DB::transaction(function () use ($request, $table, $reservationTime) {
            $reservation = Reservation::create([
                'user_id' => Auth::id(),
                'table_id' => $table->id,
                'reservation_time' => $reservationTime,
                'status' => 'pending',
            ]);

            $deposit = 100.00;
            $paymentMethodMap = [
                'card' => 'cbe_birr',
                'bank_transfer' => 'amole',
            ];
            $frontendPaymentType = $request->input('payment.paymentType');
            $backendPaymentMethod = $paymentMethodMap[$frontendPaymentType] ?? 'cash';
            Log::info("Creating payment with method: {$backendPaymentMethod}");
            Payment::create([
                'reservation_id' => $reservation->id,
                'payment_method' => $backendPaymentMethod,
                'amount' => 0,
                'deposit_amount' => $deposit,
                'paid_at' => now(),
                'status' => 'paid',
            ]);

            $reservation->status = 'confirmed';
            $reservation->save();

            return redirect()->route('tables.index')->with('success', 'Table reserved with deposit!');
        });
    }

    public function availableTables(Request $request)
    {
        $dateTime = Carbon::parse($request->input('date_time', now()->addHour()));
        $tables = Table::all()->map(function ($table) use ($dateTime) {
            $table->available = $table->isAvailable($dateTime);
            return $table;
        });

        return response()->json($tables);
    }
}
