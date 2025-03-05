<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use Inertia\Inertia;
use Inertia\Response;

class OrderController extends Controller
{
    public function index(): Response
    {
        $orders = Order::with('user')->orderBy('ordered_at', 'desc')->get();
        return Inertia::render('Admin/Orders/Index', ['orders' => $orders]);
    }

    public function show(Order $order): Response
    {
        return Inertia::render('Admin/Orders/Show', ['order' => $order]);
    }

    public function update(Request $request, Order $order)
    {
        $request->validate(['status' => 'required|in:pending,preparing,ready,completed,cancelled']);
        $order->update(['status' => $request->status]);

        return redirect()->route('admin.orders.index')->with('success', 'Order status updated!');
    }

    public function destroy(Order $order)
    {
        $order->delete();
        return redirect()->route('admin.orders.index')->with('success', 'Order deleted!');
    }
}
