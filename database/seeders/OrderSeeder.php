<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use RuntimeException;

class OrderSeeder extends Seeder
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
            ->orderBy('id')
            ->get();

        if ($clients->isEmpty() || $products->count() < 4) {
            throw new RuntimeException('UserSeeder y ProductSeeder deben ejecutarse antes de OrderSeeder.');
        }

        $statuses = ['delivered', 'shipped', 'processing', 'pending', 'cancelled', 'delivered', 'delivered', 'shipped', 'pending', 'processing', 'cancelled', 'delivered'];
        $orderCount = 100 + ($clients->count() * 3);

        for ($orderNumber = 1; $orderNumber <= $orderCount; $orderNumber++) {
            $status = $statuses[($orderNumber - 1) % count($statuses)];
            $itemCount = ($orderNumber % 4) + 1;
            $orderItems = [];
            $totalInCents = 0;

            for ($itemIndex = 0; $itemIndex < $itemCount; $itemIndex++) {
                $product = $products[($orderNumber + $itemIndex - 1) % $products->count()];
                $quantity = (($orderNumber + $itemIndex) % 3) + 1;
                $unitPriceInCents = (int) round((float) $product->price * 100);
                $subtotalInCents = $unitPriceInCents * $quantity;
                $totalInCents += $subtotalInCents;

                $orderItems[] = [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'unit_price' => number_format($unitPriceInCents / 100, 2, '.', ''),
                    'quantity' => $quantity,
                    'subtotal' => number_format($subtotalInCents / 100, 2, '.', ''),
                ];
            }

            $paymentStatus = match ($status) {
                'cancelled' => 'refunded',
                'pending' => 'pending',
                default => 'paid',
            };

            $order = Order::withTrashed()->firstOrCreate(
                ['order_number' => sprintf('DEMO-%s-%03d', now()->format('Y'), $orderNumber)],
                [
                    'user_id' => $clients[($orderNumber - 1) % $clients->count()]->id,
                    'total' => number_format($totalInCents / 100, 2, '.', ''),
                    'status' => $status,
                    'payment_status' => $paymentStatus,
                    'shipping_address' => fake()->streetAddress().', '.fake()->city().', '.fake()->state(),
                ],
            );

            if ($order->trashed()) {
                $order->restore();
                OrderItem::withTrashed()->where('order_id', $order->id)->restore();
            }

            if ($order->items()->doesntExist()) {
                $order->items()->createMany($orderItems);
            }
        }
    }
}
