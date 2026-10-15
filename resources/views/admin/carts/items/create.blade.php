<x-layouts::app>
    <flux:card class="mx-auto max-w-2xl">
        <div class="mb-6">
            <flux:badge color="blue">Administración</flux:badge>
            <h1 class="mt-3 text-2xl font-semibold">Agregar artículo al carrito</h1>
            <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">Selecciona un carrito, un producto y la cantidad.</p>
        </div>
        <form action="{{ route('cart-items.store') }}" method="POST" class="space-y-5">
            @csrf
            <flux:select id="cart_id" name="cart_id" label="Carrito" placeholder="Selecciona un carrito" required>
                @foreach ($carts as $cart)
                    <option value="{{ $cart->id }}" @selected(old('cart_id') == $cart->id)>Carrito #{{ $cart->id }} — {{ $cart->user?->name ?? 'Usuario no disponible' }}</option>
                @endforeach
            </flux:select>
            <flux:select id="product_id" name="product_id" label="Producto" placeholder="Selecciona un producto" required>
                @foreach ($products as $product)
                    <option value="{{ $product->id }}" @selected(old('product_id') == $product->id)>{{ $product->name }} — ${{ number_format((float) $product->price, 2) }}</option>
                @endforeach
            </flux:select>
            <flux:input id="quantity" name="quantity" type="number" label="Cantidad" min="1" value="{{ old('quantity', 1) }}" required />
            @if ($carts->isEmpty() || $products->isEmpty())
                <flux:callout variant="warning">Para agregar artículos necesitas al menos un carrito y un producto activo.</flux:callout>
            @endif
            <div class="flex justify-end gap-2 border-t border-zinc-200 pt-5 dark:border-white/10">
                <flux:button href="{{ route('cart-items.index') }}" variant="ghost">Cancelar</flux:button>
                <flux:button type="submit" variant="primary" :disabled="$carts->isEmpty() || $products->isEmpty()">Agregar artículo</flux:button>
            </div>
        </form>
    </flux:card>
</x-layouts::app>
