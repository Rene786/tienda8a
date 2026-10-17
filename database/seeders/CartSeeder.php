<?php

namespace Database\Seeders;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use RuntimeException;

class CartSeeder extends Seeder
{
    public function run(): void
    {
        $clients = User::query()
            ->where('role', 'cliente')
            ->where(function ($query): void {
                $query->where('email', 'test@example.com')
                    ->orWhere('email', 'like', 'cliente%@techstore.test');
            })
            ->orderBy('email')
            ->get();
        $products = Product::query()
            ->where('status', true)
            ->where('stock', '>', 0)
            ->orderBy('id')
            ->get();

        if ($clients->isEmpty() || $products->count() < 5) {
            throw new RuntimeException('UserSeeder y ProductSeeder deben ejecutarse antes de CartSeeder.');
        }

        foreach ($clients as $cartIndex => $client) {
            $cart = Cart::withTrashed()->firstOrCreate(['user_id' => $client->id]);

            if ($cart->trashed()) {
                $cart->restore();
                CartItem::withTrashed()->where('cart_id', $cart->id)->restore();
            }

            $itemCount = 2 + ($cartIndex % 4);

            for ($itemIndex = 0; $itemIndex < $itemCount; $itemIndex++) {
                $product = $products[($cartIndex * 5 + $itemIndex) % $products->count()];
                $quantity = min((($cartIndex + $itemIndex) % 3) + 1, $product->stock);

                $cartItem = CartItem::withTrashed()->firstOrCreate(
                    [
                        'cart_id' => $cart->id,
                        'product_id' => $product->id,
                    ],
                    ['quantity' => $quantity],
                );

                if ($cartItem->trashed()) {
                    $cartItem->restore();
                }

                if ($cartItem->quantity !== $quantity) {
                    $cartItem->update(['quantity' => $quantity]);
                }
            }
        }
    }
}
