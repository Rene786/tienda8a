<x-layouts::app title="Recuperar contraseña">
    <flux:card class="mx-auto w-full max-w-md p-7 sm:p-9">
        <div class="mb-7">
            <flux:badge color="blue">Recupera tu cuenta</flux:badge>
            <h1 class="mt-3 text-2xl font-semibold tracking-tight">Olvidé mi contraseña</h1>
            <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">
                Escribe tu correo y te enviaremos un enlace para restablecerla.
            </p>
        </div>

        @if (session('status'))
            <p role="status" class="mb-5 text-sm text-green-700 dark:text-green-400">
                Si el correo está registrado, recibirás un enlace para restablecer tu contraseña.
            </p>
        @endif

        <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
            @csrf

            <div>
                <flux:input id="email" name="email" type="email" label="Correo electrónico" value="{{ old('email') }}" required autofocus autocomplete="email" />
                @error('email')
                    <p role="alert" class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <flux:button type="submit" variant="primary" class="w-full">
                Enviar enlace de recuperación
            </flux:button>
        </form>

        <p class="mt-6 text-center text-sm text-zinc-600 dark:text-zinc-400">
            <flux:link href="{{ route('login') }}">Volver a iniciar sesión</flux:link>
        </p>
    </flux:card>
</x-layouts.app>
