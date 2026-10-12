<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CartController extends Controller
{
    public function index(): View
    {
        $carts = Cart::with('user')
            ->withCount('items')
            ->latest()
            ->paginate(10);

        return view('carts.index', compact('carts'));
    }

    public function create(): View
    {
        $users = User::whereDoesntHave('cart', fn ($query) => $query->withTrashed())
            ->orderBy('name')
            ->get();

        return view('carts.create', compact('users'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'user_id' => [
                'required',
                'integer',
                Rule::exists('users', 'id')->whereNull('deleted_at'),
                'unique:carts,user_id',
            ],
        ]);

        Cart::create($validated);

        return redirect()->route('carts.index')->with('success', 'El carrito se registró correctamente.');
    }

    public function edit(int $id): View
    {
        $cart = Cart::findOrFail($id);
        $users = User::where(function ($query) use ($cart) {
            $query->whereDoesntHave('cart', fn ($cartQuery) => $cartQuery->withTrashed())
                ->orWhereKey($cart->user_id);
        })
            ->orderBy('name')
            ->get();

        return view('carts.edit', compact('cart', 'users'));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $cart = Cart::findOrFail($id);

        $validated = $request->validate([
            'user_id' => [
                'required',
                'integer',
                Rule::exists('users', 'id')->whereNull('deleted_at'),
                Rule::unique('carts', 'user_id')->ignore($cart->id),
            ],
        ]);

        $cart->update($validated);

        return redirect()->route('carts.index')->with('success', 'El carrito se actualizó correctamente.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $cart = Cart::findOrFail($id);
        $cart->delete();

        return redirect()->route('carts.index')->with('success', 'El carrito se eliminó correctamente.');
    }
}
