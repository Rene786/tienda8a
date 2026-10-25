<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class ProfileControllerTest extends TestCase
{
    use RefreshDatabase;

    public static function accountRoles(): array
    {
        return [
            'administrator' => ['admin'],
            'customer' => ['cliente'],
        ];
    }

    public function test_guest_is_redirected_to_login_from_profile(): void
    {
        $this->get(route('profile.edit'))
            ->assertRedirect(route('login'));
    }

    #[DataProvider('accountRoles')]
    public function test_profile_shows_only_the_authenticated_users_orders_for_each_role(string $role): void
    {
        $user = User::factory()->create(['role' => $role]);
        $otherUser = User::factory()->create();
        $ownOrder = $user->orders()->create([
            'order_number' => 'TS-OWN-'.$user->id,
            'total' => 1299.99,
            'status' => 'processing',
            'payment_status' => 'paid',
        ]);
        $otherOrder = $otherUser->orders()->create([
            'order_number' => 'TS-OTHER-'.$otherUser->id,
            'total' => 799.99,
            'status' => 'pending',
            'payment_status' => 'pending',
        ]);

        $this->actingAs($user)
            ->get(route('profile.edit'))
            ->assertOk()
            ->assertSee('Mi perfil')
            ->assertSee($ownOrder->order_number)
            ->assertDontSee($otherOrder->order_number);
    }

    #[DataProvider('accountRoles')]
    public function test_profile_updates_account_details_without_changing_role(string $role): void
    {
        $user = User::factory()->create(['role' => $role]);

        $this->actingAs($user)
            ->patch(route('profile.update'), [
                'name' => 'Nombre actualizado',
                'email' => 'actualizado@example.com',
                'role' => $role === 'cliente' ? 'admin' : 'cliente',
            ])
            ->assertRedirect(route('profile.edit'))
            ->assertSessionHas('success', 'Tu perfil se actualizó correctamente.');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Nombre actualizado',
            'email' => 'actualizado@example.com',
            'role' => $role,
        ]);
    }

    public function test_profile_rejects_an_email_already_used_by_another_account(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $this->actingAs($user)
            ->patch(route('profile.update'), [
                'name' => 'Nombre sin guardar',
                'email' => $otherUser->email,
            ])
            ->assertSessionHasErrorsIn('updateProfileInformation', 'email');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
        ]);
    }
}
