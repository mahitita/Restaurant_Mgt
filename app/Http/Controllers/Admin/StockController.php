<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Stock;
use Illuminate\Http\Request;
use Inertia\Inertia;

class StockController extends Controller
{
    public function index()
    {
        $stocks = Stock::all();
        return Inertia::render('Admin/Stocks/Index', ['stocks' => $stocks]);
    }

    public function create()
    {
        return Inertia::render('Admin/Stocks/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:stocks,name',
            'quantity' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        Stock::create($request->only(['name', 'quantity', 'price', 'description']));

        return redirect()->route('admin.stocks.index')->with('success', 'Stock item created successfully.');
    }

    public function edit(Stock $stock)
    {
        return Inertia::render('Admin/Stocks/Edit', ['stock' => $stock]);
    }

    public function update(Request $request, Stock $stock)
    {
        $request->validate([
            'name' => 'required|string|unique:stocks,name,' . $stock->id,
            'quantity' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        $stock->update($request->only(['name', 'quantity', 'price', 'description']));

        return redirect()->route('admin.stocks.index')->with('success', 'Stock item updated successfully.');
    }

    public function destroy(Stock $stock)
    {
        $stock->delete();
        return redirect()->route('admin.stocks.index')->with('success', 'Stock item deleted successfully.');
    }
}