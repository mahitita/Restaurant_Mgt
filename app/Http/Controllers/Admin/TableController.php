<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Table;
use Inertia\Inertia;
use Inertia\Response;

class TableController extends Controller
{
    public function index(): Response
    {
        $tables = Table::all();
        return Inertia::render('Admin/Tables/Index', ['tables' => $tables]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'table_number' => 'required|unique:tables',
            'seats' => 'required|integer',
            'status' => 'required|in:available,reserved,occupied'
        ]);

        Table::create($request->all());
        return redirect()->route('admin.tables.index')->with('success', 'Table added!');
    }

    public function update(Request $request, Table $table)
    {
        $request->validate(['status' => 'required|in:available,reserved,occupied']);
        $table->update(['status' => $request->status]);

        return redirect()->route('admin.tables.index')->with('success', 'Table status updated!');
    }

    public function destroy(Table $table)
    {
        $table->delete();
        return redirect()->route('admin.tables.index')->with('success', 'Table removed!');
    }
}

