<?php

namespace App\Http\Controllers\Admin;

use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with('items.menuItem')->orderBy('created_at', 'desc')->get();
        return inertia('admin/Orders/Index', compact('orders'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'order_type' => 'required',
            'menu_items' => 'required|array',
            'menu_items.*.id' => 'exists:menu_items,id',
            'menu_items.*.quantity' => 'required|integer|min:1',
        ]);

        $order = Order::create([
            'order_type' => $request->order_type,
            'table_id' => $request->table_id ?? null,
            'total_price' => 0,
        ]);

        $totalPrice = 0;
        foreach ($request->menu_items as $item) {
            $menuItem = Menu::find($item['id']);
            $subtotal = $menuItem->price * $item['quantity'];
            $totalPrice += $subtotal;

            OrderItem::create([
                'order_id' => $order->id,
                'menu_item_id' => $item['id'],
                'quantity' => $item['quantity'],
                'subtotal' => $subtotal,
            ]);
        }

        $order->update(['total_price' => $totalPrice]);

        return redirect()->route('admin.orders.index')->with('success', 'Order created successfully.');
    }

    public function updateStatus(Request $request, Order $order)
{
    $request->validate([
        'status' => 'required|in:Pending,Preparing,Completed,Canceled',
    ]);

    $order->update(['status' => $request->status]);

    return back()->with('success', 'Order status updated successfully.');
}

}

