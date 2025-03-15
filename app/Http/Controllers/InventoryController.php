<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\InventoryLog;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function index()
    {
        $inventories = Inventory::all();
        return view('inventory.index', compact('inventories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'quantity' => 'required|integer|min:0',
            'threshold' => 'required|integer|min:0',
            'expiry_date' => 'nullable|date',
        ]);

        $inventory = Inventory::create($request->all());
        $this->logAction($inventory->id, 'added', $request->quantity);

        return back()->with('success', 'Inventory item added!');
    }

    public function update(Request $request, Inventory $inventory)
    {
        $request->validate(['quantity' => 'required|integer|min:0']);
        $difference = $request->quantity - $inventory->quantity;

        $inventory->update($request->all());
        $this->logAction($inventory->id, $difference > 0 ? 'added' : 'deducted', abs($difference));

        return back()->with('success', 'Inventory updated!');
    }

    private function logAction($inventoryId, $action, $quantity)
    {
        InventoryLog::create([
            'inventory_id' => $inventoryId,
            'action' => $action,
            'quantity' => $quantity,
        ]);
    }

    public function dashboard()
{
    $inventories = Inventory::all();
    return view('admin.inventory.index', compact('inventories'));
}

}
