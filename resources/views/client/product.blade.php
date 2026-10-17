<x-layouts::app :title="$product->name.' | TechStore'">
    <div class="space-y-6">
        <nav aria-label="Ruta de navegación" class="flex flex-wrap items-center gap-2 text-sm text-zinc-500 dark:text-zinc-400">
            <a href="{{ route('store') }}" class="transition hover:text-[#2563EB]">Catálogo</a>
            <flux:icon name="chevron-right" variant="micro" class="size-4" />
            <a href="{{ route('store', ['category' => $product->category_id]) }}" class="transition hover:text-[#2563EB]">{{ $product->category->name }}</a>
            <flux:icon name="chevron-right" variant="micro" class="size-4" />
            <span aria-current="page" class="font-medium text-[#1E293B] dark:text-white">{{ $product->name }}</span>
        </nav>

        <section class="grid items-start gap-6 lg:grid-cols-12 lg:gap-8">
            <div class="space-y-5 lg:col-span-7">
                <flux:card class="overflow-hidden p-2 sm:p-3">
                    @if ($images->isNotEmpty())
                        <div class="relative aspect-[4/3] overflow-hidden rounded-xl bg-[#0F172A]/[0.03] dark:bg-white/5">
                            <img src="{{ asset('storage/images/'.$images->first()) }}" alt="{{ $product->name }}" class="size-full object-cover">
                            @if ($product->featured)
                                <div class="absolute left-4 top-4">
                                    <flux:badge color="amber" icon="sparkles">Producto destacado</flux:badge>
                                </div>
                            @endif
                        </div>
                        @if ($images->count() > 1)
                            <div class="mt-3 grid grid-cols-2 gap-3">
                                @foreach ($images->slice(1) as $image)
                                    <div class="aspect-[4/3] overflow-hidden rounded-xl bg-[#0F172A]/[0.03] dark:bg-white/5">
                                        <img src="{{ asset('storage/images/'.$image) }}" alt="Vista adicional de {{ $product->name }}" loading="lazy" decoding="async" class="size-full object-cover transition duration-300 hover:scale-105">
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    @else
                        <div class="flex aspect-[4/3] items-center justify-center rounded-xl bg-[#0F172A]/[0.03] text-[#0F172A]/20 dark:bg-white/5 dark:text-white/20">
                            <flux:icon name="photo" variant="outline" class="size-16" />
                        </div>
                    @endif
                </flux:card>

                <flux:card>
                    <div class="flex items-center gap-3">
                        <span class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-[#2563EB]/10 text-[#2563EB]">
                            <flux:icon name="information-circle" variant="outline" class="size-5" />
                        </span>
                        <div>
                            <h2 class="font-semibold">Detalles del producto</h2>
                            <p class="text-sm text-zinc-500 dark:text-zinc-400">Información proporcionada por TechStore</p>
                        </div>
                    </div>

                    <dl class="mt-5 divide-y divide-[#E2E8F0] dark:divide-[#27364D]">
                        <div class="flex items-center justify-between gap-4 py-3 text-sm">
                            <dt class="text-zinc-500 dark:text-zinc-400">Categoría</dt>
                            <dd class="font-medium text-right">{{ $product->category->name }}</dd>
                        </div>
                        <div class="flex items-center justify-between gap-4 py-3 text-sm">
                            <dt class="text-zinc-500 dark:text-zinc-400">Código</dt>
                            <dd class="font-medium">{{ $product->slug }}</dd>
                        </div>
                        <div class="flex items-center justify-between gap-4 py-3 text-sm">
                            <dt class="text-zinc-500 dark:text-zinc-400">Estado</dt>
                            <dd><flux:badge color="green">Activo</flux:badge></dd>
                        </div>
                    </dl>
                </flux:card>
            </div>

            <flux:card class="flex flex-col gap-6 lg:sticky lg:top-6 lg:col-span-5">
                <div class="space-y-4">
                    <div class="flex flex-wrap items-center gap-2">
                        <flux:badge color="blue">{{ $product->category->name }}</flux:badge>
                        @if ($product->featured)
                            <flux:badge color="amber" icon="sparkles">Producto destacado</flux:badge>
                        @endif
                    </div>

                    <h1 class="text-3xl font-semibold tracking-tight text-[#0F172A] dark:text-white sm:text-4xl">{{ $product->name }}</h1>

                    <div class="rounded-xl border border-[#E2E8F0] bg-[#F8FAFC] p-4 dark:border-[#27364D] dark:bg-[#0B1220]">
                        <p class="text-xs font-medium uppercase tracking-wide text-zinc-500 dark:text-zinc-400">Precio</p>
                        <p class="mt-1 text-3xl font-semibold tracking-tight text-[#16A34A]">${{ number_format((float) $product->price, 2) }}</p>
                    </div>

                    <div class="flex items-center justify-between gap-4 rounded-xl border border-[#E2E8F0] px-4 py-3 dark:border-[#27364D]">
                        <div class="flex items-center gap-3">
                            <span class="flex size-9 items-center justify-center rounded-full {{ $product->stock > 0 ? 'bg-[#16A34A]/10 text-[#16A34A]' : 'bg-red-500/10 text-red-500' }}">
                                <flux:icon :name="$product->stock > 0 ? 'check' : 'x-mark'" variant="micro" class="size-4" />
                            </span>
                            <div>
                                <p class="text-sm font-medium">{{ $product->stock > 0 ? 'Disponible' : 'Agotado' }}</p>
                                <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ number_format($product->stock) }} en existencia</p>
                            </div>
                        </div>
                        <flux:badge :color="$product->stock > 0 ? 'green' : 'red'">
                            {{ $product->stock > 0 ? 'En stock' : 'Sin stock' }}
                        </flux:badge>
                    </div>
                </div>

                <div class="space-y-3 border-t border-[#E2E8F0] pt-5 dark:border-[#27364D]">
                    <h2 class="text-sm font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">Descripción</h2>
                    <p class="whitespace-pre-line text-sm leading-7 text-zinc-600 dark:text-zinc-400">
                        {{ $product->description ?: 'No hay una descripción disponible para este producto.' }}
                    </p>
                </div>

                <div class="mt-auto space-y-3 border-t border-[#E2E8F0] pt-5 dark:border-[#27364D]">
                    <flux:button type="button" variant="primary" icon="shopping-cart" class="techstore-primary-button w-full" disabled>
                        Agregar al carrito (próximamente)
                    </flux:button>
                    <p class="text-center text-xs text-zinc-500 dark:text-zinc-400">La compra en línea todavía no está disponible.</p>
                </div>
            </flux:card>
        </section>
    </div>
</x-layouts::app>
