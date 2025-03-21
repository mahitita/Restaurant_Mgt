<?php

namespace App\Http\Controllers\User;

use Carbon\Carbon;
use App\Models\Menu;
use Inertia\Inertia;
use App\Models\Table;
use App\Http\Controllers\Controller;

class HomeController extends Controller
{
public function index()
    {
        $featuredMenus = Menu::where('available', true)
            ->take(3)
            ->get()
            ->map(function ($menu) {
                return [
                    'id' => $menu->id,
                    'name' => $menu->name,
                    'price' => $menu->price,
                    'description' => $menu->description,
                    'image' => $menu->image ? asset('storage/' . $menu->image) : null,
                ];
            });

        $availableTables = Table::all()
            ->map(function ($table) {
                $today = Carbon::today();
                $table->available = $table->isAvailable($today);
                return $table->only(['id', 'table_number', 'seats', 'available']);
            })
            ->filter(fn($table) => $table['available'])
            ->take(3);

        return Inertia::render('Home', [
            'featuredMenus' => $featuredMenus,
            'availableTables' => $availableTables,
        ]);
    }
}
