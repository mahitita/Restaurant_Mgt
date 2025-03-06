<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Menu;

class HomeController extends Controller
{
    public function index()
    {
        $menus = Menu::take(6)->get(); // Show top 6 menu items
        return inertia('Home', ['menus' => $menus]);
    }
}
