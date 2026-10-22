<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CartItemController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'status' => ['nullable', Rule::in(['all', 'deleted'])],
        ]);

        $cartItems = CartItem::withTrashed()
            ->with([
                'cart' => fn ($query) => $query->withTrashed()->with('user'),
                'product' => fn ($query) => $query->withTrashed(),
            ])
            ->when(($filters['status'] ?? 'all') === 'deleted', fn ($query) => $query->onlyTrashed())
            ->when(($filters['status'] ?? 'all') === 'all', fn ($query) => $query->withoutTrashed())
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $statusOptions = ['all' => 'Vigentes', 'deleted' => 'Eliminados'];

        return view('admin.carts.items.index', compact('cartItems', 'statusOptions'));
    }

    public function create(): View
    {
        $carts = Cart::with('user')->latest()->get();
        $products = Product::where('status', true)->orderBy('name')->get();

        return view('admin.carts.items.create', compact('carts', 'products'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'cart_id' => ['required', 'integer', Rule::exists('carts', 'id')->whereNull('deleted_at')],
            'product_id' => ['required', 'integer', Rule::exists('products', 'id')->whereNull('deleted_at')],
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $existingCartItem = CartItem::withTrashed()
            ->where('cart_id', $validated['cart_id'])
            ->where('product_id', $validated['product_id'])
            ->first();

        if ($existingCartItem && ! $existingCartItem->trashed()) {
            return back()
                ->withErrors(['product_id' => 'Este producto ya está agregado a ese carrito.'])
                ->withInput();
        }

        if ($existingCartItem) {
            $existingCartItem->quantity = $validated['quantity'];
            $existingCartItem->restore();
        } else {
            CartItem::create($validated);
        }

        return redirect()->route('cart-items.index')->with('success', 'El artículo se agregó correctamente.');
    }

    public function edit(CartItem $cartItem): View
    {
        $carts = Cart::withTrashed()->with('user')->latest()->get();
        $products = Product::withTrashed()->orderBy('name')->get();

        return view('admin.carts.items.edit', compact('cartItem', 'carts', 'products'));
    }

    public function update(Request $request, CartItem $cartItem): RedirectResponse
    {
        $validated = $request->validate([
            'cart_id' => ['required', 'integer', Rule::exists('carts', 'id')->where(fn ($query) => $query->whereNull('deleted_at')->orWhere('id', $cartItem->cart_id))],
            'product_id' => ['required', 'integer', Rule::exists('products', 'id')->where(fn ($query) => $query->whereNull('deleted_at')->orWhere('id', $cartItem->product_id))],
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $duplicate = CartItem::withTrashed()
            ->where('cart_id', $validated['cart_id'])
            ->where('product_id', $validated['product_id'])
            ->where('id', '!=', $cartItem->id)
            ->exists();

        if ($duplicate) {
            return back()
                ->withErrors(['product_id' => 'Este producto ya está registrado en ese carrito.'])
                ->withInput();
        }

        $cartItem->update($validated);

        return redirect()->route('cart-items.index')->with('success', 'El artículo se actualizó correctamente.');
    }

    public function destroy(CartItem $cartItem): RedirectResponse
    {
        $cartItem->delete();

        return redirect()->route('cart-items.index')->with('success', 'El artículo se eliminó correctamente.');
    }
}
