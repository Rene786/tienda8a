<x-layouts::app>
    <flux:card class="mx-auto max-w-2xl">
        <div class="mb-6">
            <flux:badge color="blue">Administración</flux:badge>
            <h1 class="mt-3 text-2xl font-semibold">Editar usuario</h1>
            <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">Actualiza los datos de {{ $user->name }}.</p>
        </div>
        <form action="{{ route('users.update', $user->id) }}" method="POST" class="space-y-5">
            @csrf
            @method('PUT')
            <flux:input id="name" name="name" label="Nombre completo" value="{{ old('name', $user->name) }}" required maxlength="255" autocomplete="name" />
            <flux:input id="email" type="email" name="email" label="Correo electrónico" value="{{ old('email', $user->email) }}" required maxlength="255" autocomplete="email" />
            <flux:select id="role" name="role" label="Rol" required>
                <option value="cliente" @selected(old('role', $user->role) === 'cliente')>Cliente</option>
                <option value="admin" @selected(old('role', $user->role) === 'admin')>Administrador</option>
            </flux:select>
            <div class="grid gap-5 sm:grid-cols-2">
                <flux:input id="password" type="password" name="password" label="Nueva contraseña" minlength="8" autocomplete="new-password" description="Déjala vacía para conservar la contraseña actual." />
                <flux:input id="password_confirmation" type="password" name="password_confirmation" label="Confirmar nueva contraseña" minlength="8" autocomplete="new-password" />
            </div>
            <div class="flex justify-end gap-2 border-t border-zinc-200 pt-5 dark:border-white/10">
                <flux:button href="{{ route('users.index') }}" variant="ghost">Cancelar</flux:button>
                <flux:button type="submit" variant="primary">Guardar cambios</flux:button>
            </div>
        </form>
    </flux:card>
</x-layouts::app>
