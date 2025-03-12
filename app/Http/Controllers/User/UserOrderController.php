<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserOrderController extends Controller
{
    public function index()
    {
        $orders = Order::where('user_id', Auth::id())->latest()->get();
        return inertia('Orders', ['orders' => $orders]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'cart' => 'required|array|min:1',
            'cart.*.id' => 'required|exists:menus,id',
            'cart.*.quantity' => 'required|integer|min:1',
            'cart.*.price' => 'required|numeric|min:0',
            'order_type' => 'required|in:dine-in,takeout,delivery',
            'table_id' => 'nullable|required_if:order_type,dine-in|exists:tables,id',
            'pickup_time' => 'nullable|required_if:order_type,takeout|date|after:now',
            'delivery_address' => 'nullable|required_if:order_type,delivery|string|max:255',
        ]);

        // Calculate total price from the cart
        $totalPrice = collect($request->cart)->sum(fn($item) => $item['price'] * $item['quantity']);

        // Create the order
        $order = Order::create([
            'user_id' => Auth::id(),
            'order_type' => $request->order_type,
            'status' => 'pending',
            'total_price' => $totalPrice,
            'table_id' => $request->table_id,
            'pickup_time' => $request->pickup_time,
            'delivery_address' => $request->delivery_address,
        ]);

        // Create related order items
        foreach ($request->cart as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'menu_id' => $item['id'],
                'quantity' => $item['quantity'],
                'price' => $item['price'],
            ]);
        }

        return redirect()->route('orders.index')->with('success', 'Order placed successfully!');
    }
}
