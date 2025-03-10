<?php

namespace App\Http\Controllers\User;

use Inertia\Inertia;
use App\Models\Table;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserTableController extends Controller
{
    /**
     * Display a listing of the available tables.
     */
    public function index()
    {
        $tables = Table::all();
        return Inertia::render('Tables', [
            'tables' => $tables
        ]);
    }

    /**
     * Store a newly selected table in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'table_id' => 'required|exists:tables,id',
        ]);

        $table = Table::find($request->table_id);

        if ($table->status != 'available') {
            return redirect()->back()->with('error', 'This table is not available!');
        }

        $table->status = 'reserved';
        $table->save();

        return redirect()->route('tables.index')->with('success', 'Table reserved successfully!');
    }
}
