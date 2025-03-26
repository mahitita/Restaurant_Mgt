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
        $user = Auth::guard('web')->user();
        return Inertia::render('Cart', [
            'auth' => [
                'user' => $user,
            ],
        ]);
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

                // Calculate prep time and priority
                $cartItems = collect($request->cart);
                $isPeakHour = $this->isPeakHour(now());
                $isPriority = $this->determinePriority($cartItems, $totalPrice, $isPeakHour);
                $estimatedWaitMinutes = $this->calculatePrepTime($cartItems);

                $order = Order::create([
                    'user_id' => Auth::id(),
                    'order_type' => $request->order_type,
                    'status' => 'pending',
                    'total_price' => $totalPrice,
                    'table_id' => $request->table_id,
                    'pickup_time' => $request->pickup_time,
                    'delivery_address' => $request->delivery_address,
                    'estimated_wait_minutes' => $estimatedWaitMinutes,
                    'is_priority' => $isPriority,
                ]);
                Log::info('Order created:', ['order_id' => $order->id, 'estimated_wait_minutes' => $estimatedWaitMinutes, 'is_priority' => $isPriority]);

                foreach ($request->cart as $item) {
                    if (!$this->checkInventory($item['id'], $item['quantity'])) {
                        throw new \Exception("Insufficient inventory for menu item ID {$item['id']}");
                    }

                    OrderItem::create([
                        'order_id' => $order->id,
                        'menu_id' => $item['id'],
                        'quantity' => $item['quantity'],
                        'price' => $item['price'],
                    ]);
                    Log::info('Order item created:', ['menu_id' => $item['id'], 'quantity' => $item['quantity']]);

                    // Deduct inventory after creating order item
                    $this->deductInventory($item['id'], $item['quantity'], $order->id);
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
                    ->with('success', "Order placed successfully! Estimated wait: {$estimatedWaitMinutes} minutes.");
            });
        } catch (\Exception $e) {
            Log::error('Order store failed:', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    private function calculatePrepTime($cartItems, $referenceTime = null)
    {
        $referenceTime = $referenceTime ? Carbon::parse($referenceTime) : now();
        $baseTime = 5;
        $orderPrepTime = $cartItems->sum(function ($item) {
            $menu = Menu::find($item['id']);
            $prepTimePerItem = $menu->prep_time ?? 15;
            return $prepTimePerItem * $item['quantity'];
        });

        $totalPrice = $cartItems->sum(fn($item) => $item['price'] * $item['quantity']);
        $isPeakHour = $this->isPeakHour($referenceTime);
        $isPriority = $this->determinePriority($cartItems, $totalPrice, $isPeakHour);

        $ordersAhead = Order::whereIn('status', ['pending', 'received', 'preparing'])
            ->where('created_at', '<', $referenceTime)
            ->with('orderItems.menu')
            ->get();

        $totalPrepTimeAhead = $ordersAhead->sum(function ($order) {
            return $order->orderItems->sum(fn($item) => ($item->menu->prep_time ?? 15) * $item->quantity);
        });

        $priorityOrdersAhead = $ordersAhead->where('is_priority', true);
        $nonPriorityOrdersAhead = $ordersAhead->where('is_priority', false);

        $priorityPrepTimeAhead = $priorityOrdersAhead->sum(function ($order) {
            return $order->orderItems->sum(fn($item) => ($item->menu->prep_time ?? 15) * $item->quantity);
        });

        $nonPriorityPrepTimeAhead = $nonPriorityOrdersAhead->sum(function ($order) {
            return $order->orderItems->sum(fn($item) => ($item->menu->prep_time ?? 15) * $item->quantity);
        });

        $numberOfChefs = config('app.number_of_chefs', 3);
        $priorityKitchenLoad = $numberOfChefs > 0 ? $priorityPrepTimeAhead / $numberOfChefs : $priorityPrepTimeAhead;
        $nonPriorityKitchenLoad = $numberOfChefs > 0 ? $nonPriorityPrepTimeAhead / $numberOfChefs : $nonPriorityPrepTimeAhead;

        $peakHourFactor = $isPeakHour ? 1.2 : 1.0;
        $effectiveKitchenLoad = $isPriority
            ? $priorityKitchenLoad
            : ($priorityKitchenLoad + $nonPriorityKitchenLoad);

        $estimatedWaitMinutes = round(($baseTime + $orderPrepTime + $effectiveKitchenLoad) * $peakHourFactor);
        $estimatedWaitMinutes = max(10, $estimatedWaitMinutes);

        Log::info('Wait time calculation:', [
            'orderPrepTime' => $orderPrepTime,
            'priorityOrdersAhead' => $priorityOrdersAhead->count(),
            'nonPriorityOrdersAhead' => $nonPriorityOrdersAhead->count(),
            'priorityPrepTimeAhead' => $priorityPrepTimeAhead,
            'nonPriorityPrepTimeAhead' => $nonPriorityPrepTimeAhead,
            'numberOfChefs' => $numberOfChefs,
            'effectiveKitchenLoad' => $effectiveKitchenLoad,
            'peakHourFactor' => $peakHourFactor,
            'isPriority' => $isPriority,
            'estimatedWaitMinutes' => $estimatedWaitMinutes,
        ]);

        return $estimatedWaitMinutes;
    }

    private function determinePriority($items, $totalPrice, $isPeakHour)
    {
        $itemCount = $items->sum('quantity');
        $user = Auth::user();

        $isHighValueOrder = $totalPrice > 50 || $itemCount > 5;
        $isLoyalCustomer = $user->orders()->count() > 10;
        $hasReservation = Reservation::where('user_id', $user->id)
            ->where('status', 'confirmed')
            ->whereDate('reservation_time', now()->toDateString())
            ->exists();

        $isPriority = $isPeakHour && ($isHighValueOrder || $isLoyalCustomer || $hasReservation);

        Log::info('Priority determination:', [
            'isPeakHour' => $isPeakHour,
            'isHighValueOrder' => $isHighValueOrder,
            'isLoyalCustomer' => $isLoyalCustomer,
            'hasReservation' => $hasReservation,
            'isPriority' => $isPriority,
        ]);

        return $isPriority;
    }

    private function isPeakHour($time)
    {
        $hour = $time->hour;
        return ($hour >= 12 && $hour <= 14) || ($hour >= 18 && $hour <= 20);
    }

    public function confirmation($orderId)
    {
        $order = Order::with('orderItems')->findOrFail($orderId);
        if ($order->user_id !== Auth::guard('web')->id()) { // Use 'web' guard for customers
            abort(403, 'Unauthorized');
        }

        $payment = Payment::where('order_id', $order->id)
            ->orWhereHas('reservation', fn($query) => $query->where('table_id', $order->table_id))
            ->firstOrFail();

        $reservations = Reservation::where('user_id', Auth::guard('web')->id())
            ->whereDate('reservation_time', now()->toDateString())
            ->get()
            ->map(fn($reservation) => [
                'id' => $reservation->id,
                'table_id' => $reservation->table_id,
                'reservation_time' => $reservation->reservation_time->toDateTimeString(),
            ])
            ->all();

        return inertia('OrderConfirmation', [ // Updated to match component path
            'order' => [
                'id' => $order->id,
                'order_type' => $order->order_type,
                'total_price' => $order->total_price,
                'table_id' => $order->table_id,
                'pickup_time' => $order->pickup_time,
                'delivery_address' => $order->delivery_address,
                'estimated_wait_minutes' => $order->estimated_wait_minutes ?? 0,
                'order_items' => $order->orderItems->map(fn($item) => [
                    'id' => $item->id,
                    'menu_id' => $item->menu_id,
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                ])->all(),
            ],
            'payment' => [
                'payment_method' => $payment->payment_method,
                'amount' => $payment->amount,
                'deposit_amount' => $payment->deposit_amount ?? 0,
                'deposit_refunded' => $payment->deposit_refunded ?? false,
                'paid_at' => $payment->paid_at,
                'status' => $payment->status,
            ],
            'reservations' => $reservations,
            'success' => session('flash.success', 'Order and payment processed successfully!'),
        ]);
    }

    private function deductInventory($menuId, $quantity, $orderId = null)
    {
        $menu = Menu::with('inventories')->find($menuId);
        if (!$menu) {
            throw new \Exception("Menu item not found: {$menuId}");
        }

        foreach ($menu->inventories as $ingredient) {
            $requiredQuantity = $ingredient->pivot->quantity * $quantity;
            $inventory = Inventory::where('name', $ingredient->name)->first();

            if (!$inventory) {
                throw new \Exception("Inventory not found for ingredient: {$ingredient->name}");
            }

            if ($inventory->isExpired()) {
                throw new \Exception("Cannot deduct expired stock: {$ingredient->name}");
            }

            if ($inventory->remaining_quantity < $requiredQuantity) {
                throw new \Exception("Insufficient stock for {$ingredient->name}. Available: {$inventory->remaining_quantity}, Required: {$requiredQuantity}");
            }

            // Deduct the inventory
            $originalQuantity = $inventory->quantity;
            $originalRemaining = $inventory->remaining_quantity;
            $inventory->quantity -= $requiredQuantity;
            $inventory->remaining_quantity -= $requiredQuantity;
            $inventory->save();

            // Log the inventory deduction with more details
            InventoryLog::create([
                'inventory_id' => $inventory->id,
                'action' => 'deducted',
                'quantity' => (string) $requiredQuantity,
                'reason' => $orderId ? "Order placed (Order ID: {$orderId})" : "Manual deduction",
            ]);

            Log::info('Inventory deducted:', [
                'ingredient' => $ingredient->name,
                'menu_id' => $menuId,
                'deducted_quantity' => $requiredQuantity,
                'original_quantity' => $originalQuantity,
                'new_quantity' => $inventory->quantity,
                'original_remaining' => $originalRemaining,
                'new_remaining' => $inventory->remaining_quantity,
                'change_type' => 'deduction',
                'reason' => $orderId ? "Order placed (Order ID: {$orderId})" : "Manual deduction",
            ]);

            // Check for low stock after deduction
            $lowStockThreshold = $inventory->threshold;
            if ($inventory->remaining_quantity <= $lowStockThreshold) {
                Log::warning('Low stock alert:', [
                    'ingredient' => $inventory->name,
                    'remaining_quantity' => $inventory->remaining_quantity,
                    'threshold' => $lowStockThreshold,
                ]);
            }
        }
    }

    private function checkInventory($menuId, $quantity)
    {
        $menu = Menu::with('inventories')->find($menuId);
        if (!$menu) {
            Log::error('Menu item not found:', ['menu_id' => $menuId]);
            return false;
        }

        foreach ($menu->inventories as $ingredient) {
            $requiredQuantity = $ingredient->pivot->quantity * $quantity;
            $inventory = Inventory::where('name', $ingredient->name)->first();

            if (!$inventory) {
                Log::error('Inventory not found for ingredient:', ['ingredient' => $ingredient->name]);
                return false;
            }

            if ($inventory->isExpired()) {
                Log::error('Ingredient is expired:', ['ingredient' => $ingredient->name, 'expiry_date' => $inventory->expiry_date]);
                return false;
            }

            if ($inventory->remaining_quantity < $requiredQuantity) {
                Log::warning('Insufficient inventory for ingredient:', [
                    'ingredient' => $ingredient->name,
                    'available' => $inventory->remaining_quantity,
                    'required' => $requiredQuantity,
                ]);
                return false;
            }

            Log::info('Inventory check passed for ingredient:', [
                'ingredient' => $ingredient->name,
                'available' => $inventory->remaining_quantity,
                'required' => $requiredQuantity,
            ]);
        }

        return true;
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
            'payment.paymentType' => 'required|in:card,bank_transfer,cash',
            'payment.accountNumber' => 'required_if:payment.paymentType,!=,cash|string|max:255',
        ]);

        $reservations = Reservation::whereIn('id', $request->reservation_ids)
            ->where('user_id', Auth::id())
            ->where('status', 'confirmed')
            ->get();

        if ($reservations->isEmpty()) {
            abort(403, 'Invalid or unauthorized reservation.');
        }

        return DB::transaction(function () use ($request, $reservations) {
            $totalPrice = collect($request->cart)->sum(fn($item) => $item['price'] * $item['quantity']);
            $cartItems = collect($request->cart);
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
                $this->deductInventory($item['id'], $item['quantity'], $order->id);
            }

            foreach ($reservations as $reservation) {
                $reservation->order_id = $order->id;
                $reservation->save();
            }

            $paymentMethodMap = [
                'card' => 'cbe_birr',
                'bank_transfer' => 'amole',
                'cash' => 'cash',
            ];
            $frontendPaymentType = $request->input('payment.paymentType');
            $backendPaymentMethod = $paymentMethodMap[$frontendPaymentType] ?? 'cash';

            Payment::create([
                'order_id' => $order->id,
                'payment_method' => $backendPaymentMethod,
                'amount' => $totalPrice,
                'deposit_amount' => 0,
                'paid_at' => $frontendPaymentType === 'cash' ? null : now(),
                'status' => $frontendPaymentType === 'cash' ? 'pending' : 'paid',
            ]);

            return redirect()->route('orders.track', $order->id)
                ->with('success', 'Pre-order placed! Estimated wait: ' . $prepTime . ' minutes.');
        });
    }

    public function preorder(Request $request)
    {
        $reservationIds = is_array($request->query('reservation_ids'))
            ? $request->query('reservation_ids')
            : explode(',', $request->query('reservation_ids'));
        $reservations = Reservation::whereIn('id', $reservationIds)
            ->where('user_id', Auth::id())
            ->where('status', 'confirmed')
            ->with('table')
            ->get();

        if ($reservations->isEmpty()) {
            abort(403, 'Invalid or unauthorized reservation.');
        }

        $menuItems = Menu::where('available', true)->get()->map(fn($item) => [
            'id' => $item->id,
            'name' => $item->name,
            'price' => $item->price,
            'image' => $item->image,
        ]);

        return Inertia::render('Orders/Preorder', [
            'reservations' => $reservations->map(fn($r) => [
                'id' => $r->id,
                'table_number' => $r->table->table_number,
                'reservation_time' => $r->reservation_time->toDateTimeString(),
            ]),
            'menuItems' => $menuItems,
        ]);
    }


    public function track($orderId)
    {
        $order = Order::with('orderItems.menu')->findOrFail($orderId);
        if ($order->user_id !== Auth::guard('web')->id()) {
            abort(403, 'Unauthorized');
        }

        $payment = Payment::where('order_id', $order->id)
            ->orWhereHas('reservation', fn($query) => $query->where('table_id', $order->table_id))
            ->firstOrFail();

        $reservations = Reservation::where('user_id', Auth::guard('web')->id())
            ->whereDate('reservation_time', now()->toDateString())
            ->get()
            ->map(fn($reservation) => [
                'id' => $reservation->id,
                'table_id' => $reservation->table_id,
                'reservation_time' => $reservation->reservation_time->toDateTimeString(),
            ])
            ->all();

        return Inertia::render('Orders/Track', [
            'order' => [
                'id' => $order->id,
                'order_type' => $order->order_type,
                'status' => $order->status,
                'total_price' => $order->total_price,
                'table_id' => $order->table_id,
                'pickup_time' => $order->pickup_time?->toDateTimeString(),
                'delivery_address' => $order->delivery_address,
                'estimated_wait_minutes' => $order->estimated_wait_minutes ?? 0,
                'created_at' => $order->created_at->toDateTimeString(),
                'order_items' => $order->orderItems->map(fn($item) => [
                    'id' => $item->id,
                    'menu_id' => $item->menu_id,
                    'name' => $item->menu->name,
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                    'image' => $item->menu->image,
                ])->all(),
            ],
            'payment' => [
                'payment_method' => $payment->payment_method,
                'amount' => $payment->amount,
                'deposit_amount' => $payment->deposit_amount ?? 0,
                'deposit_refunded' => $payment->deposit_refunded ?? false,
                'paid_at' => $payment->paid_at?->toDateTimeString(),
                'status' => $payment->status,
            ],
            'reservations' => $reservations,
            'success' => session('flash.success'),
        ]);
    }

    public function myOrders(Request $request)
    {
        $orders = Order::with('orderItems.menu')
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->through(fn($order) => [
                'id' => $order->id,
                'order_type' => $order->order_type,
                'status' => $order->status,
                'total_price' => $order->total_price,
                'is_priority' => $order->is_priority,
                'estimated_wait_minutes' => $order->estimated_wait_minutes,
                'ordered_at' => $order->ordered_at->toDateTimeString(),
                'table_id' => $order->table_id,
                'pickup_time' => $order->pickup_time?->toDateTimeString(),
                'delivery_address' => $order->delivery_address,
                'items' => $order->orderItems->map(fn($item) => [
                    'name' => $item->menu->name,
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                ]),
            ]);

        return Inertia::render('Orders/MyOrders', [
            'orders' => $orders,
        ]);
    }
}
