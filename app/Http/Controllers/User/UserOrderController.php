<?php

namespace App\Http\Controllers\User;

use Carbon\Carbon;
use App\Models\Menu;
use Inertia\Inertia;
use App\Models\Order;
use App\Models\Table;
use App\Models\Payment;
use App\Models\Inventory;
use App\Models\OrderItem;
use App\Models\Reservation;
use App\Models\InventoryLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class UserOrderController extends Controller
{
    public function cart()
    {
        return Inertia::render('Cart');
    }

    public function store(Request $request)
    {
        Log::info('Order store request received:', $request->all());

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

        try {
            return DB::transaction(function () use ($request) {
                $totalPrice = collect($request->cart)->sum(fn($item) => $item['price'] * $item['quantity']);
                Log::info('Calculated total price:', ['total_price' => $totalPrice]);

                $order = Order::create([
                    'user_id' => Auth::id(),
                    'order_type' => $request->order_type,
                    'status' => 'pending',
                    'total_price' => $totalPrice,
                    'table_id' => $request->table_id,
                    'pickup_time' => $request->pickup_time,
                    'delivery_address' => $request->delivery_address,
                ]);
                Log::info('Order created:', ['order_id' => $order->id]);

                foreach ($request->cart as $item) {
                    OrderItem::create([
                        'order_id' => $order->id,
                        'menu_id' => $item['id'],
                        'quantity' => $item['quantity'],
                        'price' => $item['price'],
                    ]);
                    Log::info('Order item created:', ['menu_id' => $item['id'], 'quantity' => $item['quantity']]);
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
                    $reservationPayment = $reservation->payment ?? Payment::create([
                        'reservation_id' => $reservation->id,
                        'payment_method' => $backendPaymentMethod,
                        'amount' => 0,
                        'deposit_amount' => 0,
                        'paid_at' => now(),
                        'status' => 'pending',
                    ]);

                    if ($frontendPaymentType === 'cash') {
                        $reservationPayment->update([
                            'amount' => $totalPrice,
                            'status' => 'paid',
                            'deposit_refunded' => true,
                        ]);
                        $reservation->table->status = 'occupied';
                        $reservation->table->save();
                        Log::info('Cash payment processed for reservation:', ['reservation_id' => $reservation->id]);
                    } else {
                        $depositUsed = min($totalPrice, $reservationPayment->deposit_amount ?? 0);
                        $remainingAmount = max(0, $totalPrice - ($reservationPayment->deposit_amount ?? 0));

                        $reservationPayment->update([
                            'amount' => $totalPrice,
                            'deposit_amount' => $depositUsed,
                            'payment_method' => $backendPaymentMethod,
                            'paid_at' => now(),
                            'status' => 'paid',
                        ]);

                        if ($remainingAmount > 0) {
                            Log::info("Additional payment needed: {$remainingAmount}");
                        }

                        $reservation->table->status = 'occupied';
                        $reservation->table->save();
                        Log::info('Non-cash payment processed for reservation:', ['reservation_id' => $reservation->id]);
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
                    Log::info('Payment created for order:', ['order_id' => $order->id]);
                }

                return redirect()->route('orders.confirmation', ['order' => $order->id])
                    ->with('success', 'Order placed successfully!');
            });
        } catch (\Exception $e) {
            Log::error('Order store failed:', ['error' => $e->getMessage()]);
            throw $e; // Re-throw to rollback transaction and trigger error response
        }
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

        // Ensure reservations is always an array, even if empty
        $reservations = Reservation::where('user_id', Auth::id())
            ->whereDate('reservation_time', now()->toDateString())
            ->get()
            ->map(fn($reservation) => [
                'id' => $reservation->id,
                'table_id' => $reservation->table_id,
                'reservation_time' => $reservation->reservation_time->toDateTimeString(),
            ])
            ->all(); // Convert Collection to array

        return inertia('OrderConfirmation', [
            'order' => [
                'id' => $order->id,
                'order_type' => $order->order_type,
                'total_price' => $order->total_price,
                'table_id' => $order->table_id,
                'pickup_time' => $order->pickup_time,
                'delivery_address' => $order->delivery_address,
                'order_items' => $order->orderItems->map(fn($item) => [
                    'id' => $item->id,
                    'menu_id' => $item->menu_id,
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                ]),
            ],
            'payment' => [
                'payment_method' => $payment->payment_method,
                'amount' => $payment->amount,
                'deposit_amount' => $payment->deposit_amount ?? 0,
                'deposit_refunded' => $payment->deposit_refunded ?? false,
                'paid_at' => $payment->paid_at,
                'status' => $payment->status,
            ],
            'reservations' => $reservations, // Always an array
            'success' => 'Order and payment processed successfully!',
        ]);
    }

    private function deductInventory($menuId, $quantity)
    {
        $menu = Menu::with('ingredients')->find($menuId);

        foreach ($menu->ingredients as $ingredient) {
            $inventory = Inventory::where('name', $ingredient->name)->first();

            if ($inventory && $inventory->quantity >= $ingredient->pivot->quantity * $quantity) {
                $inventory->decrement('quantity', $ingredient->pivot->quantity * $quantity);

                InventoryLog::create([
                    'inventory_id' => $inventory->id,
                    'action' => 'deducted',
                    'quantity' => $ingredient->pivot->quantity * $quantity,
                ]);

                if ($inventory->isLowStock()) {
                    logger('Low stock alert: ' . $inventory->name);
                }
            } else {
                throw new \Exception("Insufficient stock for {$ingredient->name}");
            }
        }
    }
    private function checkInventory($menuId, $quantity)
    {
        $menu = Menu::with('ingredients')->find($menuId);

        foreach ($menu->ingredients as $ingredient) {
            $inventory = Inventory::where('name', $ingredient->name)->first();

            if (!$inventory || $inventory->quantity < $ingredient->pivot->quantity * $quantity) {
                return false; // Insufficient stock
            }
        }

        return true;
    }

    public function preorder(Request $request)
{
    $reservationIds = explode(',', $request->query('reservation_ids'));
    $reservations = Reservation::whereIn('id', $reservationIds)
        ->where('user_id', Auth::id())
        ->with('table')
        ->get();

    // if ($reservations->isEmpty()) {
    //     abort(403, 'Invalid or unauthorized reservation.');
    // }

    return Inertia::render('Orders/Preorder', [
        'reservations' => $reservations->map(fn($r) => [
            'id' => $r->id,
            'table_number' => $r->table->table_number,
            'reservation_time' => $r->reservation_time->toDateTimeString(),
        ]),
        'menuItems' => Menu::all(), // Assume a Menu model exists
    ]);
}

public function storePreorder(Request $request)
    {
        $request->validate([
            'reservation_ids' => 'required|array|min:1',
            'reservation_ids.*' => 'exists:reservations,id',
            'cart' => 'required|array|min:1',
            'cart.*.id' => 'required|exists:menus,id',
            'cart.*.quantity' => 'required|integer|min:1',
            'cart.*.price' => 'required|numeric|min:0',
        ]);

        $reservations = Reservation::whereIn('id', $request->reservation_ids)
            ->where('user_id', Auth::id())
            ->get();

        if ($reservations->isEmpty()) {
            abort(403, 'Invalid or unauthorized reservation.');
        }

        return DB::transaction(function () use ($request, $reservations) {
            $totalPrice = collect($request->cart)->sum(fn($item) => $item['price'] * $item['quantity']);
            $cartItems = collect($request->cart)->map(fn($item) => Menu::find($item['id']));
            $prepTime = $this->calculatePrepTime($cartItems, $reservations->first()->reservation_time);
            $isPeakHour = $this->isPeakHour(now());
            $isPriority = $this->determinePriority($cartItems, $totalPrice, $isPeakHour);

            $order = Order::create([
                'user_id' => Auth::id(),
                'order_type' => 'dine-in',
                'status' => 'received',
                'total_price' => $totalPrice,
                'table_id' => $reservations->first()->table_id,
                'is_priority' => $isPriority,
                'estimated_wait_minutes' => $prepTime,
            ]);

            foreach ($request->cart as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'menu_id' => $item['id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                ]);
            }

            foreach ($reservations as $reservation) {
                $reservation->order_id = $order->id;
                $reservation->save();
            }

            return redirect()->route('orders.track', $order->id)
                ->with('success', 'Pre-order placed! Estimated wait: ' . $prepTime . ' minutes.');
        });
    }

    public function track($orderId)
    {
        $order = Order::with('orderItems.menu')->findOrFail($orderId);
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        return Inertia::render('Orders/Track', [
            'order' => [
                'id' => $order->id,
                'status' => $order->status,
                'total_price' => $order->total_price,
                'estimated_wait_minutes' => $order->estimated_wait_minutes,
                'items' => $order->orderItems->map(fn($item) => [
                    'name' => $item->menu->name,
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                ]),
            ],
        ]);
    }

    private function calculatePrepTime($items, $reservationTime = null)
    {
        $baseTime = 5; // Adjusted base time
        $itemPrepTime = $items->sum(fn($item) => ($item->prep_time ?? 5) * $item->pivot->quantity);

        // Kitchen load: average completion time of recent orders
        $recentOrders = Order::where('status', 'completed')
            ->where('updated_at', '>=', now()->subHour())
            ->get();
        $avgCompletionTime = $recentOrders->count() > 0
            ? $recentOrders->avg(fn($o) => $o->updated_at->diffInMinutes($o->created_at))
            : 10;

        $activeOrders = Order::whereIn('status', ['received', 'preparing'])->count();
        $kitchenLoad = $activeOrders * ($avgCompletionTime / 2); // Half impact per order

        // Table wait (for dine-in without reservation or if reservation is later)
        $tableWait = 0;
        if ($reservationTime) {
            $timeToReservation = max(0, now()->diffInMinutes($reservationTime));
            $availableTables = Table::where('status', 'available')->count();
            if ($availableTables === 0 && $timeToReservation > 0) {
                $occupiedTables = Table::where('status', 'occupied')->get();
                $tableWait = $occupiedTables->count() > 0
                    ? $occupiedTables->avg(fn($t) => now()->diffInMinutes($t->updated_at)) / 2
                    : 15; // Default 15 min if no data
            }
        }

        return round($baseTime + $itemPrepTime + $kitchenLoad + $tableWait);
    }

    private function isPeakHour($time)
    {
        $hour = $time->hour;
        return ($hour >= 12 && $hour < 14) || ($hour >= 18 && $hour < 20); // 12-2 PM, 6-8 PM
    }

    private function determinePriority($items, $totalPrice, $isPeakHour)
{
    $prepTime = $items->sum(fn($item) => ($item->prep_time ?? 5) * $item->pivot->quantity);
    $orderSize = $items->sum('pivot.quantity');
    $userOrderCount = Order::where('user_id', Auth::id())->count();

    $isQuick = $prepTime < 15; // Quick prep
    $isHighValue = $totalPrice > 50; // High value
    $isLarge = $orderSize > 5; // Large order
    $isLoyal = $userOrderCount > 10; // Loyal customer

    return $isPeakHour && ($isQuick || $isHighValue || ($isLarge && $isLoyal));
}

}
