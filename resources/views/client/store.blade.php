<x-layouts::app title="Catálogo | TechStore">
    <div class="space-y-8">
        <section class="overflow-hidden rounded-2xl bg-gradient-to-br from-[#0F172A] via-[#1D4ED8] to-[#2563EB] px-6 py-12 text-white shadow-xl sm:px-10">
            <div class="mx-auto max-w-3xl text-center">
                <flux:badge color="blue" class="mb-4">Catálogo TechStore</flux:badge>
                <h1 class="text-3xl font-semibold tracking-tight sm:text-5xl">Encuentra tu próxima tecnología</h1>
                <p class="mx-auto mt-4 max-w-2xl text-sm leading-6 text-zinc-200 sm:text-base">
                    Explora nuestros productos y consulta su precio y disponibilidad.
                </p>
            </div>
        </section>

        <section aria-label="Filtros del catálogo">
            <flux:card>
                <form action="{{ route('store') }}" method="GET" class="grid gap-4 sm:grid-cols-[minmax(0,1fr)_minmax(0,16rem)_minmax(0,13rem)_auto_auto_auto] sm:items-end">
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
                                    {{ $category->name }}
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

                    <flux:button type="submit" variant="primary" icon="magnifying-glass" class="techstore-primary-button">Filtrar</flux:button>
                    <flux:button type="submit" variant="ghost">Ordenar</flux:button>
                    @if (request()->filled('search') || request()->filled('category') || request()->filled('sort'))
                        <flux:button href="{{ route('store') }}" variant="ghost">Limpiar</flux:button>
                    @endif
                </form>
            </flux:card>
        </section>

        <section aria-label="Productos disponibles">
            <div class="mb-5 flex flex-wrap items-end justify-between gap-3">
                <div>
                    <h2 class="text-2xl font-semibold tracking-tight">Nuestros productos</h2>
                    <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">
                        {{ number_format($products->total()) }} {{ $products->total() === 1 ? 'producto encontrado' : 'productos encontrados' }}
                    </p>
                </div>
            </div>

            @if ($products->isEmpty())
                <flux:card class="border-dashed py-12 text-center">
                    <div class="mx-auto flex max-w-sm flex-col items-center">
                        <flux:icon name="magnifying-glass" variant="outline" class="mb-4 size-10 text-zinc-400" />
                        <h3 class="text-lg font-semibold">No encontramos productos</h3>
                        <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">Prueba con otra búsqueda o cambia la categoría seleccionada.</p>
                        <flux:button href="{{ route('store') }}" variant="ghost" class="mt-4">Ver todo el catálogo</flux:button>
                    </div>
                </flux:card>
            @else
                <div class="grid gap-5 sm:grid-cols-2 xl:grid-cols-3">
                    @foreach ($products as $product)
                        <a href="{{ route('store.show', $product->slug) }}" aria-label="Ver detalles de {{ $product->name }}" class="group block rounded-2xl focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2">
                        <flux:card class="overflow-hidden p-0 transition duration-200 group-hover:-translate-y-1 group-hover:shadow-lg">
                            <div class="relative aspect-[16/10] overflow-hidden bg-[#0F172A]/[0.03] dark:bg-white/5">
                                @if ($product->image)
                                    <img src="{{ asset('storage/images/'.$product->image) }}" alt="{{ $product->name }}" loading="lazy" decoding="async" class="size-full object-cover transition duration-300 group-hover:scale-105">
                                @else
                                    <div class="flex size-full items-center justify-center text-[#0F172A]/20 dark:text-white/20">
                                        <flux:icon name="photo" variant="outline" class="size-12" />
                                    </div>
                                @endif
                                @if ($product->featured)
                                    <div class="absolute left-3 top-3">
                                        <flux:badge color="blue" icon="sparkles">Destacado</flux:badge>
                                    </div>
                                @endif
                            </div>

                            <div class="space-y-4 p-5">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="min-w-0">
                                        <flux:badge color="blue" size="sm">{{ $product->category->name }}</flux:badge>
                                        <h3 class="mt-2 truncate text-lg font-semibold">{{ $product->name }}</h3>
                                    </div>
                                    <span class="shrink-0 text-lg font-semibold text-[#16A34A]">${{ number_format((float) $product->price, 2) }}</span>
                                </div>

                                <p class="line-clamp-2 min-h-10 text-sm leading-5 text-zinc-600 dark:text-zinc-400">
                                    {{ $product->description ?: 'Consulta los detalles de este producto.' }}
                                </p>

                                <div class="flex items-center justify-between border-t border-[#E2E8F0] pt-4 dark:border-[#27364D]">
                                    <span class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Disponibilidad</span>
                                    <flux:badge :color="$product->stock > 0 ? 'green' : 'red'">
                                        {{ $product->stock > 0 ? 'Disponible' : 'Agotado' }}
                                    </flux:badge>
                                </div>
                            </div>
                        </flux:card>
                        </a>
                    @endforeach
                </div>

                @if ($products->hasPages())
                    <div class="mt-6">{{ $products->links() }}</div>
                @endif
            @endif
        </section>
    </div>
</x-layouts::app>
