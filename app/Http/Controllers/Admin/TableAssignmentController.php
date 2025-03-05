<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TableAssignment;
use Inertia\Inertia;
use Inertia\Response;

class TableAssignmentController extends Controller
{
    public function index(): Response
    {
        $assignments = TableAssignment::with(['table', 'reservation', 'queue'])->orderBy('assigned_at', 'desc')->get();
        return Inertia::render('Admin/TableAssignments/Index', ['assignments' => $assignments]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'table_id' => 'required|exists:tables,id',
            'reservation_id' => 'nullable|exists:reservations,id',
            'queue_id' => 'nullable|exists:queues,id'
        ]);

        TableAssignment::create($request->all());
        return redirect()->route('admin.table-assignments.index')->with('success', 'Table assigned successfully!');
    }

    public function destroy(TableAssignment $tableAssignment)
    {
        $tableAssignment->delete();
        return redirect()->route('admin.table-assignments.index')->with('success', 'Table assignment removed!');
    }
}

