<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Menu;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    // Show all orders
    public function index()
    {
        $orders = Order::with('user', 'orderItems.menu')->latest()->get();
        return inertia('admin/orders/Index', compact('orders'));
    }

    // Create a new order (Dine-in, Takeout, Delivery)
    public function store(Request $request)
    {
        $request->validate([
            'order_type' => 'required|in:dine-in,takeout,delivery',
            'items' => 'required|array',
            'items.*.menu_id' => 'exists:menus,id',
            'items.*.quantity' => 'integer|min:1',
        ]);

        $order = Order::create([
            'user_id' => auth()->id(),
            'order_type' => $request->order_type,
            'status' => 'pending',
            'total_price' => 0,
        ]);

        $totalPrice = 0;

        foreach ($request->items as $item) {
            $menu = Menu::find($item['menu_id']);
            $price = $menu->price * $item['quantity'];

            OrderItem::create([
                'order_id' => $order->id,
                'menu_id' => $item['menu_id'],
                'quantity' => $item['quantity'],
                'price' => $price,
            ]);

            $totalPrice += $price;
        }

        $order->update(['total_price' => $totalPrice]);

        return redirect()->route('admin.orders.index')->with('success', 'Order placed successfully!');
    }

    // Update order status
    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,preparing,ready,completed,cancelled',
        ]);

        $order->update(['status' => $request->status]);

        return back()->with('success', 'Order status updated.');
    }

    // Delete an order
    public function destroy(Order $order)
    {
        $order->delete();
        return back()->with('success', 'Order deleted.');
    }
}
