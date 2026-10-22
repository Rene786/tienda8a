<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductFile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminStatusFiltersTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_filter_active_inactive_and_deleted_products_and_categories(): void
    {
        $admin = $this->createUser('admin-status@example.test', 'admin');
        $activeCategory = Category::create(['name' => 'Categoría activa', 'status' => true]);
        $inactiveCategory = Category::create(['name' => 'Categoría inactiva', 'status' => false]);
        $deletedCategory = Category::create(['name' => 'Categoría eliminada', 'status' => true]);
        $this->createProduct($activeCategory, 'Producto activo', true);
        $this->createProduct($activeCategory, 'Producto inactivo', false);
        $deletedProduct = $this->createProduct($activeCategory, 'Producto eliminado', true);
        $deletedCategory->delete();
        $deletedProduct->delete();

        $this->actingAs($admin)
            ->get(route('products.index'))
            ->assertOk()
            ->assertSee('Producto activo')
            ->assertSee('Producto inactivo')
            ->assertDontSee('Producto eliminado');

        $this->get(route('products.index', ['status' => 'active']))
            ->assertOk()
            ->assertSee('Producto activo')
            ->assertDontSee('Producto inactivo')
            ->assertDontSee('Producto eliminado');

        $this->get(route('products.index', ['status' => 'inactive']))
            ->assertOk()
            ->assertSee('Producto inactivo')
            ->assertDontSee('Producto activo')
            ->assertDontSee('Producto eliminado');

        $this->get(route('products.index', ['status' => 'deleted']))
            ->assertOk()
            ->assertSee('Producto eliminado')
            ->assertSee('Eliminado')
            ->assertDontSee('Producto activo');

        $this->get(route('categories.index', ['status' => 'inactive']))
            ->assertOk()
            ->assertSee('Categoría inactiva')
            ->assertDontSee('Categoría activa');

        $this->get(route('categories.index', ['status' => 'deleted']))
            ->assertOk()
            ->assertSee('Categoría eliminada')
            ->assertSee('Eliminada')
            ->assertDontSee('Categoría activa');
    }

    public function test_admin_can_filter_deleted_records_in_all_other_soft_deletable_lists(): void
    {
        $admin = $this->createUser('admin-deleted@example.test', 'admin');
        $client = $this->createUser('client-deleted@example.test', 'cliente');
        $deletedUser = $this->createUser('deleted-user@example.test', 'cliente');
        $category = Category::create(['name' => 'Accesorios', 'status' => true]);
        $product = $this->createProduct($category, 'Producto relacionado', true);
        $order = Order::create([
            'user_id' => $client->id,
            'order_number' => 'STATUS-FILTER-001',
            'total' => 100,
            'status' => 'pending',
            'payment_status' => 'pending',
        ]);
        Order::create([
            'user_id' => $client->id,
            'order_number' => 'STATUS-FILTER-ACTIVE',
            'total' => 100,
            'status' => 'pending',
            'payment_status' => 'pending',
        ]);
        $cart = Cart::create(['user_id' => $client->id]);
        $cartItem = CartItem::create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 1,
        ]);
        $orderItem = OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'unit_price' => $product->price,
            'quantity' => 1,
            'subtotal' => $product->price,
        ]);
        $productFile = ProductFile::create([
            'product_id' => $product->id,
            'name' => 'Manual eliminado',
            'file_path' => 'manual.pdf',
            'file_type' => 'pdf',
        ]);

        $order->delete();
        $cart->delete();
        $cartItem->delete();
        $orderItem->delete();
        $productFile->delete();
        $deletedUser->delete();

        $this->actingAs($admin)
            ->get(route('users.index', ['status' => 'deleted']))
            ->assertOk()
            ->assertSee('deleted-user@example.test')
            ->assertSee('Eliminado');

        $this->get(route('orders.index', ['status' => 'deleted']))
            ->assertOk()
            ->assertSee('STATUS-FILTER-001')
            ->assertSee('Eliminado');

        $this->get(route('orders.index', ['status' => 'pending']))
            ->assertOk()
            ->assertSee('STATUS-FILTER-ACTIVE')
            ->assertDontSee('STATUS-FILTER-001');

        $this->get(route('carts.index', ['status' => 'deleted']))
            ->assertOk()
            ->assertSee('client-deleted@example.test')
            ->assertSee('Eliminado');

        $this->get(route('cart-items.index', ['status' => 'deleted']))
            ->assertOk()
            ->assertSee('Artículo eliminado')
            ->assertSee('Producto relacionado');

        $this->get(route('order-items.index', ['status' => 'deleted']))
            ->assertOk()
            ->assertSee('Artículo eliminado')
            ->assertSee('Producto relacionado');

        $this->get(route('product-files.index', ['status' => 'deleted']))
            ->assertOk()
            ->assertSee('Manual eliminado')
            ->assertSee('Eliminado');
    }

    private function createUser(string $email, string $role): User
    {
        $user = User::create([
            'name' => $role === 'admin' ? 'Administrador' : 'Cliente',
            'email' => $email,
            'password' => 'password',
        ]);
        $user->role = $role;
        $user->save();

        return $user;
    }

    private function createProduct(Category $category, string $name, bool $status): Product
    {
        return Product::create([
            'category_id' => $category->id,
            'name' => $name,
            'slug' => str()->slug($name),
            'price' => 100,
            'stock' => 5,
            'featured' => false,
            'status' => $status,
        ]);
    }
}
