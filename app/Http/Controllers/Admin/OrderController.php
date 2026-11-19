<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Book orders: verify payment (on the payments queue or here), pack,
 * hand to the courier and record the tracking code.
 */
class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->string('status')->toString();
        $search = $request->string('search')->trim()->toString();

        $orders = Order::query()
            ->with(['items', 'payments'])
            ->when($status !== '', fn ($query) => $query->where('status', $status))
            ->when($search !== '', fn ($query) => $query->where(fn ($query) => $query
                ->where('reference', 'like', "%{$search}%")
                ->orWhere('phone', 'like', "%{$search}%")
                ->orWhere('customer_name', 'like', "%{$search}%")))
            ->latest()
            ->paginate(30)
            ->withQueryString();

        return view('admin.orders.index', compact('orders', 'status', 'search'));
    }

    public function show(Order $order): View
    {
        $order->load(['items.book', 'payments.verifier', 'deliveryZone']);

        return view('admin.orders.show', compact('order'));
    }

    public function update(Request $request, Order $order): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::enum(OrderStatus::class)],
            'courier' => ['nullable', Rule::in(array_keys(Order::COURIERS))],
            'tracking_code' => ['nullable', 'string', 'max:60'],
            'staff_notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $status = OrderStatus::from($validated['status']);

        $order->update([
            ...$validated,
            'shipped_at' => $status === OrderStatus::Shipped ? ($order->shipped_at ?? now()) : $order->shipped_at,
            'delivered_at' => $status === OrderStatus::Delivered ? ($order->delivered_at ?? now()) : $order->delivered_at,
        ]);

        return back()->with('status', 'অর্ডার আপডেট হয়েছে।');
    }
}
