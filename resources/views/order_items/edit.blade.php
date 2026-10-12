<x-layouts::app>
    <flux:card class="mx-auto max-w-2xl">
        <div class="mb-6">
            <flux:badge color="blue">Administración</flux:badge>
            <h1 class="mt-3 text-2xl font-semibold">Editar artículo del pedido</h1>
            <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">El subtotal y el total del pedido se recalculan al guardar.</p>
        </div>
        <form action="{{ route('order-items.update', $orderItem) }}" method="POST" class="space-y-5">
            @csrf
            @method('PUT')
            <flux:select id="order_id" name="order_id" label="Pedido" required>
                @foreach ($orders as $order)
                    <option value="{{ $order->id }}" @selected(old('order_id', $orderItem->order_id) == $order->id)>{{ $order->order_number }} — {{ $order->user?->name ?? 'Cliente' }}{{ $order->trashed() ? ' (eliminado)' : '' }}</option>
                @endforeach
            </flux:select>
            <flux:select id="product_id" name="product_id" label="Producto">
                <option value="">Conservar nombre y precio históricos</option>
                @foreach ($products as $product)
                    <option value="{{ $product->id }}" @selected(old('product_id', $orderItem->product_id) == $product->id)>{{ $product->name }} — ${{ number_format((float) $product->price, 2) }}{{ $product->trashed() ? ' (eliminado)' : '' }}</option>
                @endforeach
            </flux:select>
            <flux:input id="quantity" name="quantity" type="number" label="Cantidad" min="1" value="{{ old('quantity', $orderItem->quantity) }}" required />
            <div class="flex justify-end gap-2 border-t border-zinc-200 pt-5 dark:border-white/10">
                <flux:button href="{{ route('order-items.index') }}" variant="ghost">Cancelar</flux:button>
                <flux:button type="submit" variant="primary">Guardar cambios</flux:button>
            </div>
        </form>
    </flux:card>
</x-layouts::app>
