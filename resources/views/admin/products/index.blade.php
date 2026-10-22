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

        <flux:card class="mb-6 border-[#E2E8F0] bg-white shadow-sm dark:border-[#27364D] dark:bg-[#111C31]">
            <form action="{{ route('products.index') }}" method="GET" class="grid gap-4 sm:grid-cols-2 sm:items-end xl:grid-cols-[minmax(0,1fr)_minmax(0,14rem)_minmax(0,12rem)_minmax(0,12rem)_auto_auto]">
                <flux:field>
                    <flux:label for="search">Buscar productos</flux:label>
                    <flux:input id="search" name="search" type="search" value="{{ request('search') }}" placeholder="Nombre o descripción" />
                </flux:field>

                <flux:field>
                    <flux:label for="category">Categoría</flux:label>
                    <flux:select id="category" name="category">
                        <option value="">Todas las categorías</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" @selected((string) request('category') === (string) $category->id)>
                                {{ $category->name }}{{ $category->status ? '' : ' (Inactiva)' }}
                            </option>
                        @endforeach
                    </flux:select>
                </flux:field>

                <flux:field>
                    <flux:label for="sort">Ordenar por</flux:label>
                    <flux:select id="sort" name="sort">
                        <option value="featured" @selected(request('sort', 'featured') === 'featured')>Destacados</option>
                        <option value="name_asc" @selected(request('sort') === 'name_asc')>Nombre: A–Z</option>
                        <option value="name_desc" @selected(request('sort') === 'name_desc')>Nombre: Z–A</option>
                    </flux:select>
                </flux:field>

                <flux:field>
                    <flux:label for="status">Estado</flux:label>
                    <flux:select id="status" name="status">
                        @foreach ($statusOptions as $value => $label)
                            <option value="{{ $value }}" @selected(request('status', 'all') === $value)>{{ $label }}</option>
                        @endforeach
                    </flux:select>
                </flux:field>

                <flux:button type="submit" variant="primary" icon="magnifying-glass" class="techstore-primary-button">Filtrar</flux:button>
                @if (request()->filled('search') || request()->filled('category') || request()->filled('sort') || request('status', 'all') !== 'all')
                    <flux:button href="{{ route('products.index') }}" variant="ghost">Limpiar</flux:button>
                @endif
            </form>
        </flux:card>

        <div class="mb-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <flux:card class="border-[#E2E8F0] border-t-2 border-t-[#0F172A] bg-white shadow-sm dark:border-[#27364D] dark:bg-[#111C31]">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <flux:text class="text-sm text-[#1E293B]/70 dark:text-zinc-400">Productos encontrados</flux:text>
                        <flux:heading size="xl" class="mt-2 text-[#0F172A] dark:text-white">{{ number_format($products->total()) }}</flux:heading>
                        <flux:text class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">Con los filtros actuales</flux:text>
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
            <div class="space-y-3">
                @foreach ($products as $product)
                    <flux:card class="group border-[#E2E8F0] bg-white p-3 shadow-sm transition duration-200 hover:border-[#2563EB]/40 hover:shadow-md dark:border-[#27364D] dark:bg-[#111C31] sm:p-4">
                        <div class="flex items-center gap-3 sm:gap-4">
                            <div class="relative size-16 shrink-0 overflow-hidden rounded-lg bg-[#0F172A]/[0.03] sm:size-20">
                                @if ($product->image)
                                    <img src="{{ asset('storage/images/'.$product->image) }}" alt="{{ $product->name }}" loading="lazy" decoding="async" class="size-full object-cover">
                                @else
                                    <img src="{{ asset('images/product-placeholder.svg') }}" alt="{{ $product->name }}" loading="lazy" decoding="async" class="size-full object-cover">
                                @endif
                            </div>

                            <div class="min-w-0 flex-1 space-y-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    <flux:badge color="blue" size="sm">{{ $product->category?->name ?? 'Sin categoría' }}</flux:badge>
                                    @if ($product->trashed())
                                        <flux:badge color="red">Eliminado</flux:badge>
                                    @else
                                        <flux:badge :color="$product->status ? 'green' : 'zinc'" :icon="$product->status ? 'check-circle' : 'minus-circle'">
                                            {{ $product->status ? 'Activo' : 'Inactivo' }}
                                        </flux:badge>
                                        @if ($product->featured)
                                            <flux:badge color="blue" icon="sparkles">Destacado</flux:badge>
                                        @endif
                                    @endif
                                </div>
                                <flux:heading size="lg" class="truncate text-[#0F172A] dark:text-white">{{ $product->name }}</flux:heading>
                                <flux:text class="truncate text-xs text-zinc-500 dark:text-zinc-400">/{{ $product->slug }}</flux:text>
                                @if ($product->description)
                                    <p class="line-clamp-1 text-sm text-zinc-600 dark:text-zinc-400">{{ $product->description }}</p>
                                @endif
                            </div>

                            <div class="flex flex-col items-end gap-2 border-l border-[#E2E8F0] pl-3 dark:border-[#27364D] sm:flex-row sm:flex-wrap sm:items-center sm:justify-between sm:gap-4 sm:pl-4">
                                <div class="flex items-center gap-2">
                                    <span class="text-lg font-semibold text-[#16A34A]">${{ number_format((float) $product->price, 2) }}</span>
                                    <flux:badge :color="$product->stock > 5 ? 'green' : ($product->stock > 0 ? 'amber' : 'red')">
                                        {{ number_format($product->stock) }}
                                    </flux:badge>
                                </div>
                                <div class="flex items-center gap-1">
                                    @unless ($product->trashed())
                                        <flux:button href="{{ route('products.edit', $product->id) }}" size="sm" variant="ghost" icon="pencil-square">Editar</flux:button>
                                        <form action="{{ route('products.destroy', $product->id) }}" method="POST" onsubmit="return confirm('¿Seguro que deseas eliminar este producto?')">
                                            @csrf
                                            @method('DELETE')
                                            <flux:button type="submit" size="sm" variant="danger" icon="trash" aria-label="Eliminar {{ $product->name }}" />
                                        </form>
                                    @endunless
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
