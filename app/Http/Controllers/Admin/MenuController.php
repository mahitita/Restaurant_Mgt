<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\Category;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function index()
    {
        $menus = Menu::with('category')->get();
        return inertia('Admin/Menu/Index', ['menus' => $menus]);
    }

    public function create()
    {
        $categories = Category::all();
        return inertia('Admin/Menu/Create', ['categories' => $categories]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:menu_items,name',
            'price' => 'required|numeric',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('menus', 'public');
        }

        Menu::create([
            'name' => $request->name,
            'price' => $request->price,
            'category_id' => $request->category_id,
            'description' => $request->description,
            'image' => $imagePath ?? null,
        ]);

        return redirect()->route('Admin.Menu.index')->with('success', 'Menu item created successfully.');
    }

    public function edit(Menu $menu)
    {
        $categories = Category::all();
        return inertia('Admin/Menu/Edit', ['menu' => $menu, 'categories' => $categories]);
    }

    public function update(Request $request, Menu $menu)
    {
        $request->validate([
            'name' => 'required|string|unique:menu_items,name,' . $menu->id,
            'price' => 'required|numeric',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('menu_items', 'public');
            // Delete old image if it exists
            if ($menu->image) {
                unlink(storage_path('app/public/' . $menu->image));
            }
        }

        $menu->update([
            'name' => $request->name,
            'price' => $request->price,
            'category_id' => $request->category_id,
            'description' => $request->description,
            'image' => $imagePath ?? $menu->image,
        ]);

        return redirect()->route('Admin.Menu.index')->with('success', 'Menu item updated successfully.');
    }

    public function destroy(Menu $menu)
    {
        // Delete image if it exists
        if ($menu->image) {
            unlink(storage_path('app/public/' . $menu->image));
        }

        $menu->delete();
        return redirect()->route('Admin.Menu.index')->with('success', 'Menu item deleted successfully.');
    }
}
