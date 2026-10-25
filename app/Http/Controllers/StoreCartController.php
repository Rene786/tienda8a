<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class StoreCartController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        abort_unless($user instanceof User, 401);

        $cart = $user->cart()->with('items.product')->first();
        $items = $cart?->items ?? collect();
        $total = $items->sum(
            fn (CartItem $item): float => $item->product === null
                ? 0.0
                : (float) $item->product->price * $item->quantity
        );

        return view('client.cart', compact('items', 'total'));
    }

    public function store(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:1'],
        ]);
        $user = $request->user();
        abort_unless($user instanceof User, 401);

        DB::transaction(function () use ($product, $user, $validated): void {
            $availableProduct = Product::query()
                ->where('status', true)
                ->lockForUpdate()
                ->findOrFail($product->id);
            $cart = $this->getOrCreateCart($user);
            $cartItem = $cart->items()
                ->withTrashed()
                ->where('product_id', $availableProduct->id)
                ->lockForUpdate()
                ->first();
            $quantity = ($cartItem !== null && ! $cartItem->trashed() ? $cartItem->quantity : 0)
                + $validated['quantity'];

            if ($quantity > $availableProduct->stock) {
                throw ValidationException::withMessages([
                    'quantity' => 'La cantidad solicitada supera las existencias disponibles.',
                ]);
            }

            if ($cartItem === null) {
                $cart->items()->create([
                    'product_id' => $availableProduct->id,
                    'quantity' => $quantity,
                ]);

                return;
            }

            $cartItem->quantity = $quantity;
            if ($cartItem->trashed()) {
                $cartItem->restore();

                return;
            }

            $cartItem->save();
        });

        return redirect()
            ->route('store-cart.index')
            ->with('success', 'El producto se agregó al carrito.');
    }

    public function update(Request $request, CartItem $cartItem): RedirectResponse
    {
        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:1'],
        ]);
        $user = $request->user();
        abort_unless($user instanceof User, 401);

        $cart = $user->cart()->firstOrFail();
        $ownedCartItem = $cart->items()->with('product')->findOrFail($cartItem->id);
        $product = $ownedCartItem->product;

        if ($product === null || ! $product->status) {
            return back()
                ->withErrors(['quantity' => 'Este producto ya no está disponible.'])
                ->withInput();
        }

        if ($validated['quantity'] > $product->stock) {
            return back()
                ->withErrors(['quantity' => 'La cantidad solicitada supera las existencias disponibles.'])
                ->withInput();
        }

        $ownedCartItem->update(['quantity' => $validated['quantity']]);

        return redirect()->route('store-cart.index')->with('success', 'La cantidad se actualizó.');
    }

    public function destroy(Request $request, CartItem $cartItem): RedirectResponse
    {
        $user = $request->user();
        abort_unless($user instanceof User, 401);

        $user->cart()->firstOrFail()
            ->items()
            ->findOrFail($cartItem->id)
            ->delete();

        return redirect()->route('store-cart.index')->with('success', 'El producto se quitó del carrito.');
    }

    private function getOrCreateCart(User $user): Cart
    {
        $cart = $user->cart()->withTrashed()->first();

        if ($cart === null) {
            return $user->cart()->create();
        }

        if ($cart->trashed()) {
            $cart->restore();
        }

        return $cart;
    }
}
