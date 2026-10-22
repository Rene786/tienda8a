<x-layouts::app>
    <section class="mx-auto max-w-6xl">
        <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
            <div>
                <flux:badge color="blue">Administración</flux:badge>
                <h1 class="mt-3 text-2xl font-semibold">Artículos del carrito</h1>
                <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">Consulta y administra los productos agregados a cada carrito.</p>
            </div>
            <flux:button href="{{ route('cart-items.create') }}" variant="primary">Agregar artículo</flux:button>
        </div>
        @if (session('success'))
            <flux:callout variant="success" class="mb-4">{{ session('success') }}</flux:callout>
        @endif
        <x-admin.status-filter route-name="cart-items.index" :options="$statusOptions" />
        <flux:table>
            <flux:table.columns>
                <flux:table.column>Carrito / usuario</flux:table.column>
                <flux:table.column>Producto</flux:table.column>
                <flux:table.column>Cantidad</flux:table.column>
                <flux:table.column>Agregado</flux:table.column>
                <flux:table.column>Acciones</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @forelse ($cartItems as $cartItem)
                    <flux:table.row>
                        <flux:table.cell variant="strong">
                            Carrito #{{ $cartItem->cart_id }}
                            <span class="block text-xs font-normal text-zinc-500">{{ $cartItem->cart?->user?->name ?? 'Usuario no disponible' }}</span>
                            @if ($cartItem->cart?->trashed())<span class="block text-xs text-amber-700">Carrito eliminado</span>@endif
                            @if ($cartItem->trashed())<span class="block text-xs text-red-700">Artículo eliminado</span>@endif
                        </flux:table.cell>
                        <flux:table.cell>
                            {{ $cartItem->product?->name ?? 'Producto no disponible' }}
                            @if ($cartItem->product?->trashed())<span class="block text-xs text-amber-700">Producto eliminado</span>@endif
                        </flux:table.cell>
                        <flux:table.cell><flux:badge color="blue">{{ $cartItem->quantity }}</flux:badge></flux:table.cell>
                        <flux:table.cell>{{ $cartItem->created_at?->format('d/m/Y') }}</flux:table.cell>
                        <flux:table.cell>
                            @unless ($cartItem->trashed())
                                <div class="flex items-center gap-2">
                                    <flux:button href="{{ route('cart-items.edit', $cartItem) }}" size="sm" variant="ghost">Editar</flux:button>
                                    <form action="{{ route('cart-items.destroy', $cartItem) }}" method="POST" onsubmit="return confirm('¿Seguro que deseas eliminar este artículo?')">
                                        @csrf
                                        @method('DELETE')
                                        <flux:button type="submit" size="sm" variant="danger">Eliminar</flux:button>
                                    </form>
                                </div>
                            @endunless
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="5" class="text-center">Todavía no hay artículos en los carritos.</flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
        @if ($cartItems->hasPages())
            <div class="mt-6">{{ $cartItems->links() }}</div>
        @endif
    </section>
</x-layouts::app>
