<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AdminAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_client_cannot_access_admin_dashboard_or_management_routes(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'cliente']));

        $this->get(route('home'))->assertForbidden();
        $this->get(route('products.index'))->assertForbidden();
        $this->get(route('orders.index'))->assertForbidden();
        $this->get(route('users.index'))->assertForbidden();
        $this->get(route('pdf-downloads.index'))->assertForbidden();
        $this->get(route('reports.dashboard', 'products'))->assertForbidden();
    }

    public function test_client_sees_profile_instead_of_admin_navigation(): void
    {
        $client = User::factory()->create(['role' => 'cliente']);

        $this->actingAs($client)
            ->get(route('profile.edit'))
            ->assertOk()
            ->assertSee('Mi perfil')
            ->assertSee('href="'.route('profile.edit').'"', false)
            ->assertDontSee('Iniciar sesión')
            ->assertDontSee('href="'.route('home').'"', false)
            ->assertDontSee('href="'.route('products.index').'"', false)
            ->assertDontSee('href="'.route('orders.index').'"', false);

        $this->get('/')
            ->assertOk()
            ->assertSee('href="'.route('profile.edit').'"', false)
            ->assertDontSee('Administrar productos');
    }

    public function test_admin_can_access_the_dashboard_and_admin_navigation(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->get(route('home'))
            ->assertOk()
            ->assertSee('Panel de administración')
            ->assertSee('href="'.route('products.index').'"', false)
            ->assertSee('href="'.route('profile.edit').'"', false);
    }

    public function test_client_login_redirects_to_store_home_even_with_an_admin_intended_url(): void
    {
        User::factory()->create([
            'email' => 'client@example.test',
            'password' => 'password',
            'role' => 'cliente',
        ]);
        session()->put('url.intended', route('home'));

        Livewire::test('auth.login')
            ->set('email', 'client@example.test')
            ->set('password', 'password')
            ->call('login')
            ->assertRedirect(url('/'));
    }

    public function test_admin_login_redirects_to_dashboard(): void
    {
        User::factory()->create([
            'email' => 'admin@example.test',
            'password' => 'password',
            'role' => 'admin',
        ]);

        Livewire::test('auth.login')
            ->set('email', 'admin@example.test')
            ->set('password', 'password')
            ->call('login')
            ->assertRedirect(route('home'));
    }

    public function test_new_client_registration_redirects_to_store_home(): void
    {
        Livewire::test('auth.register')
            ->set('name', 'Nuevo cliente')
            ->set('email', 'new-client@example.test')
            ->set('password', 'password')
            ->set('password_confirmation', 'password')
            ->call('register')
            ->assertRedirect(url('/'));

        $this->assertDatabaseHas('users', [
            'email' => 'new-client@example.test',
            'role' => 'cliente',
        ]);
    }
}
