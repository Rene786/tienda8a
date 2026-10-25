<x-layouts::app title="Mi carrito | TechStore">
    <section class="mx-auto max-w-5xl">
        <div class="mb-6 flex flex-wrap items-end justify-between gap-4">
            <div>
                <flux:badge color="blue">Tu compra</flux:badge>
                <h1 class="mt-3 text-2xl font-semibold">Mi carrito</h1>
                <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">Revisa los productos que agregaste.</p>
            </div>
            <flux:button href="{{ route('store') }}" variant="ghost">Seguir comprando</flux:button>
        </div>

        @if (session('success'))
            <flux:callout variant="success" class="mb-5">{{ session('success') }}</flux:callout>
        @endif
        @error('cart')
            <flux:callout variant="danger" class="mb-5">{{ $message }}</flux:callout>
        @enderror

        @if ($items->isEmpty())
            <flux:card class="py-12 text-center">
                <flux:icon name="shopping-cart" variant="outline" class="mx-auto mb-4 size-10 text-zinc-400" />
                <h2 class="text-lg font-semibold">Tu carrito está vacío</h2>
                <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">Explora el catálogo y agrega los productos que te interesan.</p>
                <flux:button href="{{ route('store') }}" variant="primary" class="mt-5">Explorar catálogo</flux:button>
            </flux:card>
        @else
            <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_18rem]">
                <div class="space-y-4">
                    @foreach ($items as $item)
                        <flux:card>
                            <div class="flex flex-col gap-5 sm:flex-row sm:items-center">
                                <div class="size-24 shrink-0 overflow-hidden rounded-xl bg-zinc-100 dark:bg-zinc-800">
                                    @if ($item->product?->image)
                                        <img src="{{ asset('storage/images/'.$item->product->image) }}" alt="{{ $item->product->name }}" class="size-full object-cover">
                                    @else
                                        <img src="{{ asset('images/product-placeholder.svg') }}" alt="{{ $item->product?->name ?? 'Producto no disponible' }}" class="size-full object-cover">
                                    @endif
                                </div>
                                <div class="min-w-0 flex-1">
                                    <h2 class="font-semibold">{{ $item->product?->name ?? 'Producto no disponible' }}</h2>
                                    @if ($item->product)
                                        <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">${{ number_format((float) $item->product->price, 2) }} cada uno</p>
                                        @if (! $item->product->status || $item->product->stock < $item->quantity)
                                            <p class="mt-1 text-sm text-amber-700 dark:text-amber-400">Revisa la cantidad: quedan {{ $item->product->stock }} disponibles.</p>
                                        @endif
                                    @endif
                                    @error('quantity')
                                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div class="flex flex-wrap items-end gap-3">
                                    @if ($item->product?->status && $item->product->stock > 0)
                                        <form action="{{ route('store-cart.items.update', $item) }}" method="POST" class="flex items-end gap-2">
                                            @csrf
                                            @method('PATCH')
                                            <flux:input name="quantity" type="number" label="Cantidad" value="{{ $item->quantity }}" min="1" max="{{ $item->product->stock }}" required />
                                            <flux:button type="submit" variant="ghost" size="sm">Actualizar</flux:button>
                                        </form>
                                    @else
                                        <flux:badge color="red">No disponible</flux:badge>
                                    @endif
                                    <form action="{{ route('store-cart.items.destroy', $item) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <flux:button type="submit" variant="danger" size="sm">Quitar</flux:button>
                                    </form>
                                </div>
                            </div>
                            @if ($item->product)
                                <p class="mt-4 border-t border-zinc-200 pt-3 text-right text-sm font-semibold dark:border-white/10">
                                    Subtotal: ${{ number_format((float) $item->product->price * $item->quantity, 2) }}
                                </p>
                            @endif
                        </flux:card>
                    @endforeach
                </div>

                <aside>
                    <flux:card class="lg:sticky lg:top-6">
                        <h2 class="text-lg font-semibold">Resumen</h2>
                        <div class="mt-4 flex justify-between border-t border-zinc-200 pt-4 text-base font-semibold dark:border-white/10">
                            <span>Total</span>
                            <span>${{ number_format($total, 2) }}</span>
                        </div>
                    </flux:card>
                </aside>
            </div>
        @endif
    </section>
</x-layouts::app>
