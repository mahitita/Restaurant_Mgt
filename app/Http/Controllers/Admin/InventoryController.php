<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Inventory;
use Inertia\Inertia;
use Inertia\Response;

class InventoryController extends Controller
{
    public function index(): Response
    {
        $items = Inventory::all();
        return Inertia::render('Admin/Inventory/Index', ['items' => $items]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'item_name' => 'required|unique:inventory',
            'quantity' => 'required|integer',
            'unit' => 'required'
        ]);

        Inventory::create($request->all());
        return redirect()->route('admin.inventory.index')->with('success', 'Inventory item added!');
    }

    public function update(Request $request, Inventory $inventory)
    {
        $request->validate(['quantity' => 'required|integer']);
        $inventory->update(['quantity' => $request->quantity]);

        return redirect()->route('admin.inventory.index')->with('success', 'Inventory updated!');
    }

    public function destroy(Inventory $inventory)
    {
        $inventory->delete();
        return redirect()->route('admin.inventory.index')->with('success', 'Item removed!');
    }
}
