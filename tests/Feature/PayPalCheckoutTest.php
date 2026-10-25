<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class PayPalCheckoutTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'services.paypal.client_id' => 'sandbox-client-id',
            'services.paypal.client_secret' => 'sandbox-client-secret',
            'services.paypal.environment' => 'sandbox',
            'services.paypal.currency_code' => 'MXN',
        ]);
    }

    public function test_guest_cannot_start_paypal_checkout(): void
    {
        $this->post(route('paypal.checkout'))
            ->assertRedirect(route('login'));
    }

    public function test_checkout_creates_a_pending_order_and_redirects_to_paypal_sandbox(): void
    {
        Http::fake([
            'api-m.sandbox.paypal.com/v1/oauth2/token' => Http::response([
                'access_token' => 'sandbox-access-token',
            ]),
            'api-m.sandbox.paypal.com/v2/checkout/orders' => Http::response([
                'id' => 'PAYPAL-ORDER-123',
                'links' => [
                    [
                        'rel' => 'approve',
                        'href' => 'https://www.sandbox.paypal.com/checkoutnow?token=PAYPAL-ORDER-123',
                    ],
                ],
            ], 201),
        ]);

        $user = User::factory()->create();
        $product = $this->createProduct();
        $this->addToCart($user, $product, 2);

        $this->actingAs($user)
            ->post(route('paypal.checkout'))
            ->assertRedirect('https://www.sandbox.paypal.com/checkoutnow?token=PAYPAL-ORDER-123');

        $order = Order::query()->where('user_id', $user->id)->firstOrFail();
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'total' => '200.00',
            'status' => 'pending',
            'payment_status' => 'pending',
            'paypal_order_id' => 'PAYPAL-ORDER-123',
        ]);
        $this->assertDatabaseHas('order_items', [
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => 2,
            'subtotal' => '200.00',
        ]);

        Http::assertSent(fn ($request): bool => $request->url() === 'https://api-m.sandbox.paypal.com/v2/checkout/orders'
            && $request['intent'] === 'CAPTURE'
            && $request['purchase_units'][0]['amount']['currency_code'] === 'MXN'
            && $request['purchase_units'][0]['amount']['value'] === '200.00'
            && $request['purchase_units'][0]['reference_id'] === $order->order_number
            && $request['application_context']['return_url'] === route('paypal.return'));
    }

    public function test_checkout_requires_sandbox_credentials_and_does_not_create_an_order_without_them(): void
    {
        config(['services.paypal.client_secret' => null]);
        $user = User::factory()->create();
        $this->addToCart($user, $this->createProduct());

        $this->actingAs($user)
            ->from(route('store-cart.index'))
            ->post(route('paypal.checkout'))
            ->assertRedirect(route('store-cart.index'))
            ->assertSessionHasErrors('payment');

        $this->assertDatabaseCount('orders', 0);
        Http::assertNothingSent();
    }

    public function test_paypal_checkout_is_disabled_outside_sandbox(): void
    {
        config(['services.paypal.environment' => 'production']);
        $user = User::factory()->create();
        $this->addToCart($user, $this->createProduct());

        $this->actingAs($user)
            ->post(route('paypal.checkout'))
            ->assertRedirect(route('store-cart.index'))
            ->assertSessionHasErrors('payment');

        $this->assertDatabaseCount('orders', 0);
        Http::assertNothingSent();
    }

    public function test_checkout_rejects_products_that_exceed_available_stock(): void
    {
        $user = User::factory()->create();
        $this->addToCart($user, $this->createProduct(stock: 1), quantity: 2);

        $this->actingAs($user)
            ->from(route('store-cart.index'))
            ->post(route('paypal.checkout'))
            ->assertRedirect(route('store-cart.index'))
            ->assertSessionHasErrors('cart');

        $this->assertDatabaseCount('orders', 0);
        Http::assertNothingSent();
    }

    public function test_checkout_handles_a_paypal_api_failure_without_exposing_an_exception_page(): void
    {
        Http::fake([
            'api-m.sandbox.paypal.com/v1/oauth2/token' => Http::response([
                'access_token' => 'sandbox-access-token',
            ]),
            'api-m.sandbox.paypal.com/v2/checkout/orders' => Http::response([
                'name' => 'AUTHENTICATION_FAILURE',
                'message' => 'Authentication failed due to invalid client credentials.',
            ], 401),
        ]);
        $user = User::factory()->create();
        $this->addToCart($user, $this->createProduct());

        $this->actingAs($user)
            ->post(route('paypal.checkout'))
            ->assertRedirect(route('store-cart.index'))
            ->assertSessionHasErrors('payment');

        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'status' => 'cancelled',
            'payment_status' => 'failed',
            'paypal_order_id' => null,
        ]);
    }

    public function test_return_captures_the_paypal_order_checks_amount_and_clears_cart(): void
    {
        $user = User::factory()->create();
        $order = $this->createOrder($user, 'PAYPAL-ORDER-123');
        $this->addToCart($user, $this->createProduct());

        Http::fake([
            'api-m.sandbox.paypal.com/v1/oauth2/token' => Http::response([
                'access_token' => 'sandbox-access-token',
            ]),
            'api-m.sandbox.paypal.com/v2/checkout/orders/PAYPAL-ORDER-123/capture' => Http::response(
                $this->captureData($order),
                201,
            ),
        ]);

        $this->actingAs($user)
            ->get(route('paypal.return', ['token' => 'PAYPAL-ORDER-123']))
            ->assertRedirect(route('store-cart.index'))
            ->assertSessionHas('success', 'Pago de prueba aprobado. Pedido '.$order->order_number.'.');

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'payment_status' => 'paid',
            'status' => 'processing',
        ]);
        $this->assertSoftDeleted('cart_items', ['product_id' => Product::query()->where('slug', 'mouse-de-prueba')->value('id')]);

        Http::assertSent(fn ($request): bool => str_ends_with($request->url(), '/v2/checkout/orders/PAYPAL-ORDER-123/capture')
            && $request->hasHeader('PayPal-Request-Id')
            && $request->body() === '{}');
    }

    public function test_repeated_paypal_return_does_not_capture_a_sandbox_payment_again(): void
    {
        $user = User::factory()->create();
        $order = $this->createOrder($user, 'PAYPAL-ORDER-123');
        $order->update([
            'payment_status' => 'paid',
            'status' => 'processing',
        ]);

        $this->actingAs($user)
            ->get(route('paypal.return', ['token' => 'PAYPAL-ORDER-123']))
            ->assertRedirect(route('store-cart.index'))
            ->assertSessionHas('success', 'El pago ya fue confirmado. Pedido '.$order->order_number.'.');

        Http::assertNothingSent();
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'payment_status' => 'paid',
            'status' => 'processing',
        ]);
    }

    public function test_admin_orders_list_labels_paypal_sandbox_payment_while_order_is_paid_and_processing(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $order = $this->createOrder($admin, 'PAYPAL-ORDER-123');
        $order->update([
            'payment_status' => 'paid',
            'status' => 'processing',
        ]);

        $this->actingAs($admin)
            ->get(route('orders.index'))
            ->assertOk()
            ->assertSee('Pago de prueba aprobado')
            ->assertSee('Processing')
            ->assertDontSee('>Paid<');
    }

    public function test_customer_cannot_capture_another_users_paypal_order(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $order = $this->createOrder($owner, 'PAYPAL-ORDER-123');

        $this->actingAs($otherUser)
            ->get(route('paypal.return', ['token' => 'PAYPAL-ORDER-123']))
            ->assertNotFound();

        Http::assertNothingSent();
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'payment_status' => 'pending',
        ]);
    }

    public function test_return_does_not_mark_an_order_paid_when_the_capture_amount_does_not_match(): void
    {
        $user = User::factory()->create();
        $order = $this->createOrder($user, 'PAYPAL-ORDER-123');
        $capture = $this->captureData($order);
        $capture['purchase_units'][0]['payments']['captures'][0]['amount']['value'] = '99.00';

        Http::fake([
            'api-m.sandbox.paypal.com/v1/oauth2/token' => Http::response([
                'access_token' => 'sandbox-access-token',
            ]),
            'api-m.sandbox.paypal.com/v2/checkout/orders/PAYPAL-ORDER-123/capture' => Http::response($capture, 201),
        ]);

        $this->actingAs($user)
            ->get(route('paypal.return', ['token' => 'PAYPAL-ORDER-123']))
            ->assertRedirect(route('store-cart.index'))
            ->assertSessionHasErrors('payment');

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'payment_status' => 'pending',
            'status' => 'pending',
        ]);
    }

    public function test_guest_cannot_view_or_capture_a_paypal_return(): void
    {
        $this->get(route('paypal.return', ['token' => 'PAYPAL-ORDER-123']))
            ->assertRedirect(route('login'));
    }

    private function createProduct(int $stock = 5): Product
    {
        $category = Category::create([
            'name' => 'Accesorios',
            'status' => true,
        ]);

        return Product::create([
            'category_id' => $category->id,
            'name' => 'Mouse de prueba',
            'slug' => 'mouse-de-prueba',
            'price' => 100,
            'stock' => $stock,
            'featured' => false,
            'status' => true,
        ]);
    }

    private function addToCart(User $user, Product $product, int $quantity = 1): void
    {
        $cart = Cart::create(['user_id' => $user->id]);
        $cart->items()->create([
            'product_id' => $product->id,
            'quantity' => $quantity,
        ]);
    }

    private function createOrder(User $user, string $paypalOrderId): Order
    {
        return $user->orders()->create([
            'order_number' => 'TS-PAYPAL-'.$user->id,
            'total' => '200.00',
            'status' => 'pending',
            'payment_status' => 'pending',
            'paypal_order_id' => $paypalOrderId,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function captureData(Order $order): array
    {
        return [
            'id' => $order->paypal_order_id,
            'status' => 'COMPLETED',
            'purchase_units' => [
                [
                    'reference_id' => $order->order_number,
                    'payments' => [
                        'captures' => [
                            [
                                'id' => 'PAYPAL-CAPTURE-456',
                                'status' => 'COMPLETED',
                                'amount' => [
                                    'currency_code' => 'MXN',
                                    'value' => number_format((float) $order->total, 2, '.', ''),
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }
}
