<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class StoreCatalogTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_carousel_only_displays_featured_active_products_with_images(): void
    {
        $category = Category::create([
            'name' => 'Audio',
            'status' => true,
        ]);

        Product::create([
            'category_id' => $category->id,
            'name' => 'Audífonos destacados',
            'slug' => 'audifonos-destacados',
            'price' => 799.99,
            'stock' => 5,
            'image' => 'audifonos.jpg',
            'featured' => true,
            'status' => true,
        ]);

        Product::create([
            'category_id' => $category->id,
            'name' => 'Destacado sin imagen',
            'slug' => 'destacado-sin-imagen',
            'price' => 499.99,
            'stock' => 2,
            'featured' => true,
            'status' => true,
        ]);

        Product::create([
            'category_id' => $category->id,
            'name' => 'Producto normal con imagen',
            'slug' => 'producto-normal-con-imagen',
            'price' => 299.99,
            'stock' => 8,
            'image' => 'producto.jpg',
            'featured' => false,
            'status' => true,
        ]);

        Product::create([
            'category_id' => $category->id,
            'name' => 'Destacado inactivo',
            'slug' => 'destacado-inactivo',
            'price' => 199.99,
            'stock' => 1,
            'image' => 'inactivo.jpg',
            'featured' => true,
            'status' => false,
        ]);

        $this->get('/')
            ->assertOk()
            ->assertSee('Productos destacados')
            ->assertSee('[scrollbar-width:none]', false)
            ->assertSee('setInterval(() => this.scrollFeatured(1), 3000)', false)
            ->assertSee('aria-label="Productos anteriores"', false)
            ->assertSee('aria-label="Productos siguientes"', false)
            ->assertSee('Audífonos destacados')
            ->assertSee('audifonos.jpg')
            ->assertSee(route('store.show', 'audifonos-destacados'))
            ->assertDontSee('Destacado sin imagen')
            ->assertDontSee('Producto normal con imagen')
            ->assertDontSee('Destacado inactivo');
    }

    public function test_homepage_links_to_the_about_page_journey_and_about_page_has_direct_links(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee(route('about').'#recorrido', false)
            ->assertSee('Ver recorrido');

        $this->get(route('about'))
            ->assertOk()
            ->assertSee('id="recorrido"', false)
            ->assertSee(route('store'), false)
            ->assertSee(route('store', ['sort' => 'featured']), false)
            ->assertSee(route('store', ['sort' => 'name_asc']), false)
            ->assertSee(route('store', ['sort' => 'name_desc']), false)
            ->assertSee(url('/').'#featured-products-title', false)
            ->assertSee('Área de gestión')
            ->assertSee('Historial de descargas PDF')
            ->assertDontSee('href="'.route('home').'"', false)
            ->assertDontSee('href="'.route('products.index').'"', false)
            ->assertDontSee('href="'.route('pdf-downloads.index').'"', false);
    }

    public function test_store_displays_active_products_without_cart_actions(): void
    {
        $category = Category::create([
            'name' => 'Accesorios',
            'status' => true,
        ]);

        Product::create([
            'category_id' => $category->id,
            'name' => 'Mouse inalámbrico',
            'slug' => 'mouse-inalambrico',
            'description' => 'Mouse para uso diario.',
            'price' => 399.99,
            'stock' => 5,
            'featured' => false,
            'status' => true,
        ]);

        Product::create([
            'category_id' => $category->id,
            'name' => 'Producto oculto',
            'slug' => 'producto-oculto',
            'price' => 99.99,
            'stock' => 5,
            'featured' => false,
            'status' => false,
        ]);

        $response = $this->get(route('store'));

        $response->assertOk()
            ->assertSee('Mouse inalámbrico')
            ->assertDontSee('Producto oculto')
            ->assertDontSee('Agregar al carrito');
    }

    public function test_store_filters_products_by_search_and_category(): void
    {
        $category = Category::create([
            'name' => 'Audio',
            'status' => true,
        ]);

        $otherCategory = Category::create([
            'name' => 'Accesorios',
            'status' => true,
        ]);

        Product::create([
            'category_id' => $category->id,
            'name' => 'Audífonos inalámbricos',
            'slug' => 'audifonos-inalambricos',
            'description' => 'Audio para llevar.',
            'price' => 899.99,
            'stock' => 3,
            'featured' => true,
            'status' => true,
        ]);

        Product::create([
            'category_id' => $otherCategory->id,
            'name' => 'Funda para audífonos',
            'slug' => 'funda-para-audifonos',
            'price' => 149.99,
            'stock' => 8,
            'featured' => false,
            'status' => true,
        ]);

        $response = $this->get(route('store', [
            'search' => 'inalámbricos',
            'category' => $category->id,
        ]));

        $response->assertOk()
            ->assertSee('Audífonos inalámbricos')
            ->assertDontSee('Funda para audífonos');
    }

    public function test_store_sorts_products_by_name_and_featured_status(): void
    {
        $category = Category::create([
            'name' => 'Computación',
            'status' => true,
        ]);

        foreach ([
            ['name' => 'Mouse', 'featured' => false],
            ['name' => 'Teclado', 'featured' => true],
            ['name' => 'Audífonos', 'featured' => false],
        ] as $productData) {
            Product::create([
                'category_id' => $category->id,
                'name' => $productData['name'],
                'slug' => Str::slug($productData['name']),
                'price' => 100,
                'stock' => 1,
                'featured' => $productData['featured'],
                'status' => true,
            ]);
        }

        $this->get(route('store', ['sort' => 'name_asc']))
            ->assertOk()
            ->assertSeeInOrder(['Audífonos', 'Mouse', 'Teclado'])
            ->assertSee('value="name_asc" selected', false);

        $this->get(route('store', ['sort' => 'name_desc']))
            ->assertOk()
            ->assertSeeInOrder(['Teclado', 'Mouse', 'Audífonos'])
            ->assertSee('value="name_desc" selected', false);

        $this->get(route('store', ['sort' => 'featured']))
            ->assertOk()
            ->assertSeeInOrder(['Teclado', 'Audífonos', 'Mouse'])
            ->assertSee('value="featured" selected', false);
    }

    public function test_customer_can_open_the_product_details_from_the_catalog(): void
    {
        $category = Category::create([
            'name' => 'Computación',
            'status' => true,
        ]);

        Product::create([
            'category_id' => $category->id,
            'name' => 'Teclado mecánico',
            'slug' => 'teclado-mecanico',
            'description' => 'Teclado con teclas retroiluminadas.',
            'price' => 1299.99,
            'stock' => 4,
            'image' => 'teclado.jpg',
            'image_2' => 'teclado-lateral.jpg',
            'featured' => true,
            'status' => true,
        ]);

        $this->get(route('store'))
            ->assertOk()
            ->assertSee(route('store.show', 'teclado-mecanico'));

        $this->get(route('store.show', 'teclado-mecanico'))
            ->assertOk()
            ->assertSee('Teclado mecánico')
            ->assertSee('Teclado con teclas retroiluminadas.')
            ->assertSee('Computación')
            ->assertSee('$1,299.99')
            ->assertSee('4 en existencia')
            ->assertSee('teclado-lateral.jpg')
            ->assertSee('Agregar al carrito (próximamente)')
            ->assertSee('disabled', false);
    }

    public function test_customer_cannot_open_inactive_product_details(): void
    {
        $category = Category::create([
            'name' => 'Accesorios',
            'status' => true,
        ]);

        Product::create([
            'category_id' => $category->id,
            'name' => 'Producto oculto',
            'slug' => 'producto-oculto',
            'price' => 99.99,
            'stock' => 1,
            'featured' => false,
            'status' => false,
        ]);

        $this->get(route('store.show', 'producto-oculto'))
            ->assertNotFound();
    }
}
