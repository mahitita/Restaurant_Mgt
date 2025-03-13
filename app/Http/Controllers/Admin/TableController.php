<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Table;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TableController extends Controller
{
    public function index()
    {
        $tables = Table::all()->map(function ($table) {
            return [
                'id' => $table->id,
                'table_number' => $table->table_number,
                'seats' => $table->seats,
                'status' => $table->status,
            ];
        });

        return Inertia::render('Admin/Tables/Index', [
            'tables' => $tables,
        ]);
    }


    public function updateStatus(Request $request, Table $table)
    {
        $request->validate([
            'status' => 'required|in:available,occupied,reserved',
        ]);

        $table->status = $request->status;
        $table->save();

        if ($request->status === 'available') {
            $reservation = $table->reservations()
                ->where('status', 'confirmed')
                ->whereDate('reservation_time', now()->toDateString())
                ->first();
            if ($reservation) {
                $reservation->status = 'cancelled';
                $reservation->save();
            }
        }

        return redirect()->route('admin.tables.index')->with('success', 'Table status updated.');
    }
}
