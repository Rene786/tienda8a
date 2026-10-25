<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\PdfDownload;
use App\Models\Product;
use App\Models\ProductFile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PdfDownloadTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_provides_a_link_to_the_pdf_download_history(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']))
            ->get(route('home'))
            ->assertOk()
            ->assertSee('Historial de descargas PDF')
            ->assertSee(route('pdf-downloads.index'));
    }

    public function test_authenticated_user_can_view_the_pdf_download_history(): void
    {
        $user = User::factory()->create(['name' => 'Usuario de prueba', 'role' => 'admin']);
        PdfDownload::create([
            'user_id' => $user->id,
            'file_name' => 'reporte-products-techstore.pdf',
            'file_path' => 'reports/dashboard/reporte-products-techstore.pdf',
            'downloaded_at' => now(),
            'ip_address' => '127.0.0.1',
        ]);

        $this->actingAs($user)
            ->get(route('pdf-downloads.index'))
            ->assertOk()
            ->assertSee('Usuario de prueba')
            ->assertSee('reporte-products-techstore.pdf')
            ->assertSee('reports/dashboard/reporte-products-techstore.pdf')
            ->assertSee('127.0.0.1');
    }

    public function test_guest_cannot_view_the_pdf_download_history(): void
    {
        $this->get(route('pdf-downloads.index'))
            ->assertRedirect(route('login'));
    }

    public function test_dashboard_pdf_download_is_saved_to_the_history(): void
    {
        $user = User::factory()->create(['role' => 'admin']);

        $this->actingAs($user)
            ->get(route('reports.dashboard', 'products'))
            ->assertDownload('reporte-products-techstore.pdf');

        $this->assertDatabaseHas('pdf_downloads', [
            'user_id' => $user->id,
            'product_file_id' => null,
            'file_name' => 'reporte-products-techstore.pdf',
            'file_path' => 'reports/dashboard/reporte-products-techstore.pdf',
        ]);
    }

    public function test_product_pdf_download_is_saved_to_the_history(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('manuales/guia.pdf', 'Contenido PDF');

        $user = User::factory()->create(['role' => 'admin']);
        $category = Category::create([
            'name' => 'Accesorios',
            'status' => true,
        ]);
        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Audífonos',
            'slug' => 'audifonos',
            'price' => 499.99,
            'stock' => 4,
            'featured' => false,
            'status' => true,
        ]);
        $productFile = ProductFile::create([
            'product_id' => $product->id,
            'name' => 'Guía de usuario',
            'file_path' => 'manuales/guia.pdf',
            'file_type' => 'PDF',
        ]);

        $this->actingAs($user);

        $this->get(route('product-files.index'))
            ->assertOk()
            ->assertSee(route('product-files.download', $productFile));

        $this->get(route('product-files.download', $productFile))
            ->assertDownload('Guia de usuario.pdf');

        $this->assertDatabaseHas('pdf_downloads', [
            'user_id' => $user->id,
            'product_file_id' => $productFile->id,
            'file_name' => 'Guía de usuario',
            'file_path' => 'manuales/guia.pdf',
        ]);
    }
}
