<?php

namespace App\Http\Controllers\Admin;

use Inertia\Inertia;
use App\Models\Inventory;
use Illuminate\Http\Request;
use App\Events\InventoryUpdated;
use App\Http\Controllers\Controller;

class InventoryController extends Controller
{
    public function index()
    {
        $inventory = Inventory::with('purchases')->get();
        return Inertia::render('Admin/Inventory/Index', ['inventory' => $inventory]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:inventories,name',
            'quantity' => 'required|integer|min:0',
            'threshold' => 'required|integer|min:1',
            'expiry_date' => 'nullable|date|after:today',
        ]);

        $inventory = Inventory::create($validated);
         broadcast(new InventoryUpdated($inventory))->toOthers();
        return redirect()->back()->with('success', 'Inventory item added.');
    }

    public function update(Request $request, Inventory $inventory)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:inventories,name,' . $inventory->id,
            'quantity' => 'required|integer|min:0',
            'threshold' => 'required|integer|min:1',
            'expiry_date' => 'nullable|date|after:today',
        ]);

        $inventory->update($validated);
        // broadcast(new InventoryUpdated($inventory))->toOthers();

        return redirect()->back()->with('success', 'Inventory updated.');
    }

    public function destroy(Inventory $inventory)
    {
        $inventory->delete();
        // broadcast(new InventoryUpdated($inventory))->toOthers();

        return redirect()->back()->with('success', 'Inventory item deleted.');
    }
}