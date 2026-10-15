<x-layouts::app>
    <flux:card class="mx-auto max-w-2xl">
        <div class="mb-6">
            <flux:badge color="blue">Administración</flux:badge>
            <h1 class="mt-3 text-2xl font-semibold">Editar artículo del carrito</h1>
            <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">Actualiza el carrito, el producto o la cantidad.</p>
        </div>
        <form action="{{ route('cart-items.update', $cartItem) }}" method="POST" class="space-y-5">
            @csrf
            @method('PUT')
            <flux:select id="cart_id" name="cart_id" label="Carrito" required>
                @foreach ($carts as $cart)
                    <option value="{{ $cart->id }}" @selected(old('cart_id', $cartItem->cart_id) == $cart->id)>Carrito #{{ $cart->id }} — {{ $cart->user?->name ?? 'Usuario no disponible' }}{{ $cart->trashed() ? ' (eliminado)' : '' }}</option>
                @endforeach
            </flux:select>
            <flux:select id="product_id" name="product_id" label="Producto" required>
                @foreach ($products as $product)
                    <option value="{{ $product->id }}" @selected(old('product_id', $cartItem->product_id) == $product->id)>{{ $product->name }} — ${{ number_format((float) $product->price, 2) }}{{ $product->trashed() ? ' (eliminado)' : '' }}</option>
                @endforeach
            </flux:select>
            <flux:input id="quantity" name="quantity" type="number" label="Cantidad" min="1" value="{{ old('quantity', $cartItem->quantity) }}" required />
            <div class="flex justify-end gap-2 border-t border-zinc-200 pt-5 dark:border-white/10">
                <flux:button href="{{ route('cart-items.index') }}" variant="ghost">Cancelar</flux:button>
                <flux:button type="submit" variant="primary">Guardar cambios</flux:button>
            </div>
        </form>
    </flux:card>
</x-layouts::app>
