<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        // Allow filtering by status via query parameter
        $statusFilter = $request->query('status', ['pending']); // Default to 'received' and 'preparing'
        if (!is_array($statusFilter)) {
            $statusFilter = [$statusFilter];
        }
        $validStatuses = ['pending', 'received', 'preparing', 'ready', 'completed', 'cancelled'];
        $statusFilter = array_intersect($statusFilter, $validStatuses);

        // Log the status filter being applied
        Log::info('Order Index - Status Filter:', ['status' => $statusFilter]);

        $orders = Order::with('orderItems.menu', 'user')
            ->when(!empty($statusFilter), fn($query) => $query->whereIn('status', $statusFilter))
            ->orderBy('is_priority', 'desc')
            ->orderBy('created_at', 'asc')
            ->paginate(10) // Paginate with 10 orders per page
            ->through(fn($order) => [
                'id' => $order->id,
                'user_name' => optional($order->user)->name ?? 'Guest',
                'order_type' => $order->order_type,
                'table_id' => $order->table_id,
                'pickup_time' => $order->pickup_time,
                'delivery_address' => $order->delivery_address,
                'status' => $order->status,
                'total_price' => $order->total_price,
                'is_priority' => $order->is_priority,
                'estimated_wait_minutes' => $order->estimated_wait_minutes,
                'ordered_at' => $order->ordered_at,
                'items' => $order->orderItems->map(fn($item) => [
                    'name' => $item->menu->name,
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                ]),
            ]);

        // Log the orders being returned
        Log::info('Order Index - Orders Retrieved:', ['orders' => $orders->toArray()]);

        return Inertia::render('Admin/Orders/Index', [
            'orders' => $orders,
            'filters' => [
                'status' => $statusFilter,
            ],
        ]);
    }

    public function togglePriority(Order $order)
    {
        try {
            $order->is_priority = !$order->is_priority;
            $order->save();

            return redirect()->route('admin.orders.index')
                ->with('success', 'Priority updated successfully.')
                ->setStatusCode(303);
        } catch (\Exception $e) {
            Log::error('Failed to toggle order priority: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to update priority. Please try again.')
                ->setStatusCode(303);
        }
    }

    public function updateStatus(Request $request, Order $order)
    {
        try {
            $validated = $request->validate([
                'status' => 'required|in:received,preparing,ready,completed,cancelled',
                'estimated_wait_minutes' => 'nullable|integer|min:0',
            ]);

            $order->status = $validated['status'];
            if (isset($validated['estimated_wait_minutes'])) {
                $order->estimated_wait_minutes = $validated['estimated_wait_minutes'];
            }
            $order->save();

            return redirect()->route('admin.orders.index')
                ->with('success', 'Order status updated successfully.')
                ->setStatusCode(303);
        } catch (\Exception $e) {
            Log::error('Failed to update order status: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to update order status. Please try again.')
                ->setStatusCode(303);
        }
    }
}