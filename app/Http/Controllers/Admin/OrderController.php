<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use Inertia\Inertia;
use Inertia\Response;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with('orderItems.menu', 'user')
            ->whereIn('status', ['received', 'preparing'])
            ->orderBy('is_priority', 'desc')
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(fn($order) => [
                'id' => $order->id,
                'user_name' => $order->user->name ?? 'Unknown',
                'status' => $order->status,
                'total_price' => $order->total_price,
                'is_priority' => $order->is_priority,
                'estimated_wait_minutes' => $order->estimated_wait_minutes,
                'items' => $order->orderItems->map(fn($item) => [
                    'name' => $item->menu->name,
                    'quantity' => $item->quantity,
                ]),
            ]);

        return Inertia::render('Admin/Orders/Index', [
            'orders' => $orders,
        ]);
    }

    public function togglePriority(Order $order)
    {
        $order->is_priority = !$order->is_priority;
        $order->save();

        return redirect()->route('admin.orders.index')->with('success', 'Priority updated.');
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:received,preparing,ready,completed',
        ]);

        $order->status = $request->status;
        $order->save();

        return redirect()->route('admin.orders.index')->with('success', 'Order status updated.');
    }
}
