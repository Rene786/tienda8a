<x-layouts::app>
    <section class="mx-auto max-w-6xl">
        <div class="mb-8 flex flex-wrap items-end justify-between gap-4">
            <div class="max-w-2xl">
                <flux:badge color="blue" icon="squares-2x2">Catálogo</flux:badge>
                <h1 class="mt-3 text-3xl font-semibold tracking-tight text-[#0F172A] dark:text-white">Productos</h1>
                <p class="mt-2 text-sm leading-6 text-[#1E293B]/75 dark:text-zinc-400">Administra el inventario, consulta la disponibilidad y mantén actualizado el catálogo de TechStore.</p>
            </div>
            <flux:button href="{{ route('products.create') }}" variant="primary" icon="plus" class="techstore-primary-button">Nuevo producto</flux:button>
        </div>

        @if (session('success'))
            <flux:callout variant="success" icon="check-circle" class="mb-6">{{ session('success') }}</flux:callout>
        @endif

        <div class="mb-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <flux:card class="border-[#E2E8F0] border-t-2 border-t-[#0F172A] bg-white shadow-sm dark:border-[#27364D] dark:bg-[#111C31]">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <flux:text class="text-sm text-[#1E293B]/70 dark:text-zinc-400">Productos registrados</flux:text>
                        <flux:heading size="xl" class="mt-2 text-[#0F172A] dark:text-white">{{ number_format($products->total()) }}</flux:heading>
                        <flux:text class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">En todo el catálogo</flux:text>
                    </div>
                    <span class="flex size-10 items-center justify-center rounded-xl bg-[#0F172A]/5 text-[#0F172A] dark:bg-white/10 dark:text-white">
                        <flux:icon name="squares-2x2" variant="outline" class="size-5" />
                    </span>
                </div>
            </flux:card>
            <flux:card class="border-[#E2E8F0] border-t-2 border-t-[#2563EB] bg-white shadow-sm dark:border-[#27364D] dark:bg-[#111C31]">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <flux:text class="text-sm text-[#1E293B]/70 dark:text-zinc-400">En esta página</flux:text>
                        <flux:heading size="xl" class="mt-2 text-[#0F172A] dark:text-white">{{ $products->count() }}</flux:heading>
                        <flux:text class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">Productos mostrados</flux:text>
                    </div>
                    <span class="flex size-10 items-center justify-center rounded-xl bg-[#2563EB]/10 text-[#2563EB]">
                        <flux:icon name="clipboard-document-list" variant="outline" class="size-5" />
                    </span>
                </div>
            </flux:card>
            <flux:card class="border-[#E2E8F0] border-t-2 border-t-[#16A34A] bg-white shadow-sm dark:border-[#27364D] dark:bg-[#111C31]">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <flux:text class="text-sm text-[#1E293B]/70 dark:text-zinc-400">Con existencias</flux:text>
                        <flux:heading size="xl" class="mt-2 text-[#16A34A]">{{ $products->getCollection()->where('stock', '>', 0)->count() }}</flux:heading>
                        <flux:text class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">En esta página</flux:text>
                    </div>
                    <span class="flex size-10 items-center justify-center rounded-xl bg-[#16A34A]/10 text-[#16A34A]">
                        <flux:icon name="check-circle" variant="outline" class="size-5" />
                    </span>
                </div>
            </flux:card>
            <flux:card class="border-[#E2E8F0] border-t-2 border-t-[#1D4ED8] bg-white shadow-sm dark:border-[#27364D] dark:bg-[#111C31]">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <flux:text class="text-sm text-[#1E003B]/70 dark:text-zinc-400">Destacados</flux:text>
                        <flux:heading size="xl" class="mt-2 text-[#2563EB]">{{ $products->getCollection()->where('featured', true)->count() }}</flux:heading>
                        <flux:text class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">En esta página</flux:text>
                    </div>
                    <span class="flex size-10 items-center justify-center rounded-xl bg-[#1D4ED8]/10 text-[#1D4ED8]">
                        <flux:icon name="sparkles" variant="outline" class="size-5" />
                    </span>
                </div>
            </flux:card>
        </div>

        <div class="mb-4 flex flex-wrap items-end justify-between gap-2">
            <div>
                <flux:heading size="lg">Inventario</flux:heading>
                <flux:text class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Mostrando {{ $products->firstItem() ?? 0 }}–{{ $products->lastItem() ?? 0 }} de {{ number_format($products->total()) }} productos</flux:text>
            </div>
            <flux:badge color="blue" icon="clock">Más recientes</flux:badge>
        </div>

        @if ($products->isEmpty())
            <flux:card class="border-dashed border-[#CBD5E1] bg-white py-12 text-center shadow-sm dark:border-[#27364D] dark:bg-[#111C31]">
                <div class="mx-auto flex max-w-sm flex-col items-center">
                    <div class="mb-4 flex size-12 items-center justify-center rounded-full bg-[#2563EB]/10 text-[#2563EB]">
                        <flux:icon name="archive-box" variant="outline" class="size-6" />
                    </div>
                    <flux:heading size="lg">Aún no hay productos</flux:heading>
                    <flux:text class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">Agrega el primer producto para empezar a organizar tu catálogo.</flux:text>
                    <flux:button href="{{ route('products.create') }}" variant="primary" icon="plus" class="techstore-primary-button mt-5">Crear producto</flux:button>
                </div>
            </flux:card>
        @else
            <div class="grid gap-5 sm:grid-cols-2 xl:grid-cols-3">
                @foreach ($products as $product)
                    <flux:card class="group overflow-hidden border-[#E2E8F0] bg-white p-0 shadow-sm transition duration-200 hover:-translate-y-1 hover:border-[#2563EB]/40 hover:shadow-lg dark:border-[#27364D] dark:bg-[#111C31]">
                        <div class="relative aspect-[16/10] overflow-hidden bg-[#0F172A]/[0.03]">
                            @if ($product->image)
                                <img src="{{ asset('storage/images/'.$product->image) }}" alt="{{ $product->name }}" loading="lazy" decoding="async" class="size-full object-cover transition duration-300 group-hover:scale-105">
                            @else
                                <div class="flex size-full items-center justify-center text-[#0F172A]/20">
                                    <flux:icon name="photo" variant="outline" class="size-12" />
                                </div>
                            @endif
                            <div class="absolute left-3 top-3 flex flex-wrap gap-2">
                                @if ($product->featured)
                                    <flux:badge color="blue" icon="sparkles">Destacado</flux:badge>
                                @endif
                                <flux:badge :color="$product->status ? 'green' : 'zinc'" :icon="$product->status ? 'check-circle' : 'minus-circle'">
                                    {{ $product->status ? 'Activo' : 'Inactivo' }}
                                </flux:badge>
                            </div>
                        </div>

                        <div class="space-y-4 p-5">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <flux:badge color="blue" size="sm">{{ $product->category?->name ?? 'Sin categoría' }}</flux:badge>
                                    <flux:heading size="lg" class="mt-2 truncate text-[#0F172A] dark:text-white">{{ $product->name }}</flux:heading>
                                    <flux:text class="mt-1 truncate text-xs text-zinc-500 dark:text-zinc-400">/{{ $product->slug }}</flux:text>
                                </div>
                                <span class="shrink-0 text-lg font-semibold text-[#16A34A]">${{ number_format((float) $product->price, 2) }}</span>
                            </div>

                            @if ($product->description)
                                <p class="line-clamp-2 min-h-10 text-sm leading-5 text-zinc-600 dark:text-zinc-400">{{ $product->description }}</p>
                            @else
                                <p class="min-h-10 text-sm text-zinc-400">Sin descripción.</p>
                            @endif

                            <div class="flex items-center justify-between border-t border-[#E2E8F0] pt-4">
                                <div class="flex items-center gap-2">
                                    <flux:badge :color="$product->stock > 5 ? 'green' : ($product->stock > 0 ? 'amber' : 'red')">
                                        {{ number_format($product->stock) }}
                                    </flux:badge>
                                    <span class="text-xs font-medium text-[#1E293B]/70 dark:text-zinc-400">
                                        {{ $product->stock > 5 ? 'En existencia' : ($product->stock > 0 ? 'Pocas unidades' : 'Agotado') }}
                                    </span>
                                </div>
                                <div class="flex items-center gap-1">
                                    <flux:button href="{{ route('products.edit', $product->id) }}" size="sm" variant="ghost" icon="pencil-square">Editar</flux:button>
                                    <form action="{{ route('products.destroy', $product->id) }}" method="POST" onsubmit="return confirm('¿Seguro que deseas eliminar este producto?')">
                                        @csrf
                                        @method('DELETE')
                                        <flux:button type="submit" size="sm" variant="danger" icon="trash" aria-label="Eliminar {{ $product->name }}" />
                                    </form>
                                </div>
                            </div>
                        </div>
                    </flux:card>
                @endforeach
            </div>

            @if ($products->hasPages())
                <div class="mt-6">{{ $products->links() }}</div>
            @endif
        @endif
    </section>
</x-layouts::app>
