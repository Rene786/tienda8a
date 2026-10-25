<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

new class extends Component
{
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    public function register()
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'same:password_confirmation'],
            'password_confirmation' => ['required'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        Auth::login($user);

        session()->regenerate();

        $this->redirect(url('/'));
    }
};
?>

<flux:card class="mx-auto w-full max-w-md p-7 sm:p-9">
        <div class="mb-7">
            <flux:badge color="blue">Únete a TechStore</flux:badge>
            <h1 class="mt-3 text-2xl font-semibold tracking-tight">Crear cuenta</h1>
            <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">Completa tus datos para comenzar.</p>
        </div>

        <form wire:submit="register" class="space-y-5">
            <div>
                <flux:input id="name" type="text" wire:model="name" label="Nombre" required autofocus autocomplete="name" />
            </div>

            <div>
                <flux:input id="email" type="email" wire:model="email" label="Correo electrónico" required autocomplete="email" />
            </div>

            <div>
                <flux:input id="password" type="password" wire:model="password" label="Contraseña" required autocomplete="new-password" />
            </div>

            <div>
                <flux:input id="password_confirmation" type="password" wire:model="password_confirmation" label="Confirmar contraseña" required autocomplete="new-password" />
            </div>

            <flux:button type="submit" variant="primary" class="w-full">
                Crear cuenta
            </flux:button>
        </form>

        <p class="mt-6 text-center text-sm text-zinc-600 dark:text-zinc-400">
            ¿Ya tienes una cuenta? <flux:link href="{{ route('login') }}">Iniciar sesión</flux:link>
        </p>
    </flux:card>
