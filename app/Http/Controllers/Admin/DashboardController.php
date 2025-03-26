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

class DashboardController extends Controller
{
    public function index()
    {
        $ordersCount = Order::whereDate('created_at', today())->count();
        $revenue = Order::whereDate('created_at', today())->sum('total_price') ?? 0;
        $reservationsCount = Reservation::whereDate('reservation_time', today())->where('status', 'confirmed')->count();
        $lowInventoryCount = Inventory::where('quantity', '<=', 10)->count();
        $recentOrders = Order::with('user')->latest()->take(5)->get()->map(fn($order) => [
            'id' => $order->id,
            'customer' => $order->user->name,
            'total' => number_format($order->total_price, 2),
            'status' => $order->status,
        ]);
        $inventoryAlerts = Inventory::where('quantity', '<=', 10)->take(3)->get(['id', 'name', 'quantity']);
        $reservations = Reservation::whereDate('reservation_time', today())->where('status', 'confirmed')->take(3)->get()->map(fn($res) => [
            'id' => $res->id,
            'customer' => $res->user->name,
            'table' => $res->tables->first()->table_number,
            'time' => $res->reservation_time->format('h:i A'),
        ]);
    
        return inertia('Admin/AdminDashboard', [
            'ordersCount' => $ordersCount,
            'revenue' => $revenue,
            'reservationsCount' => $reservationsCount,
            'lowInventoryCount' => $lowInventoryCount,
            'recentOrders' => $recentOrders,
            'inventoryAlerts' => $inventoryAlerts,
            'reservations' => $reservations,
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

