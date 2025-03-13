<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
            'payment.paymentType' => 'required|in:card,bank_transfer',
            'payment.accountNumber' => 'required|string|max:255',
        ]);

        return DB::transaction(function () use ($request) {
            $totalPrice = collect($request->cart)->sum(fn($item) => $item['price'] * $item['quantity']);

            $order = Order::create([
                'user_id' => Auth::id(),
                'order_type' => $request->order_type,
                'status' => 'pending',
                'total_price' => $totalPrice,
                'table_id' => $request->table_id,
                'pickup_time' => $request->pickup_time,
                'delivery_address' => $request->delivery_address,
            ]);

            foreach ($request->cart as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'menu_id' => $item['id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                ]);
            }

            $paymentMethodMap = [
                'card' => 'cbe_birr',
                'bank_transfer' => 'amole',
            ];
            $frontendPaymentType = $request->input('payment.paymentType');
            $backendPaymentMethod = $paymentMethodMap[$frontendPaymentType] ?? 'cash';

            $payment = Payment::create([
                'order_id' => $order->id,
                'payment_method' => $backendPaymentMethod,
                'amount' => $totalPrice,
                'paid_at' => now(),
                'status' => 'paid',
            ]);

            // Redirect to confirmation route with order ID
            return redirect()->route('orders.confirmation', ['order' => $order->id]);
        });
    }

    // New method for confirmation page
    public function confirmation($orderId)
    {
        $order = Order::with('orderItems')->findOrFail($orderId);
        $payment = Payment::where('order_id', $order->id)->firstOrFail();

        if ($order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        return inertia('OrderConfirmation', [
            'order' => $order,
            'payment' => $payment,
            'success' => 'Order and payment processed successfully!',
        ]);
    }
}
