<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use Inertia\Inertia;
use App\Models\Table;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TableController extends Controller
{
    public function index()
    {
        $tables = Table::with('reservations')->get()->map(function ($table) {
            $todayReservation = $table->reservations()
                ->where('status', 'confirmed')
                ->whereDate('reservation_time', now()->toDateString())
                ->first();
            return [
                'id' => $table->id,
                'table_number' => $table->table_number,
                'seats' => $table->seats,
                'type' => $table->type,
                'x_coordinate' => $table->x_coordinate,
                'y_coordinate' => $table->y_coordinate,
                'width' => $table->width,
                'height' => $table->height,
                'status' => $table->status,
                'reserved_today' => $todayReservation ? true : false,
            ];
        });

        return Inertia::render('Admin/Tables/Index', [
            'tables' => $tables,
            'reservationsRoute' => route('admin.reservations.index'),
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Tables/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'table_number' => 'required|string|unique:tables,table_number',
            'seats' => 'required|integer|min:1',
            'width' => 'required|integer|min:50',
            'height' => 'required|integer|min:50',
            'type' => 'required|in:rectangle,round,oval,square',
        ]);

        $lastTable = Table::latest()->first();
        $x = $lastTable ? $lastTable->x_coordinate + 150 : 50;
        $y = $lastTable ? $lastTable->y_coordinate : 50;

        Table::create([
            'table_number' => $validated['table_number'],
            'seats' => $validated['seats'],
            'x_coordinate' => $x,
            'y_coordinate' => $y,
            'width' => $validated['width'],
            'height' => $validated['height'],
            'type' => $validated['type'],
        ]);

        return redirect()->route('admin.tables.index')->with('success', 'Table added successfully.');
    }

    public function edit(Table $table)
    {
        return Inertia::render('Admin/Tables/Edit', ['table' => $table]);
    }

    public function update(Request $request, Table $table)
    {
        $validated = $request->validate([
            'table_number' => 'required|string|unique:tables,table_number,' . $table->id,
            'seats' => 'required|integer|min:1',
            'x_coordinate' => 'required|integer',
            'y_coordinate' => 'required|integer',
            'width' => 'required|integer|min:50',
            'height' => 'required|integer|min:50',
            'type' => 'required|in:rectangle,round,oval,square',
        ]);

        $table->update($validated);

        return redirect()->route('admin.tables.index')->with('success', 'Table updated successfully.');
    }

    public function destroy(Table $table)
    {
        $table->delete();
        return redirect()->route('admin.tables.index')->with('success', 'Table deleted successfully.');
    }
    public function updateStatus(Request $request, Table $table)
    {
        $validated = $request->validate([
            'status' => 'required|in:available,occupied,reserved',
            'date' => 'nullable|date', // Optional date to check; defaults to today
        ]);

        $checkDate = $request->date ? \Carbon\Carbon::parse($request->date) : now();
        $reservationDate = $checkDate->toDateString();

        $reservation = $table->reservations()
            ->where('status', 'confirmed')
            ->whereDate('reservation_time', $reservationDate)
            ->first();

        if ($validated['status'] === 'available' && $reservation) {
            return redirect()->back()->with('error', "Table {$table->table_number} is reserved for {$reservationDate}. Cancel the reservation first.");
        }

        if ($validated['status'] === 'reserved' && !$reservation) {
            return redirect()->back()->with('error', "Table {$table->table_number} has no confirmed reservation for {$reservationDate}. Create a reservation first.");
        }

        $table->status = $validated['status'];
        $table->save();

        return redirect()->route('admin.tables.index')->with('success', 'Table status updated.');
    }


}