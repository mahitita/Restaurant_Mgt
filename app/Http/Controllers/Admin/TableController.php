<?php

namespace App\Http\Controllers\Admin;

use Inertia\Inertia;
use App\Models\Table;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TableController extends Controller
{
    // Show all tables
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

    // Show form to create a table
    public function create()
    {
        return inertia('Admin/Tables/Create');
    }

    // Store new table
    public function store(Request $request)
    {
        $request->validate([
            'table_number' => 'required|unique:tables',
            'seats' => 'required|integer|min:1',
            'width' => 'required|integer|min:50',
            'height' => 'required|integer|min:50',
            'type' => 'required|in:rectangle,round,oval,square',
        ]);

        // Auto-calculate x, y based on the number of tables
        $lastTable = Table::latest()->first();
        $x = $lastTable ? $lastTable->x_coordinate + 150 : 50;
        $y = $lastTable ? $lastTable->y_coordinate : 50;

        Table::create([
            'table_number' => $request->table_number,
            'seats' => $request->seats,
            'x_coordinate' => $x,
            'y_coordinate' => $y,
            'width' => $request->width,
            'height' => $request->height,
            'type' => $request->type,
        ]);

        return redirect()->route('admin.tables.index')->with('success', 'Table added successfully.');
    }


    // Show edit form
    public function edit(Table $table)
    {
        return inertia('Admin/Tables/Edit', ['table' => $table]);
    }

    // Update table
    public function update(Request $request, Table $table)
    {
        $request->validate([
            'table_number' => 'required|unique:tables,table_number,' . $table->id,
            'seats' => 'required|integer|min:1',
            'x_coordinate' => 'required|integer',
            'y_coordinate' => 'required|integer',
            'width' => 'required|integer|min:50',
            'height' => 'required|integer|min:50',
            'type' => 'required|in:rectangle,round,oval,square',
        ]);

        $table->update($request->all());

        return redirect()->route('admin.tables.index')->with('success', 'Table updated successfully.');
    }

    // Delete a table
    public function destroy(Table $table)
    {
        $table->delete();
        return redirect()->route('admin.tables.index')->with('success', 'Table deleted successfully.');
    }

    // public function updateStatus(Request $request, Table $table)
    // {
    //     $request->validate([
    //         'status' => 'required|in:available,occupied,reserved',
    //     ]);

    //     $table->status = $request->status;
    //     $table->save();

    //     if ($request->status === 'available') {
    //         $reservation = $table->reservations()
    //             ->where('status', 'confirmed')
    //             ->whereDate('reservation_time', now()->toDateString())
    //             ->first();
    //         if ($reservation) {
    //             $reservation->status = 'cancelled'; // Or 'completed' if you add it
    //             $reservation->save();
    //         }
    //     }

    //     return redirect()->route('admin.tables.index')->with('success', 'Table status updated.');
    // }
}
