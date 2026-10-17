<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPaginationTest extends TestCase
{
    use RefreshDatabase;

    public function test_order_pagination_uses_working_url_links(): void
    {
        $this->createOrders();

        $this->actingAs(User::where('email', 'admin-pagination@example.test')->firstOrFail())
            ->get(route('orders.index'))
            ->assertOk()
            ->assertSee('PAGINATION-01')
            ->assertDontSee('PAGINATION-11')
            ->assertSee('?page=2')
            ->assertDontSee('wire:click="gotoPage');
    }

    public function test_order_pagination_displays_the_requested_page(): void
    {
        $this->createOrders();

        $this->actingAs(User::where('email', 'admin-pagination@example.test')->firstOrFail())
            ->get('/orders?page=2')
            ->assertOk()
            ->assertSee('PAGINATION-11')
            ->assertDontSee('PAGINATION-01');
    }

    private function createOrders(): void
    {
        $user = User::create([
            'name' => 'Administrador',
            'email' => 'admin-pagination@example.test',
            'password' => 'password',
        ]);

        for ($orderNumber = 1; $orderNumber <= 11; $orderNumber++) {
            Order::create([
                'user_id' => $user->id,
                'order_number' => sprintf('PAGINATION-%02d', $orderNumber),
                'total' => '100.00',
                'status' => 'pending',
                'payment_status' => 'pending',
                'created_at' => now()->addMinutes($orderNumber),
                'updated_at' => now()->addMinutes($orderNumber),
            ]);
        }
    }
}
