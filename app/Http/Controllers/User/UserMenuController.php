<?php

namespace App\Http\Controllers\User;

use App\Models\Menu;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserMenuController extends Controller
{
    public function index()
    {
        $menus = Menu::all();
        $categories = Category::all();
        return inertia('Menu', compact('menus', 'categories'));
    }
}
