<?php

namespace App\Http\Controllers\Admin;

use App\Models\Menu;
use App\Models\Category;
use App\Models\Inventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class MenuController extends Controller
{
    public function index()
    {
        $menus = Menu::with(['category', 'inventories'])->get()->map(function ($menu) {
            $menu->low_stock = $menu->isLowStock();
            return $menu;
        });
        return inertia('Admin/Menu/Index', ['menus' => $menus]);
    }

    public function create()
    {
        $categories = Category::all();
        $inventories = Inventory::all();
        return inertia('Admin/Menu/Create', [
            'categories' => $categories,
            'inventories' => $inventories,
        ]);
    }

    public function store(Request $request)
    {
        Log::info('Menu Store Request:', $request->all());

        try {
            $validated = $request->validate([
                'name' => 'required|string|unique:menus,name',
                'price' => 'required|numeric|min:0',
                'category_id' => 'required|exists:categories,id',
                'description' => 'nullable|string',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'prep_time' => 'nullable|integer|min:1',
                'available' => 'required|boolean', // Accepts 1, 0, true, false
                'inventory_items' => 'nullable|string',
            ]);

            Log::info('Validated Data:', $validated);

            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('menus', 'public');
                Log::info('Image Path:', ['path' => $imagePath]);
            } else {
                $imagePath = null;
            }

            $menu = Menu::create([
                'name' => $request->name,
                'price' => $request->price,
                'category_id' => $request->category_id,
                'description' => $request->description,
                'image' => $imagePath,
                'prep_time' => $request->prep_time ?? 15,
                'cost' => 0,
                'available' => filter_var($request->available, FILTER_VALIDATE_BOOLEAN), // Ensure boolean conversion
            ]);

            Log::info('Menu Created:', $menu->toArray());

            $inventoryItems = $request->inventory_items ? json_decode($request->inventory_items, true) : [];
            if (!empty($inventoryItems)) {
                $syncData = collect($inventoryItems)->mapWithKeys(function ($item) {
                    return [$item['id'] => ['quantity' => $item['quantity'], 'unit' => $item['unit']]];
                })->all();

                Log::info('Inventory Sync Data:', $syncData);

                foreach ($syncData as $id => $data) {
                    if (!Inventory::find($id)) {
                        throw new \Exception("Invalid inventory ID: {$id}");
                    }
                    if ($data['quantity'] <= 0) {
                        throw new \Exception("Quantity must be positive for inventory ID: {$id}");
                    }
                }

                $menu->inventories()->sync($syncData);
                $menu->cost = $menu->calculateCost();
                $menu->save();

                Log::info('Menu Updated with Inventory:', $menu->toArray());
            }

            return redirect()->route('admin.menus')->with('success', 'Menu item created successfully.');
        } catch (\Exception $e) {
            Log::error('Menu Creation Failed:', ['error' => $e->getMessage()]);
            return redirect()->back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    public function edit(Menu $menu)
    {
        $categories = Category::all();
        $inventories = Inventory::all();
        return inertia('Admin/Menu/Edit', [
            'menu' => $menu->load('category', 'inventories'),
            'categories' => $categories,
            'inventories' => $inventories,
        ]);
    }

    public function update(Request $request, Menu $menu)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:menus,name,' . $menu->id,
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'prep_time' => 'nullable|integer|min:1',
            'available' => 'required|boolean',
            'inventory_items' => 'nullable|array',
            'inventory_items.*.id' => 'required_with:inventory_items|exists:inventories,id',
            'inventory_items.*.quantity' => 'required_with:inventory_items|numeric|min:0',
            'inventory_items.*.unit' => 'required_with:inventory_items|string',
        ]);

        if ($request->hasFile('image')) {
            if ($menu->image) {
                Storage::disk('public')->delete($menu->image);
            }
            $imagePath = $request->file('image')->store('menus', 'public');
        }

        $menu->update([
            'name' => $request->name,
            'price' => $request->price,
            'category_id' => $request->category_id,
            'description' => $request->description,
            'image' => $imagePath ?? $menu->image,
            'prep_time' => $request->prep_time ?? $menu->prep_time,
            'available' => $request->available,
        ]);

        if ($request->inventory_items) {
            $syncData = collect($request->inventory_items)->mapWithKeys(function ($item) {
                return [$item['id'] => ['quantity' => $item['quantity'], 'unit' => $item['unit']]];
            })->all();
            $menu->inventories()->sync($syncData);
            $menu->cost = $menu->calculateCost();
            $menu->save();
        } else {
            $menu->inventories()->detach();
            $menu->cost = 0;
            $menu->save();
        }

        return redirect()->route('admin.menus')->with('success', 'Menu item updated successfully.');
    }

    public function destroy(Menu $menu)
    {
        if ($menu->image) {
            Storage::disk('public')->delete($menu->image);
        }
        $menu->delete(); // Cascade deletes inventory_menu entries
        return redirect()->route('admin.menus')->with('success', 'Menu item deleted successfully.');
    }

    public function profitReport()
{
    $menus = Menu::with(['category', 'inventories'])->get()->map(function ($menu) {
        $profit = $menu->price - $menu->cost;
        $profitMargin = $menu->price > 0 ? ($profit / $menu->price) * 100 : 0; // Percentage
        return [
            'id' => $menu->id,
            'name' => $menu->name,
            'category' => $menu->category ? $menu->category->name : 'N/A',
            'price' => $menu->price,
            'cost' => $menu->cost,
            'profit' => $profit,
            'profit_margin' => round($profitMargin, 2), // Rounded to 2 decimals
            'available' => $menu->available,
        ];
    });

    return inertia('Admin/Menu/ProfitReport', ['menus' => $menus]);
}
}