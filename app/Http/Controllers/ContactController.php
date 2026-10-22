<?php

namespace App\Http\Controllers;

use App\Mail\ContactFormMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class ContactController extends Controller
{
    public function send(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255'],
            'subject' => ['required', 'string', 'max:150'],
            'message' => ['required', 'string', 'max:5000'],
        ]);

        $recipient = config('mail.contact_to');

        if (! is_string($recipient) || filter_var($recipient, FILTER_VALIDATE_EMAIL) === false) {
            return back()
                ->withInput()
                ->with('error', 'El correo para recibir mensajes no está configurado correctamente.');
        }

        try {
            Mail::to($recipient)->send(new ContactFormMessage(
                senderName: $validated['name'],
                senderEmail: $validated['email'],
                contactSubject: $validated['subject'],
                contactMessage: $validated['message'],
            ));
        } catch (TransportExceptionInterface $exception) {
            report($exception);

            return back()
                ->withInput()
                ->with('error', 'No se pudo enviar tu mensaje. Inténtalo de nuevo más tarde.');
        }

        return redirect()
            ->route('contact')
            ->with('success', 'Tu mensaje se envió correctamente. Gracias por contactarnos.');
    }
}
