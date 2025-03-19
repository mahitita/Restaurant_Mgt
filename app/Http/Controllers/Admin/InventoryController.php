<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Inventory;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function index()
    {
        $inventories = Inventory::all()->map(function ($inventory) {
            $inventory->low_stock = $inventory->isLowStock();
            $inventory->expired = $inventory->isExpired();
            return $inventory;
        });
        return inertia('Admin/Inventory/Index', ['inventories' => $inventories]);
    }

    public function create()
    {
        return inertia('Admin/Inventory/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:inventories,name',
            'quantity' => 'required|integer|min:0',
            'unit_cost' => 'required|numeric|min:0',
            'unit' => 'required|string',
            'threshold' => 'required|integer|min:1',
            'expiry_date' => 'nullable|date|after:today',
        ]);

        Inventory::create($validated);

        return redirect()->route('admin.inventory.index')->with('success', 'Inventory item added.');
    }

    public function edit(Inventory $inventory)
    {
        return inertia('Admin/Inventory/Edit', ['inventory' => $inventory]);
    }

    public function update(Request $request, Inventory $inventory)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:inventories,name,' . $inventory->id,
            'quantity' => 'required|integer|min:0',
            'unit_cost' => 'required|numeric|min:0',
            'unit' => 'required|string',
            'threshold' => 'required|integer|min:1',
            'expiry_date' => 'nullable|date|after:today',
        ]);

        $inventory->update($validated);

        return redirect()->route('admin.inventory.index')->with('success', 'Inventory item updated.');
    }

    public function destroy(Inventory $inventory)
    {
        if ($inventory->menus()->exists()) {
            return redirect()->back()->with('error', 'Cannot delete inventory item linked to menus.');
        }

        $inventory->delete();
        return redirect()->route('admin.inventory.index')->with('success', 'Inventory item deleted.');
    }

    public function addStock(Request $request, Inventory $inventory)
    {
        $validated = $request->validate([
            'amount' => 'required|integer|min:1',
        ]);

        $inventory->addStock($validated['amount']);

        return redirect()->route('admin.inventory.index')->with('success', "Added {$validated['amount']} to {$inventory->name}.");
    }
}