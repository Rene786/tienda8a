<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class OrderItemController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'status' => ['nullable', Rule::in(['all', 'deleted'])],
        ]);

        $orderItems = OrderItem::withTrashed()
            ->with([
                'order' => fn ($query) => $query->withTrashed(),
                'product' => fn ($query) => $query->withTrashed(),
            ])
            ->when(($filters['status'] ?? 'all') === 'deleted', fn ($query) => $query->onlyTrashed())
            ->when(($filters['status'] ?? 'all') === 'all', fn ($query) => $query->withoutTrashed())
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $statusOptions = ['all' => 'Vigentes', 'deleted' => 'Eliminados'];

        return view('admin.orders.items.index', compact('orderItems', 'statusOptions'));
    }

    public function create(): View
    {
        $orders = Order::latest()->get();
        $products = Product::where('status', true)->orderBy('name')->get();

        return view('admin.orders.items.create', compact('orders', 'products'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'order_id' => ['required', 'integer', Rule::exists('orders', 'id')->whereNull('deleted_at')],
            'product_id' => ['required', 'integer', Rule::exists('products', 'id')->whereNull('deleted_at')],
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $product = Product::findOrFail($validated['product_id']);
        $subtotal = round((float) $product->price * $validated['quantity'], 2);

        DB::transaction(function () use ($validated, $product, $subtotal): void {
            OrderItem::create([
                ...$validated,
                'product_name' => $product->name,
                'unit_price' => $product->price,
                'subtotal' => $subtotal,
            ]);

            $this->recalculateOrderTotal((int) $validated['order_id']);
        });

        return redirect()->route('order-items.index')->with('success', 'El artículo se agregó correctamente.');
    }

    public function edit(OrderItem $orderItem): View
    {
        $orders = Order::withTrashed()->latest()->get();
        $products = $this->productsForEdit($orderItem);

        return view('admin.orders.items.edit', compact('orderItem', 'orders', 'products'));
    }

    public function update(Request $request, OrderItem $orderItem): RedirectResponse
    {
        $validated = $request->validate([
            'order_id' => ['required', 'integer', Rule::exists('orders', 'id')->where(fn ($query) => $query->whereNull('deleted_at')->orWhere('id', $orderItem->order_id))],
            'product_id' => ['nullable', 'integer', Rule::exists('products', 'id')->where(fn ($query) => $query->whereNull('deleted_at')->orWhere('id', $orderItem->product_id))],
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $previousOrderId = $orderItem->order_id;
        $product = filled($validated['product_id'] ?? null)
            ? Product::withTrashed()->findOrFail($validated['product_id'])
            : null;
        $productName = $product?->name ?? $orderItem->product_name;
        $unitPrice = $product?->price ?? $orderItem->unit_price;
        $subtotal = round((float) $unitPrice * $validated['quantity'], 2);

        DB::transaction(function () use ($validated, $orderItem, $product, $productName, $unitPrice, $subtotal, $previousOrderId): void {
            $orderItem->update([
                'order_id' => $validated['order_id'],
                'product_id' => $product?->id,
                'product_name' => $productName,
                'unit_price' => $unitPrice,
                'quantity' => $validated['quantity'],
                'subtotal' => $subtotal,
            ]);

            $this->recalculateOrderTotal($previousOrderId);
            if ($previousOrderId !== (int) $validated['order_id']) {
                $this->recalculateOrderTotal((int) $validated['order_id']);
            }
        });

        return redirect()->route('order-items.index')->with('success', 'El artículo se actualizó correctamente.');
    }

    public function destroy(OrderItem $orderItem): RedirectResponse
    {
        $orderId = $orderItem->order_id;

        DB::transaction(function () use ($orderItem, $orderId): void {
            $orderItem->delete();
            $this->recalculateOrderTotal($orderId);
        });

        return redirect()->route('order-items.index')->with('success', 'El artículo se eliminó correctamente.');
    }

    private function productsForEdit(OrderItem $orderItem): Collection
    {
        $products = Product::where('status', true)->orderBy('name')->get();

        if ($orderItem->product_id && ! $products->contains('id', $orderItem->product_id)) {
            $products->push(Product::withTrashed()->find($orderItem->product_id));
        }

        return $products->filter();
    }

    private function recalculateOrderTotal(int $orderId): void
    {
        $total = OrderItem::where('order_id', $orderId)->sum('subtotal');

        Order::withTrashed()->whereKey($orderId)->update(['total' => $total]);
    }
}
