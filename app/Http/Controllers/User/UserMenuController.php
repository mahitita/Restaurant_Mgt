<?php

namespace App\Http\Controllers\User;

use App\Models\Menu;
use Inertia\Inertia;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserMenuController extends Controller
{
    public function index()
    {
        $menus = Menu::with('category')->get()->map(function ($menu) {
            return [
                'id' => $menu->id,
                'name' => $menu->name,
                'price' => $menu->price,
                'prep_time' => $menu->prep_time ?? 5,
                'stock_quantity' => $menu->stock_quantity,
                'cost' => $menu->cost ?? 0,
                'image' => $menu->image ?? '/images/default-menu.jpg', // Default image
                'description' => $menu->description ?? 'A delicious dish!',
                'category_id' => $menu->category_id,
            ];
        });

        $categories = Category::all()->map(function ($category) {
            return [
                'id' => $category->id,
                'name' => $category->name,
            ];
        });

        return Inertia::render('Menu', [
            'menus' => $menus,
            'categories' => $categories,
        ]);
    }
}
