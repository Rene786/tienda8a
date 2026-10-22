<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class ForgotPasswordTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_links_to_the_password_recovery_form(): void
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertSee('¿Olvidaste tu contraseña?')
            ->assertSee(route('password.request'));
    }

    public function test_password_recovery_sends_a_reset_link_to_the_users_email(): void
    {
        Notification::fake();
        $user = User::factory()->create();

        $this->get(route('password.request'))
            ->assertOk()
            ->assertSee('name="email"', false);

        $this->post(route('password.email'), ['email' => $user->email])
            ->assertRedirect()
            ->assertSessionHas('status');

        Notification::assertSentTo($user, ResetPassword::class, function (
            ResetPassword $notification,
            array $channels,
            User $notifiable
        ): bool {
            $mailMessage = $notification->toMail($notifiable);

            $this->assertSame(['mail'], $channels);
            $this->assertSame([config('mail.from.address'), 'TechStore'], $mailMessage->from);
            $this->assertSame('Restablece tu contraseña - TechStore', $mailMessage->subject);
            $this->assertSame('Hola '.$notifiable->name.',', $mailMessage->greeting);
            $this->assertSame('Restablecer contraseña', $mailMessage->actionText);
            $this->assertSame(
                route('password.reset', [
                    'token' => $notification->token,
                    'email' => $notifiable->email,
                ]),
                $mailMessage->actionUrl
            );
            $this->assertContains(
                'Si no solicitaste este cambio, puedes ignorar este correo. Tu contraseña seguirá siendo la misma.',
                $mailMessage->outroLines
            );
            $this->assertSame('Saludos, equipo TechStore', $mailMessage->salutation);

            return true;
        });
    }

    public function test_password_reset_form_updates_the_password_using_a_valid_token(): void
    {
        $user = User::factory()->create();
        $token = Password::createToken($user);

        $this->get(route('password.reset', ['token' => $token, 'email' => $user->email]))
            ->assertOk()
            ->assertSee('name="token"', false)
            ->assertSee('name="email"', false);

        $this->post(route('password.update'), [
            'token' => $token,
            'email' => $user->email,
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ])->assertRedirect(route('login'));

        $this->get(route('login'))
            ->assertOk()
            ->assertSee('Tu contraseña se actualizó.');

        $this->assertTrue(auth()->getProvider()->validateCredentials($user->fresh(), [
            'password' => 'new-password',
        ]));
    }

    public function test_password_recovery_does_not_send_a_link_for_an_unknown_email(): void
    {
        Notification::fake();

        $this->from(route('password.request'))
            ->post(route('password.email'), ['email' => 'unknown@example.test'])
            ->assertRedirect(route('password.request'))
            ->assertSessionHasErrors('email');

        Notification::assertNothingSent();
    }
}
