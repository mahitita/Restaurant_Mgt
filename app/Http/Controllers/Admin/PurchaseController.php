<?php

namespace App\Http\Controllers\Admin;

use Inertia\Inertia;
use App\Models\Purchase;
use App\Models\Inventory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PurchaseController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'inventory_id' => 'required|exists:inventories,id',
            'quantity' => 'required|integer|min:1',
            'cost' => 'required|numeric|min:0',
            'supplier' => 'nullable|string|max:255',
        ]);

        $purchase = Purchase::create($request->all());
        $inventory = Inventory::findOrFail($request->inventory_id);
        $inventory->addStock($request->quantity); // Using the model's method

        return redirect()->back()->with('success', 'Purchase recorded and stock updated.');
    }
}