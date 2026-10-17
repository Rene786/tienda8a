<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DatabaseSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_database_seeder_creates_consistent_repeatable_demo_data(): void
    {
        $this->seed();

        self::assertSame(26, User::count());
        self::assertSame(25, User::where('role', 'cliente')->count());
        self::assertSame(62, Product::count());
        self::assertSame(12, Product::whereHas('category', fn ($query) => $query->where('name', 'Componentes'))->count());
        self::assertTrue(Product::where('name', 'like', 'Tarjeta gráfica%')->exists());
        self::assertTrue(Product::where('name', 'like', 'Memoria RAM%')->exists());

        $carts = Cart::with('items.product', 'user')->get();
        self::assertCount(25, $carts);

        foreach ($carts as $cart) {
            self::assertSame('cliente', $cart->user->role);
            self::assertGreaterThanOrEqual(2, $cart->items->count());
            self::assertLessThanOrEqual(5, $cart->items->count());

            foreach ($cart->items as $cartItem) {
                self::assertNotNull($cartItem->product);
                self::assertTrue($cartItem->product->status);
                self::assertGreaterThan(0, $cartItem->quantity);
                self::assertLessThanOrEqual($cartItem->product->stock, $cartItem->quantity);
            }
        }

        $orders = Order::with('items')->get();
        self::assertCount(175, $orders);

        foreach (User::where('role', 'cliente')->get() as $client) {
            self::assertGreaterThanOrEqual(3, $client->orders()->count());
        }

        $lineCounts = $orders->map(fn (Order $order): int => $order->items->count());
        self::assertSame(1, $lineCounts->min());
        self::assertSame(4, $lineCounts->max());

        foreach ($orders as $order) {
            $totalInCents = (int) $order->items->sum(
                fn ($item): int => (int) round((float) $item->subtotal * 100),
            );

            self::assertSame(number_format($totalInCents / 100, 2, '.', ''), $order->total);
        }

        $this->seed();

        self::assertSame(26, User::count());
        self::assertSame(62, Product::count());
        self::assertSame(25, Cart::count());
        self::assertGreaterThanOrEqual(2, CartItem::count());
        self::assertSame(175, Order::count());
    }
}
