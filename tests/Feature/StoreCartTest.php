<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StoreCartTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_view_or_update_a_cart(): void
    {
        $product = $this->createProduct();

        $this->get(route('store-cart.index'))
            ->assertRedirect(route('login'));

        $this->post(route('store-cart.items.store', $product), ['quantity' => 1])
            ->assertRedirect(route('login'));

        $this->get(route('store.show', $product->slug))
            ->assertOk()
            ->assertSee('Inicia sesión para comprar')
            ->assertDontSee('Mi carrito');
    }

    public function test_authenticated_customer_can_add_products_and_view_their_cart(): void
    {
        $user = User::factory()->create(['role' => 'cliente']);
        $product = $this->createProduct(stock: 5);

        $this->actingAs($user)
            ->post(route('store-cart.items.store', $product), ['quantity' => 2])
            ->assertRedirect(route('store-cart.index'))
            ->assertSessionHas('success', 'El producto se agregó al carrito.');

        $this->assertDatabaseHas('carts', ['user_id' => $user->id]);
        $this->assertDatabaseHas('cart_items', [
            'product_id' => $product->id,
            'quantity' => 2,
        ]);

        $this->get(route('store-cart.index'))
            ->assertOk()
            ->assertSee('Mi carrito')
            ->assertSee($product->name)
            ->assertSee('$200.00')
            ->assertSee('value="2"', false);
    }

    public function test_adding_an_existing_product_increases_quantity_and_cannot_exceed_stock(): void
    {
        $user = User::factory()->create();
        $product = $this->createProduct(stock: 3);
        $cart = Cart::create(['user_id' => $user->id]);
        $cartItem = $cart->items()->create([
            'product_id' => $product->id,
            'quantity' => 1,
        ]);

        $this->actingAs($user)
            ->post(route('store-cart.items.store', $product), ['quantity' => 1])
            ->assertRedirect(route('store-cart.index'));

        $this->assertDatabaseHas('cart_items', [
            'id' => $cartItem->id,
            'quantity' => 2,
        ]);

        $this->post(route('store-cart.items.store', $product), ['quantity' => 2])
            ->assertSessionHasErrors('quantity');

        $this->assertDatabaseHas('cart_items', [
            'id' => $cartItem->id,
            'quantity' => 2,
        ]);
    }

    public function test_customer_can_update_and_remove_an_item_from_their_cart(): void
    {
        $user = User::factory()->create();
        $product = $this->createProduct(stock: 5);
        $cart = Cart::create(['user_id' => $user->id]);
        $cartItem = $cart->items()->create([
            'product_id' => $product->id,
            'quantity' => 1,
        ]);

        $this->actingAs($user)
            ->patch(route('store-cart.items.update', $cartItem), ['quantity' => 3])
            ->assertRedirect(route('store-cart.index'));

        $this->assertDatabaseHas('cart_items', [
            'id' => $cartItem->id,
            'quantity' => 3,
        ]);

        $this->patch(route('store-cart.items.update', $cartItem), ['quantity' => 6])
            ->assertSessionHasErrors('quantity');

        $this->assertDatabaseHas('cart_items', [
            'id' => $cartItem->id,
            'quantity' => 3,
        ]);

        $this->delete(route('store-cart.items.destroy', $cartItem))
            ->assertRedirect(route('store-cart.index'));

        $this->assertSoftDeleted('cart_items', ['id' => $cartItem->id]);
    }

    public function test_customer_cannot_change_another_users_cart_item(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $product = $this->createProduct(stock: 5);
        $cart = Cart::create(['user_id' => $owner->id]);
        $cartItem = $cart->items()->create([
            'product_id' => $product->id,
            'quantity' => 1,
        ]);

        $this->actingAs($otherUser)
            ->patch(route('store-cart.items.update', $cartItem), ['quantity' => 2])
            ->assertNotFound();

        $this->assertDatabaseHas('cart_items', [
            'id' => $cartItem->id,
            'quantity' => 1,
        ]);
    }

    public function test_cart_navigation_is_visible_only_to_authenticated_users(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertDontSee('Mi carrito');

        $this->actingAs(User::factory()->create())
            ->get('/')
            ->assertOk()
            ->assertSee('href="'.route('store-cart.index').'"', false)
            ->assertSee('Mi carrito');
    }

    private function createProduct(int $stock = 5): Product
    {
        $category = Category::create([
            'name' => 'Accesorios',
            'status' => true,
        ]);

        return Product::create([
            'category_id' => $category->id,
            'name' => 'Mouse inalámbrico',
            'slug' => 'mouse-inalambrico',
            'price' => 100,
            'stock' => $stock,
            'featured' => false,
            'status' => true,
        ]);
    }
}
