<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class OrderController extends Controller
{
    private const ORDER_STATUSES = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];

    private const PAYMENT_STATUSES = ['pending', 'paid', 'failed', 'refunded'];

    public function index(): View
    {
        $orders = Order::with(['user' => fn ($query) => $query->withTrashed()])
            ->withCount('items')
            ->latest()
            ->paginate(10);

        return view('admin.orders.index', compact('orders'));
    }

    public function create(): View
    {
        $users = User::orderBy('name')->get();
        $orderStatuses = self::ORDER_STATUSES;
        $paymentStatuses = self::PAYMENT_STATUSES;

        return view('admin.orders.create', compact('users', 'orderStatuses', 'paymentStatuses'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'user_id' => ['required', 'integer', Rule::exists('users', 'id')->whereNull('deleted_at')],
            'order_number' => ['required', 'string', 'max:50', 'unique:orders,order_number'],
            'total' => ['required', 'numeric', 'min:0', 'max:99999999.99'],
            'status' => ['required', Rule::in(self::ORDER_STATUSES)],
            'payment_status' => ['required', Rule::in(self::PAYMENT_STATUSES)],
            'shipping_address' => ['nullable', 'string'],
        ]);

        Order::create($validated);

        return redirect()->route('orders.index')->with('success', 'El pedido se registró correctamente.');
    }

    public function edit(Order $order): View
    {
        $users = User::withTrashed()->orderBy('name')->get();
        $orderStatuses = self::ORDER_STATUSES;
        $paymentStatuses = self::PAYMENT_STATUSES;

        return view('admin.orders.edit', compact('order', 'users', 'orderStatuses', 'paymentStatuses'));
    }

    public function update(Request $request, Order $order): RedirectResponse
    {
        $validated = $request->validate([
            'user_id' => ['required', 'integer', Rule::exists('users', 'id')->where(fn ($query) => $query->whereNull('deleted_at')->orWhere('id', $order->user_id))],
            'order_number' => ['required', 'string', 'max:50', Rule::unique('orders', 'order_number')->ignore($order->id)],
            'total' => ['required', 'numeric', 'min:0', 'max:99999999.99'],
            'status' => ['required', Rule::in(self::ORDER_STATUSES)],
            'payment_status' => ['required', Rule::in(self::PAYMENT_STATUSES)],
            'shipping_address' => ['nullable', 'string'],
        ]);

        $order->update($validated);

        return redirect()->route('orders.index')->with('success', 'El pedido se actualizó correctamente.');
    }

    public function destroy(Order $order): RedirectResponse
    {
        $order->delete();

        return redirect()->route('orders.index')->with('success', 'El pedido se eliminó correctamente.');
    }
}
