<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Table;
use App\Models\Reservation;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Inertia\Inertia;

class UserTableController extends Controller
{

    public function index(Request $request)
    {

        $tables = Table::all();



        $dateTime = Carbon::parse($request->input('date_time', now()->addHour()));
        $tables = Table::all()->map(function ($table) use ($dateTime) {
            $table->available = $table->isAvailable($dateTime);
            return $table;
        });

        return Inertia::render('Tables', [
            'tables' => $tables,
            'selectedDateTime' => $dateTime->toDateTimeString(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'table_ids' => 'required|array|min:1',
            'table_ids.*' => 'required|exists:tables,id',
            'reservation_time' => 'required|date|after:now',
            'payment.paymentType' => 'required|in:card,bank_transfer',
            'payment.accountNumber' => 'required|string|max:255',
        ]);

        $tables = Table::findMany($request->table_ids);
        $reservationTime = Carbon::parse($request->reservation_time);

        foreach ($tables as $table) {
            if (!$table->isAvailable($reservationTime)) {
                return redirect()->back()->with('error', "Table {$table->table_number} is already reserved for this date.");
            }
        }

        return DB::transaction(function () use ($request, $tables, $reservationTime) {
            $reservations = [];
            foreach ($tables as $table) {
                $reservations[] = Reservation::create([
                    'user_id' => Auth::id(),
                    'table_id' => $table->id,
                    'reservation_time' => $reservationTime,
                    'status' => 'pending',
                ]);
            }

            $depositPerTable = 10.00;
            $totalDeposit = $depositPerTable * $tables->count();
            $paymentMethodMap = [
                'card' => 'cbe_birr',
                'bank_transfer' => 'amole',
            ];
            $frontendPaymentType = $request->input('payment.paymentType');
            $backendPaymentMethod = $paymentMethodMap[$frontendPaymentType] ?? 'cash';

            $payment = Payment::create([
                'reservation_id' => $reservations[0]->id,
                'payment_method' => $backendPaymentMethod,
                'amount' => 0,
                'deposit_amount' => $totalDeposit,
                'paid_at' => now(),
                'status' => 'paid',
            ]);

            foreach ($reservations as $reservation) {
                $reservation->status = 'confirmed';
                $reservation->save();
            }

            return redirect()->route('reservations.index')->with('success', 'Tables reserved with deposit!');
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
