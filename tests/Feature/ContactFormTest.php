<?php

namespace Tests\Feature;

use App\Mail\ContactFormMessage;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ContactFormTest extends TestCase
{
    public function test_contact_form_sends_message_to_the_configured_gmail_address(): void
    {
        Mail::fake();
        config(['mail.contact_to' => 'store@example.com']);

        $this->get(route('contact'))
            ->assertOk()
            ->assertSee(route('contact.send'))
            ->assertSee('name="name"', false);

        $this->post(route('contact.send'), [
            'name' => 'Ana Pérez',
            'email' => 'ana@example.com',
            'subject' => 'Consulta de productos',
            'message' => 'Quisiera recibir más información.',
        ])
            ->assertRedirect(route('contact'))
            ->assertSessionHas('success');

        Mail::assertSent(ContactFormMessage::class, function (ContactFormMessage $mail): bool {
            $envelope = $mail->envelope();

            return $mail->hasTo('store@example.com')
                && $envelope->replyTo[0]->address === 'ana@example.com'
                && $envelope->replyTo[0]->name === 'Ana Pérez'
                && $envelope->subject === 'Contacto TechStore: Consulta de productos'
                && str_contains($mail->render(), 'Quisiera recibir más información.');
        });
    }

    public function test_contact_form_validates_required_fields_and_does_not_send_invalid_messages(): void
    {
        Mail::fake();

        $this->from(route('contact'))
            ->post(route('contact.send'), [
                'name' => '',
                'email' => 'correo-no-valido',
                'subject' => '',
                'message' => '',
            ])
            ->assertRedirect(route('contact'))
            ->assertSessionHasErrors(['name', 'email', 'subject', 'message']);

        Mail::assertNothingSent();
    }

    public function test_contact_form_reports_missing_recipient_configuration_without_sending(): void
    {
        Mail::fake();
        config(['mail.contact_to' => null]);

        $this->from(route('contact'))
            ->post(route('contact.send'), [
                'name' => 'Ana Pérez',
                'email' => 'ana@example.com',
                'subject' => 'Consulta',
                'message' => 'Necesito ayuda.',
            ])
            ->assertRedirect(route('contact'))
            ->assertSessionHas('error');

        Mail::assertNothingSent();
    }
}
