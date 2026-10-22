<x-layouts::app title="Restablecer contraseña">
    <flux:card class="mx-auto w-full max-w-md p-7 sm:p-9">
        <div class="mb-7">
            <flux:badge color="blue">Seguridad de tu cuenta</flux:badge>
            <h1 class="mt-3 text-2xl font-semibold tracking-tight">Crear nueva contraseña</h1>
            <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">
                Elige una contraseña nueva para volver a acceder a tu cuenta.
            </p>
        </div>

        <form method="POST" action="{{ route('password.update') }}" class="space-y-5">
            @csrf
            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            <div>
                <flux:input id="email" name="email" type="email" label="Correo electrónico" value="{{ old('email', $request->email) }}" required autocomplete="email" />
                @error('email')
                    <p role="alert" class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <flux:input id="password" name="password" type="password" label="Nueva contraseña" required autocomplete="new-password" />
                @error('password')
                    <p role="alert" class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <flux:input id="password_confirmation" name="password_confirmation" type="password" label="Confirmar contraseña" required autocomplete="new-password" />
            </div>

            <flux:button type="submit" variant="primary" class="w-full">
                Restablecer contraseña
            </flux:button>
        </form>
    </flux:card>
</x-layouts.app>
