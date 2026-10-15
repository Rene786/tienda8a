<x-layouts::app>
    <flux:card class="mx-auto max-w-2xl">
        <div class="mb-6">
            <flux:badge color="blue">Administración</flux:badge>
            <h1 class="mt-3 text-2xl font-semibold">Crear carrito</h1>
            <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">Asigna un carrito a un usuario que aún no tenga uno.</p>
        </div>

        <form action="{{ route('carts.store') }}" method="POST" class="space-y-5">
            @csrf
            <flux:select id="user_id" name="user_id" label="Usuario" placeholder="Selecciona un usuario" required>
                @foreach ($users as $user)
                    <option value="{{ $user->id }}" @selected(old('user_id') == $user->id)>{{ $user->name }} — {{ $user->email }}</option>
                @endforeach
            </flux:select>
            @if ($users->isEmpty())
                <flux:callout variant="warning">No hay usuarios sin carrito disponible para asignar.</flux:callout>
            @endif
            <div class="flex justify-end gap-2 border-t border-zinc-200 pt-5 dark:border-white/10">
                <flux:button href="{{ route('carts.index') }}" variant="ghost">Cancelar</flux:button>
                <flux:button type="submit" variant="primary" :disabled="$users->isEmpty()">Crear carrito</flux:button>
            </div>
        </form>
    </flux:card>
</x-layouts::app>
