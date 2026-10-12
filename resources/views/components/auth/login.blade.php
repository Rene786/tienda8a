<?php

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

new class extends Component
{
    public string $email = '';
    public string $password = '';
    public bool $remember = false;

    public function login()
    {
        $credentials = $this->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (! Auth::attempt($credentials, $this->remember)) {
            $this->addError('email', 'Las credenciales no son correctas.');

            return;
        }

        session()->regenerate();

        $this->redirectIntended('/home');
    }
};
?>

<flux:card class="mx-auto w-full max-w-md p-7 sm:p-9">
        <div class="mb-7">
            <flux:badge color="blue">Tu espacio TechStore</flux:badge>
            <h1 class="mt-3 text-2xl font-semibold tracking-tight">Iniciar sesión</h1>
            <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">Accede para continuar.</p>
        </div>

        <form wire:submit="login" class="space-y-5">
            <div>
                <flux:input id="email" type="email" wire:model="email" label="Correo electrónico" required autofocus autocomplete="email" />
            </div>

            <div>
                <flux:input id="password" type="password" wire:model="password" label="Contraseña" required autocomplete="current-password" />
            </div>

            <flux:checkbox wire:model="remember" label="Recordarme" />

            <flux:button type="submit" variant="primary" class="w-full">
                Iniciar sesión
            </flux:button>
        </form>

        <p class="mt-6 text-center text-sm text-zinc-600 dark:text-zinc-400">
            ¿No tienes una cuenta? <flux:link href="{{ route('register') }}">Registrarse</flux:link>
        </p>
    </flux:card>
