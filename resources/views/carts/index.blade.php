<x-layouts::app>
    <section class="mx-auto max-w-6xl">
        <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
            <div>
                <flux:badge color="blue">Administración</flux:badge>
                <h1 class="mt-3 text-2xl font-semibold">Carritos</h1>
                <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">Consulta y administra los carritos de los usuarios.</p>
            </div>
            <flux:button href="{{ route('carts.create') }}" variant="primary">Nuevo carrito</flux:button>
        </div>
        @if (session('success'))
            <flux:callout variant="success" class="mb-4">{{ session('success') }}</flux:callout>
        @endif
        <flux:table :paginate="$carts">
            <flux:table.columns>
                <flux:table.column>Usuario</flux:table.column>
                <flux:table.column>Correo electrónico</flux:table.column>
                <flux:table.column>Productos</flux:table.column>
                <flux:table.column>Creado</flux:table.column>
                <flux:table.column>Acciones</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @forelse ($carts as $cart)
                    <flux:table.row>
                        <flux:table.cell variant="strong">{{ $cart->user?->name ?? 'Usuario no disponible' }}</flux:table.cell>
                        <flux:table.cell>{{ $cart->user?->email ?? '—' }}</flux:table.cell>
                        <flux:table.cell><flux:badge color="blue">{{ $cart->items_count }}</flux:badge></flux:table.cell>
                        <flux:table.cell>{{ $cart->created_at?->format('d/m/Y') }}</flux:table.cell>
                        <flux:table.cell>
                            <div class="flex items-center gap-2">
                                <flux:button href="{{ route('carts.edit', $cart->id) }}" size="sm" variant="ghost">Editar</flux:button>
                                <form action="{{ route('carts.destroy', $cart->id) }}" method="POST" onsubmit="return confirm('¿Seguro que deseas eliminar este carrito?')">
                                    @csrf
                                    @method('DELETE')
                                    <flux:button type="submit" size="sm" variant="danger">Eliminar</flux:button>
                                </form>
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="5" class="text-center">Todavía no hay carritos registrados.</flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
    </section>
</x-layouts::app>
