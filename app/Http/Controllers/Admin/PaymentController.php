<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payment;
use Inertia\Inertia;
use Inertia\Response;

class PaymentController extends Controller
{
    public function index(): Response
    {
        $payments = Payment::with('order')->orderBy('paid_at', 'desc')->get();
        return Inertia::render('Admin/Payments/Index', ['payments' => $payments]);
    }

    public function update(Request $request, Payment $payment)
    {
        $request->validate(['status' => 'required|in:pending,paid,failed']);
        $payment->update(['status' => $request->status]);

        return redirect()->route('admin.payments.index')->with('success', 'Payment status updated!');
    }

    public function destroy(Payment $payment)
    {
        $payment->delete();
        return redirect()->route('admin.payments.index')->with('success', 'Payment deleted!');
    }
}

