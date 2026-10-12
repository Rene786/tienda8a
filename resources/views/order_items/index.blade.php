<x-layouts::app>
    <section class="mx-auto max-w-6xl">
        <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
            <div>
                <flux:badge color="blue">Administración</flux:badge>
                <h1 class="mt-3 text-2xl font-semibold">Artículos de pedido</h1>
                <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">Administra los productos y cantidades incluidos en cada pedido.</p>
            </div>
            <flux:button href="{{ route('order-items.create') }}" variant="primary">Agregar artículo</flux:button>
        </div>
        @if (session('success'))
            <flux:callout variant="success" class="mb-4">{{ session('success') }}</flux:callout>
        @endif
        <flux:table :paginate="$orderItems">
            <flux:table.columns>
                <flux:table.column>Pedido</flux:table.column>
                <flux:table.column>Producto</flux:table.column>
                <flux:table.column>Precio unitario</flux:table.column>
                <flux:table.column>Cantidad</flux:table.column>
                <flux:table.column>Subtotal</flux:table.column>
                <flux:table.column>Acciones</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @forelse ($orderItems as $orderItem)
                    <flux:table.row>
                        <flux:table.cell variant="strong">
                            {{ $orderItem->order?->order_number ?? 'Pedido no disponible' }}
                            @if ($orderItem->order?->trashed())<span class="block text-xs text-amber-700">Pedido eliminado</span>@endif
                        </flux:table.cell>
                        <flux:table.cell>
                            {{ $orderItem->product_name }}
                            @if (! $orderItem->product)<span class="block text-xs text-zinc-400">Sin producto activo asociado</span>@elseif ($orderItem->product->trashed())<span class="block text-xs text-amber-700">Producto eliminado</span>@endif
                        </flux:table.cell>
                        <flux:table.cell>${{ number_format((float) $orderItem->unit_price, 2) }}</flux:table.cell>
                        <flux:table.cell>{{ $orderItem->quantity }}</flux:table.cell>
                        <flux:table.cell variant="strong">${{ number_format((float) $orderItem->subtotal, 2) }}</flux:table.cell>
                        <flux:table.cell>
                            <div class="flex items-center gap-2">
                                <flux:button href="{{ route('order-items.edit', $orderItem) }}" size="sm" variant="ghost">Editar</flux:button>
                                <form action="{{ route('order-items.destroy', $orderItem) }}" method="POST" onsubmit="return confirm('¿Seguro que deseas eliminar este artículo?')">
                                    @csrf
                                    @method('DELETE')
                                    <flux:button type="submit" size="sm" variant="danger">Eliminar</flux:button>
                                </form>
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="6" class="text-center">Todavía no hay artículos en los pedidos.</flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
    </section>
</x-layouts::app>
