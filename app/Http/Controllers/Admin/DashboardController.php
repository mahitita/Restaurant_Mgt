<?php

namespace App\Http\Controllers\Admin;

use App\Models\Menu;
use Inertia\Inertia;
use App\Models\Order;
use App\Models\Table;
use App\Models\OrderItem;
use App\Models\InventoryLog;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        // Revenue Data
        $todayOrders = Order::whereDate('created_at', today())->get();
        $totalRevenue = $todayOrders->sum('total_price');
        $hourlyRevenue = $todayOrders->groupBy(fn($o) => $o->created_at->hour)
            ->map->sum('total_price');
        $slowHours = $hourlyRevenue->filter(fn($rev, $hour) => $rev < 50 && now()->hour > $hour)->keys();

        // Staff Data
        $activeOrders = Order::whereIn('status', ['received', 'preparing'])->count();
        $tableTurnover = Table::where('status', 'occupied')->avg(fn($t) => now()->diffInMinutes($t->updated_at));
        $suggestedStaff = max(1, ceil($activeOrders / 5)); // 1 staff per 5 orders

        // Inventory Data
        $menuItems = Menu::all();
        $lowStock = $menuItems->filter(fn($item) => $item->stock_quantity < 10);
        $dailyUsage = OrderItem::whereDate('created_at', today())
            ->groupBy('menu_id')
            ->map->sum('quantity');

        // Customer Data
        $topItems = OrderItem::whereDate('created_at', today())
            ->with('menu')
            ->groupBy('menu_id')
            ->map->sum('quantity')
            ->sortDesc()
            ->take(3);
        $repeatCustomers = Order::whereDate('created_at', today())
            ->groupBy('user_id')
            ->filter(fn($orders) => $orders->count() > 1)
            ->count();
        // $feedback = Feedback::whereDate('created_at', today())->avg('rating') ?? 0;

        return Inertia::render('Admin/Dashboard', [
            'revenue' => [
                'total' => $totalRevenue,
                'hourly' => $hourlyRevenue,
                'slow_hours' => $slowHours,
            ],
            'staff' => [
                'active_orders' => $activeOrders,
                'table_turnover' => round($tableTurnover ?? 30),
                'suggested_staff' => $suggestedStaff,
            ],
            'inventory' => [
                'low_stock' => $lowStock->map(fn($item) => [
                    'name' => $item->name,
                    'stock' => $item->stock_quantity,
                ]),
                'daily_usage' => $dailyUsage,
            ],
            'customers' => [
                'top_items' => $topItems->map(fn($qty, $id) => [
                    'name' => Menu::find($id)->name,
                    'quantity' => $qty,
                ]),
                'repeat_customers' => $repeatCustomers,
                // 'average_rating' => round($feedback, 1),
            ],
        ]);
    }

    public function updateStock(Request $request)
    {
        $request->validate([
            'menu_id' => 'required|exists:menus,id',
            'quantity' => 'required|integer|min:0',
        ]);

        $menu = Menu::find($request->menu_id);
        $menu->stock_quantity = $request->quantity;
        $menu->save();

        return redirect()->route('admin.dashboard')->with('success', 'Stock updated.');
    }

    public function logWaste(Request $request)
    {
        $request->validate([
            'menu_id' => 'required|exists:menus,id',
            'quantity_used' => 'required|integer|min:0',
            'quantity_wasted' => 'required|integer|min:0',
        ]);

        InventoryLog::create([
            'menu_id' => $request->menu_id,
            'quantity_used' => $request->quantity_used,
            'quantity_wasted' => $request->quantity_wasted,
            'date' => today(),
        ]);

        $menu = Menu::find($request->menu_id);
        $menu->stock_quantity -= ($request->quantity_used + $request->quantity_wasted);
        $menu->save();

        return redirect()->route('admin.dashboard')->with('success', 'Waste logged.');
    }
}

