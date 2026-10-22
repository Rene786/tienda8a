<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminProductFiltersTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_filter_products_by_search_and_category_and_sort_results(): void
    {
        $admin = User::create([
            'name' => 'Administrador',
            'email' => 'admin-products@example.test',
            'password' => 'password',
            'role' => 'admin',
        ]);

        $category = Category::create([
            'name' => 'Accesorios',
            'status' => true,
        ]);

        $otherCategory = Category::create([
            'name' => 'Computación',
            'status' => true,
        ]);

        foreach ([
            [$category, 'Audífonos inalámbricos'],
            [$category, 'Mouse inalámbrico'],
            [$otherCategory, 'Teclado inalámbrico'],
            [$category, 'Cargador USB'],
        ] as [$productCategory, $productName]) {
            Product::create([
                'category_id' => $productCategory->id,
                'name' => $productName,
                'slug' => str()->slug($productName),
                'price' => 100,
                'stock' => 5,
                'featured' => false,
                'status' => true,
            ]);
        }

        $this->actingAs($admin)
            ->get(route('products.index', [
                'search' => 'inalámbrico',
                'category' => $category->id,
                'sort' => 'name_desc',
            ]))
            ->assertOk()
            ->assertSeeInOrder(['Mouse inalámbrico', 'Audífonos inalámbricos'])
            ->assertDontSee('Teclado inalámbrico')
            ->assertDontSee('Cargador USB')
            ->assertSee('value="'.$category->id.'" selected', false)
            ->assertSee('value="name_desc" selected', false)
            ->assertSee('name="search"', false)
            ->assertSee('Filtrar');
    }

    public function test_product_inventory_is_rendered_as_a_compact_list(): void
    {
        $admin = User::create([
            'name' => 'Administrador',
            'email' => 'admin-list@example.test',
            'password' => 'password',
            'role' => 'admin',
        ]);

        $category = Category::create([
            'name' => 'Accesorios',
            'status' => true,
        ]);

        Product::create([
            'category_id' => $category->id,
            'name' => 'Mouse compacto',
            'slug' => 'mouse-compacto',
            'price' => 100,
            'stock' => 5,
            'featured' => false,
            'status' => true,
        ]);

        $this->actingAs($admin)
            ->get(route('products.index'))
            ->assertOk()
            ->assertSee('space-y-3', false)
            ->assertSee('size-20', false)
            ->assertSee('Mouse compacto');
    }
}
