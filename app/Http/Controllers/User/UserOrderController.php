<?php

namespace App\Http\Controllers\User;

use Carbon\Carbon;
use App\Models\Order;
use App\Models\Payment;
use App\Models\OrderItem;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class UserOrderController extends Controller
{
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
            'payment.paymentType' => 'required|in:card,bank_transfer,cash',
            'payment.accountNumber' => 'required_unless:payment.paymentType,cash|string|max:255',
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

            $reservation = Reservation::where('user_id', Auth::id())
                ->where('status', 'confirmed')
                ->whereDate('reservation_time', now()->toDateString())
                ->first();

            $paymentMethodMap = [
                'card' => 'cbe_birr',
                'bank_transfer' => 'amole',
                'cash' => 'cash',
            ];
            $frontendPaymentType = $request->input('payment.paymentType');
            $backendPaymentMethod = $paymentMethodMap[$frontendPaymentType] ?? 'cash';

            if ($reservation) {
                $reservationPayment = $reservation->payment;
                if ($frontendPaymentType === 'cash') {
                    $reservationPayment->update([
                        'amount' => $totalPrice,
                        'deposit_refunded' => true,
                    ]);
                    $reservation->table->status = 'occupied';
                    $reservation->table->save();
                } else {
                    $depositUsed = min($totalPrice, $reservationPayment->deposit_amount);
                    $remainingAmount = max(0, $totalPrice - $reservationPayment->deposit_amount);

                    $reservationPayment->update([
                        'amount' => $totalPrice,
                        'deposit_amount' => $depositUsed,
                        'payment_method' => $backendPaymentMethod,
                        'paid_at' => now(),
                    ]);

                    if ($remainingAmount > 0) {
                        Log::info("Additional payment needed: {$remainingAmount}");
                        // Future: Charge remaining amount via gateway
                    }

                    $reservation->table->status = 'occupied';
                    $reservation->table->save();
                }
            } else {
                Payment::create([
                    'order_id' => $order->id,
                    'payment_method' => $backendPaymentMethod,
                    'amount' => $totalPrice,
                    'deposit_amount' => 0,
                    'paid_at' => now(),
                    'status' => 'paid',
                ]);
            }

            return redirect()->route('orders.confirmation', ['order' => $order->id]);
        });
    }

    public function confirmation($orderId)
    {
        $order = Order::with('orderItems')->findOrFail($orderId);
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $payment = Payment::where('order_id', $order->id)
            ->orWhereHas('reservation', fn($query) => $query->where('table_id', $order->table_id))
            ->firstOrFail();

        $reservation = Reservation::where('user_id', Auth::id())
            ->where('table_id', $order->table_id)
            ->whereDate('reservation_time', now()->toDateString())
            ->first();

        return inertia('OrderConfirmation', [
            'order' => $order,
            'payment' => $payment,
            'reservation' => $reservation,
            'success' => 'Order and payment processed successfully!',
        ]);
    }
}
