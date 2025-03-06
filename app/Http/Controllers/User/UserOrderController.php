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
            'cart' => 'required|array',
            'cart.*.id' => 'required|exists:menus,id',
            'cart.*.quantity' => 'required|integer|min:1',
        ]);

        $order = Order::create([
            'user_id' => Auth::id(),
            'order_type' => 'takeout',
            'status' => 'pending',
            'total_price' => collect($request->cart)->sum(fn ($item) => $item['price'] * $item['quantity']),
        ]);

        foreach ($request->cart as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'menu_id' => $item['id'],
                'quantity' => $item['quantity'],
                'price' => $item['price'],
            ]);
        }

        return redirect()->route('cart.index')->with('success', 'Order placed successfully!');
    }
}
