<x-layouts::app>
    <flux:card class="mx-auto max-w-2xl">
        <div class="mb-6">
            <flux:badge color="blue">Administración</flux:badge>
            <h1 class="mt-3 text-2xl font-semibold">Agregar artículo al pedido</h1>
            <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">El precio y el subtotal se calculan con el producto seleccionado.</p>
        </div>
        <form action="{{ route('order-items.store') }}" method="POST" class="space-y-5">
            @csrf
            <flux:select id="order_id" name="order_id" label="Pedido" placeholder="Selecciona un pedido" required>
                @foreach ($orders as $order)
                    <option value="{{ $order->id }}" @selected(old('order_id') == $order->id)>{{ $order->order_number }} — {{ $order->user?->name ?? 'Cliente' }}</option>
                @endforeach
            </flux:select>
            <flux:select id="product_id" name="product_id" label="Producto" placeholder="Selecciona un producto" required>
                @foreach ($products as $product)
                    <option value="{{ $product->id }}" @selected(old('product_id') == $product->id)>{{ $product->name }} — ${{ number_format((float) $product->price, 2) }}</option>
                @endforeach
            </flux:select>
            <flux:input id="quantity" name="quantity" type="number" label="Cantidad" min="1" value="{{ old('quantity', 1) }}" required />
            @if ($orders->isEmpty() || $products->isEmpty())
                <flux:callout variant="warning">Necesitas al menos un pedido y un producto activo para continuar.</flux:callout>
            @endif
            <div class="flex justify-end gap-2 border-t border-zinc-200 pt-5 dark:border-white/10">
                <flux:button href="{{ route('order-items.index') }}" variant="ghost">Cancelar</flux:button>
                <flux:button type="submit" variant="primary" :disabled="$orders->isEmpty() || $products->isEmpty()">Agregar artículo</flux:button>
            </div>
        </form>
    </flux:card>
</x-layouts::app>
