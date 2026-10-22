<x-layouts::app>
    <section class="mx-auto max-w-6xl">
        <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
            <div>
                <flux:badge color="blue">Administración</flux:badge>
                <h1 class="mt-3 text-2xl font-semibold">Pedidos</h1>
                <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">Consulta y administra los pedidos de la tienda.</p>
            </div>
            <flux:button href="{{ route('orders.create') }}" variant="primary">Nuevo pedido</flux:button>
        </div>
        @if (session('success'))
            <flux:callout variant="success" class="mb-4">{{ session('success') }}</flux:callout>
        @endif
        <x-admin.status-filter route-name="orders.index" :options="$statusOptions" />
        <flux:table>
            <flux:table.columns>
                <flux:table.column>Pedido</flux:table.column>
                <flux:table.column>Cliente</flux:table.column>
                <flux:table.column>Total</flux:table.column>
                <flux:table.column>Estado</flux:table.column>
                <flux:table.column>Pago</flux:table.column>
                <flux:table.column>Artículos</flux:table.column>
                <flux:table.column>Acciones</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @forelse ($orders as $order)
                    <flux:table.row>
                        <flux:table.cell variant="strong">
                            {{ $order->order_number }}
                            <span class="block text-xs font-normal text-zinc-500">{{ $order->created_at?->format('d/m/Y') }}</span>
                        </flux:table.cell>
                        <flux:table.cell>
                            {{ $order->user?->name ?? 'Usuario no disponible' }}
                            @if ($order->user?->trashed())<span class="block text-xs text-amber-700">Usuario eliminado</span>@endif
                        </flux:table.cell>
                        <flux:table.cell>${{ number_format((float) $order->total, 2) }}</flux:table.cell>
                        <flux:table.cell>
                            <flux:badge color="blue">{{ ucfirst($order->status) }}</flux:badge>
                            @if ($order->trashed())
                                <span class="mt-1 block"><flux:badge color="red">Eliminado</flux:badge></span>
                            @endif
                        </flux:table.cell>
                        <flux:table.cell>{{ ucfirst($order->payment_status) }}</flux:table.cell>
                        <flux:table.cell>{{ $order->items_count }}</flux:table.cell>
                        <flux:table.cell>
                            @unless ($order->trashed())
                                <div class="flex items-center gap-2">
                                    <flux:button href="{{ route('orders.edit', $order) }}" size="sm" variant="ghost">Editar</flux:button>
                                    <form action="{{ route('orders.destroy', $order) }}" method="POST" onsubmit="return confirm('¿Seguro que deseas eliminar este pedido?')">
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
                        <flux:table.cell colspan="7" class="text-center">Todavía no hay pedidos registrados.</flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
        @if ($orders->hasPages())
            <div class="mt-6">{{ $orders->links() }}</div>
        @endif
    </section>
</x-layouts::app>
