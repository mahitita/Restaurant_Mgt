<?php

namespace App\Http\Controllers\Admin;

use App\Models\Menu;
use Inertia\Inertia;
use App\Models\Order;
use App\Models\Table;
use App\Models\Inventory;
use App\Models\OrderItem;
use App\Models\Reservation;
use App\Models\InventoryLog;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Daily Stats
        $ordersCount = Order::whereDate('created_at', today())->count();
        $revenue = Order::whereDate('created_at', today())->sum('total_price') ?? 0;
        $reservationsCount = Reservation::whereDate('reservation_time', today())->where('status', 'confirmed')->count();
        $lowInventoryCount = Inventory::where('remaining_quantity', '<=', 'threshold')->count();

        // Recent Orders
        $recentOrders = Order::with('user')->latest()->take(5)->get()->map(fn($order) => [
            'id' => $order->id,
            'customer' => $order->user->name ?? 'Guest',
            'total' => number_format($order->total_price, 2),
            'status' => $order->status,
            'created_at' => $order->created_at->format('h:i A'),
        ]);

        // Inventory Alerts
        $inventoryAlerts = Inventory::where('remaining_quantity', '<=', 'threshold')
            ->take(3)
            ->get(['id', 'name', 'remaining_quantity'])
            ->map(fn($item) => [
                'id' => $item->id,
                'name' => $item->name,
                'quantity' => $item->remaining_quantity,
            ]);

        // Upcoming Reservations
        $reservations = Reservation::with('table')
            ->whereDate('reservation_time', today())
            ->where('status', 'confirmed')
            ->take(3)
            ->get()
            ->map(fn($res) => [
                'id' => $res->id,
                'customer' => $res->user->name ?? 'Guest',
                'table' => $res->table ? $res->table->table_number : 'N/A',
                'time' => $res->reservation_time->format('h:i A'),
            ]);

        $revenueData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $dailyRevenue = Order::whereDate('created_at', $date)->sum('total_price') ?? 0;
            $revenueData[] = [
                'day' => $date->format('D'),
                'value' => $dailyRevenue,
            ];
        }

        $pendingTasks = [
            'unprocessed_orders' => Order::whereIn('status', ['pending', 'received'])->count(),
            'low_inventory' => $lowInventoryCount,
            'pending_reservations' => Reservation::where('status', 'pending')->count(),
        ];

        return Inertia::render('Admin/AdminDashboard', [
            'ordersCount' => $ordersCount,
            'revenue' => $revenue,
            'reservationsCount' => $reservationsCount,
            'lowInventoryCount' => $lowInventoryCount,
            'recentOrders' => $recentOrders,
            'inventoryAlerts' => $inventoryAlerts,
            'reservations' => $reservations,
            'revenueData' => $revenueData,
            'pendingTasks' => $pendingTasks,
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

        InventoryLog::create([
            'inventory_id' => $menu->id,
            'action' => 'restock',
            'quantity' => $request->quantity,
            'reason' => 'Manual stock update via dashboard',
        ]);

        return redirect()->route('admin.dashboard')->with('success', 'Stock updated successfully.');
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

        return redirect()->route('admin.dashboard')->with('success', 'Waste logged successfully.');
    }
}