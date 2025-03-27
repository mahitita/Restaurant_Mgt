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
        $menus = Menu::with('category')
            ->where('available', true)
            ->get()
            ->map(function ($menu) {
                return [
                    'id' => $menu->id,
                    'name' => $menu->name,
                    'price' => $menu->price,
                    'category_id' => $menu->category_id,
                    'category_name' => $menu->category->name,
                    'description' => $menu->description ?? 'A delicious dish!',
                    'image' => $menu->image ? asset('storage/' . $menu->image) : '/images/default-menu.jpg',
                    'prep_time' => $menu->prep_time,
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

    public function addToCart(Request $request, $menuId)
    {
        if (!auth()->check()) {
            return redirect()->route('user.login', ['return_to' => route('cart.add', $menuId)]);
        }

        $menu = Menu::findOrFail($menuId);
        return redirect()->route('menu.index')->with('success', "{$menu->name} added to cart!");
    }
}
