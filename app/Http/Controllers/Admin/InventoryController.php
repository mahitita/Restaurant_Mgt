<?php

namespace App\Http\Controllers\Admin;

use App\Models\Inventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

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
            'quantity' => 'required|integer|min:0', // Validated as integer
            'unit_cost' => 'required|numeric|min:0',
            'unit' => 'required|string',
            'threshold' => 'required|integer|min:1',
            'expiry_date' => 'nullable|date|after:today',
        ]);

        $inventoryData = array_merge($validated, [
            'quantity' => (string) $validated['quantity'],
            'remaining_quantity' => (string) $validated['quantity'],
            'initial_stock' => (string) $validated['quantity'],
            'total_stock_added' => (string) $validated['quantity'],
        ]);

        Log::info('Validated input for inventory creation:', $inventoryData);

        $inventory = Inventory::create($inventoryData);

        Log::info('Inventory created:', $inventory->toArray());

        if ($validated['quantity'] > 0) {
            $inventory->logs()->create([
                'action' => 'added',
                'quantity' => (string) $validated['quantity'],
                'reason' => 'Initial stock added',
            ]);
        }

        return redirect()->route('admin.inventory.index')->with('success', 'Inventory item added.');
    }

    public function stockHistory()
    {
        $inventories = Inventory::with('logs')->get()->map(function ($inventory) {
            Log::info('Stock history for:', [
                'name' => $inventory->name,
                'quantity' => $inventory->quantity,
                'remaining_quantity' => $inventory->remaining_quantity,
                'initial_stock' => $inventory->initial_stock ?? 'Not set',
                'total_stock_added' => $inventory->total_stock_added ?? 'Not set',
                'log_count' => $inventory->logs->count(),
            ]);

            return [
                'id' => $inventory->id,
                'name' => $inventory->name,
                'quantity' => $inventory->quantity,
                'remaining_quantity' => $inventory->remaining_quantity,
                'unit' => $inventory->unit,
                'initial_stock' => $inventory->initial_stock ?? 0,
                'total_stock_added' => $inventory->total_stock_added ?? 0,
                'logs' => $inventory->logs->map(fn($log) => [
                    'action' => $log->action,
                    'quantity' => $log->quantity,
                    'reason' => $log->reason ?? 'No reason provided',
                    'created_at' => $log->created_at->toDateTimeString(),
                ]),
            ];
        });

        return inertia('Admin/Inventory/StockHistory', ['inventories' => $inventories]);
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

        $quantityChanged = $inventory->quantity !== $validated['quantity'];
        if ($quantityChanged) {
            $validated['remaining_quantity'] = $validated['quantity'];
            $validated['initial_stock'] = $validated['quantity'];
            $validated['total_stock_added'] = $validated['quantity'];
        }

        $inventory->update($validated);

        Log::info('Inventory updated:', [
            'name' => $inventory->name,
            'quantity' => $inventory->quantity,
            'remaining_quantity' => $inventory->remaining_quantity,
            'initial_stock' => $inventory->initial_stock,
            'total_stock_added' => $inventory->total_stock_added,
        ]);

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
            'total_cost' => 'required|numeric|min:0',
            'supplier' => 'nullable|string|max:255',
        ]);

        $originalQuantity = $inventory->quantity;
        $originalRemaining = $inventory->remaining_quantity;
        $originalUnitCost = $inventory->unit_cost;

        Log::info('Adding stock:', [
            'inventory' => $inventory->name,
            'original_quantity' => $originalQuantity,
            'original_remaining' => $originalRemaining,
            'amount' => $validated['amount'],
        ]);

        $inventory->increment('quantity', $validated['amount']);
        $inventory->increment('remaining_quantity', $validated['amount']);
        $inventory->increment('total_stock_added', $validated['amount']); 

        $inventory->purchases()->create([
            'quantity' => $validated['amount'],
            'cost' => $validated['total_cost'],
            'supplier' => $validated['supplier'],
        ]);

        $inventory->logs()->create([
            'action' => 'added',
            'quantity' => (string) $validated['amount'],
            'reason' => 'Stock added manually' . ($validated['supplier'] ? " by supplier: {$validated['supplier']}" : ''),
        ]);

        $existingValue = $originalQuantity * $originalUnitCost;
        $newValue = $validated['total_cost'];
        $newTotalQuantity = $originalQuantity + $validated['amount'];
        $inventory->unit_cost = $newTotalQuantity > 0 ? ($existingValue + $newValue) / $newTotalQuantity : 0;

        $inventory->save();

        Log::info('Stock added:', [
            'inventory' => $inventory->name,
            'new_quantity' => $inventory->quantity,
            'new_remaining' => $inventory->remaining_quantity,
            'new_unit_cost' => $inventory->unit_cost,
        ]);

        return redirect()->route('admin.inventory.index')->with('success', 'Stock added successfully.');
    }

    public function purchaseHistory(Inventory $inventory)
    {
        $purchases = $inventory->purchases()->orderBy('purchased_at', 'desc')->get();
        return inertia('Admin/Inventory/PurchaseHistory', [
            'inventory' => $inventory,
            'purchases' => $purchases,
        ]);
    }
}