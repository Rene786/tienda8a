<x-layouts::app :title="$product->name.' | TechStore'">
    <div class="space-y-8">
        <nav aria-label="Ruta de navegación" class="flex flex-wrap items-center gap-2 text-sm text-zinc-500 dark:text-zinc-400">
            <a href="{{ route('store') }}" class="transition hover:text-[#2563EB]">Catálogo</a>
            <flux:icon name="chevron-right" variant="micro" class="size-4" />
            <a href="{{ route('store', ['category' => $product->category_id]) }}" class="transition hover:text-[#2563EB]">{{ $product->category->name }}</a>
            <flux:icon name="chevron-right" variant="micro" class="size-4" />
            <span aria-current="page" class="font-medium text-[#1E293B] dark:text-white">{{ $product->name }}</span>
        </nav>

        <section class="grid items-start gap-6 lg:grid-cols-12 lg:gap-8">
            <div class="space-y-6 lg:col-span-7">
                <div
                    x-data="{
                        activeSlide: 0,
                        totalSlides: {{ max($images->count(), 1) }},
                        previous() { this.activeSlide = (this.activeSlide - 1 + this.totalSlides) % this.totalSlides },
                        next() { this.activeSlide = (this.activeSlide + 1) % this.totalSlides }
                    }"
                    role="region"
                    aria-label="Galería de imágenes del producto"
                    class="space-y-4"
                >
                    <div class="group relative overflow-hidden rounded-3xl border border-[#E2E8F0] bg-[#0F172A] shadow-xl dark:border-[#27364D]">
                        <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_center,_var(--tw-gradient-stops))] from-blue-900/40 via-[#0F172A] to-[#0B1220]"></div>
                        <div class="relative aspect-[4/3] overflow-hidden sm:aspect-[16/10]">
                            @if ($images->isNotEmpty())
                                <div class="flex h-full transition-transform duration-500 ease-out motion-reduce:transition-none" x-bind:style="'transform: translateX(-' + (activeSlide * 100) + '%)'">
                                    @foreach ($images as $image)
                                        <div class="flex h-full w-full shrink-0 items-center justify-center p-6 sm:p-10">
                                            <img
                                                src="{{ asset('storage/images/'.$image) }}"
                                                alt="{{ $product->name }} — imagen {{ $loop->iteration }}"
                                                @if ($loop->first) fetchpriority="high" @else loading="lazy" decoding="async" @endif
                                                class="size-full object-contain drop-shadow-2xl"
                                            >
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="flex h-full items-center justify-center p-8 sm:p-12">
                                    <img src="{{ asset('images/product-placeholder.svg') }}" alt="{{ $product->name }}" class="size-full object-contain">
                                </div>
                            @endif
                        </div>

                        <div class="pointer-events-none absolute inset-x-0 top-0 flex items-start justify-between p-4 sm:p-5">
                            <flux:badge color="blue">{{ $product->category->name }}</flux:badge>
                            @if ($product->featured)
                                <flux:badge color="amber" icon="sparkles">Destacado</flux:badge>
                            @endif
                        </div>

                        @if ($images->count() > 1)
                            <button
                                type="button"
                                x-on:click="previous()"
                                aria-label="Imagen anterior"
                                class="absolute left-3 top-1/2 flex size-10 -translate-y-1/2 items-center justify-center rounded-full border border-white/20 bg-[#0F172A]/70 text-white shadow-lg backdrop-blur transition hover:bg-[#2563EB] focus:outline-none focus-visible:ring-2 focus-visible:ring-white sm:left-5 sm:size-12"
                            >
                                <flux:icon name="chevron-left" variant="micro" class="size-5" />
                            </button>
                            <button
                                type="button"
                                x-on:click="next()"
                                aria-label="Imagen siguiente"
                                class="absolute right-3 top-1/2 flex size-10 -translate-y-1/2 items-center justify-center rounded-full border border-white/20 bg-[#0F172A]/70 text-white shadow-lg backdrop-blur transition hover:bg-[#2563EB] focus:outline-none focus-visible:ring-2 focus-visible:ring-white sm:right-5 sm:size-12"
                            >
                                <flux:icon name="chevron-right" variant="micro" class="size-5" />
                            </button>
                            <span aria-live="polite" class="absolute bottom-4 right-4 rounded-full bg-[#0F172A]/70 px-3 py-1 text-xs font-medium text-white backdrop-blur">
                                <span x-text="activeSlide + 1"></span> / {{ $images->count() }}
                            </span>
                        @endif
                    </div>

                    @if ($images->count() > 1)
                        <div class="flex gap-3 overflow-x-auto pb-1" aria-label="Seleccionar imagen del producto">
                            @foreach ($images as $image)
                                <button
                                    type="button"
                                    x-on:click="activeSlide = {{ $loop->index }}"
                                    x-bind:aria-current="activeSlide === {{ $loop->index }} ? 'true' : 'false'"
                                    aria-label="Mostrar imagen {{ $loop->iteration }}"
                                    class="size-20 shrink-0 overflow-hidden rounded-xl border-2 bg-white p-1 transition hover:border-[#2563EB] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#2563EB] sm:size-24"
                                    x-bind:class="activeSlide === {{ $loop->index }} ? 'border-[#2563EB] shadow-md' : 'border-[#E2E8F0] dark:border-[#27364D]'"
                                >
                                    <img src="{{ asset('storage/images/'.$image) }}" alt="" loading="lazy" decoding="async" class="size-full rounded-lg object-cover">
                                </button>
                            @endforeach
                        </div>
                    @endif
                </div>

                <flux:card class="border-[#E2E8F0] dark:border-[#27364D]">
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
                            <dd class="text-right font-medium">{{ $product->category->name }}</dd>
                        </div>
                        <div class="flex items-center justify-between gap-4 py-3 text-sm">
                            <dt class="text-zinc-500 dark:text-zinc-400">Código</dt>
                            <dd class="font-medium">{{ $product->slug }}</dd>
                        </div>
                        <div class="flex items-center justify-between gap-4 py-3 text-sm">
                            <dt class="text-zinc-500 dark:text-zinc-400">Disponibilidad</dt>
                            <dd>
                                <flux:badge :color="$product->stock > 0 ? 'green' : 'red'">
                                    {{ $product->stock > 0 ? 'Disponible' : 'Agotado' }}
                                </flux:badge>
                            </dd>
                        </div>
                    </dl>
                </flux:card>
            </div>

            <div class="space-y-6 lg:sticky lg:top-6 lg:col-span-5">
                <flux:card class="overflow-hidden border-[#E2E8F0] shadow-lg dark:border-[#27364D]">
                    <div class="-mx-6 -mt-6 mb-6 h-1 bg-gradient-to-r from-[#2563EB] via-[#38BDF8] to-[#16A34A]"></div>
                    <div class="space-y-5">
                        <div class="flex flex-wrap items-center gap-2">
                            <flux:badge color="blue">{{ $product->category->name }}</flux:badge>
                            @if ($product->featured)
                                <flux:badge color="amber" icon="sparkles">Producto destacado</flux:badge>
                            @endif
                        </div>

                        <div>
                            <p class="text-sm font-medium uppercase tracking-wider text-[#2563EB]">TechStore · Catálogo</p>
                            <h1 class="mt-2 text-3xl font-semibold tracking-tight text-[#0F172A] dark:text-white sm:text-4xl">{{ $product->name }}</h1>
                        </div>

                        <div class="rounded-2xl bg-[#F8FAFC] p-5 dark:bg-[#0B1220]">
                            <p class="text-xs font-medium uppercase tracking-wide text-zinc-500 dark:text-zinc-400">Precio</p>
                            <p class="mt-1 text-4xl font-bold tracking-tight text-[#16A34A]">${{ number_format((float) $product->price, 2) }}</p>
                        </div>

                        <div class="flex items-center justify-between gap-4 rounded-xl border border-[#E2E8F0] px-4 py-3 dark:border-[#27364D]">
                            <div class="flex items-center gap-3">
                                <span class="flex size-10 items-center justify-center rounded-full {{ $product->stock > 0 ? 'bg-[#16A34A]/10 text-[#16A34A]' : 'bg-red-500/10 text-red-500' }}">
                                    <flux:icon :name="$product->stock > 0 ? 'check' : 'x-mark'" variant="micro" class="size-4" />
                                </span>
                                <div>
                                    <p class="text-sm font-semibold">{{ $product->stock > 0 ? 'Disponible para ti' : 'Producto agotado' }}</p>
                                    <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ number_format($product->stock) }} en existencia</p>
                                </div>
                            </div>
                            <flux:badge :color="$product->stock > 0 ? 'green' : 'red'">
                                {{ $product->stock > 0 ? 'En stock' : 'Sin stock' }}
                            </flux:badge>
                        </div>

                        <div class="space-y-3 border-t border-[#E2E8F0] pt-5 dark:border-[#27364D]">
                            <h2 class="text-sm font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">Descripción</h2>
                            <p class="whitespace-pre-line text-sm leading-7 text-zinc-600 dark:text-zinc-400">
                                {{ $product->description ?: 'No hay una descripción disponible para este producto.' }}
                            </p>
                        </div>

                        <div class="space-y-3 border-t border-[#E2E8F0] pt-5 dark:border-[#27364D]">
                            @auth
                                @if ($product->stock > 0)
                                    <form action="{{ route('store-cart.items.store', $product) }}" method="POST" class="space-y-3">
                                        @csrf
                                        <flux:input id="quantity" name="quantity" type="number" label="Cantidad" value="1" min="1" max="{{ $product->stock }}" required />
                                        @error('quantity')
                                            <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                        @enderror
                                        <flux:button type="submit" variant="primary" icon="shopping-cart" class="techstore-primary-button w-full">
                                            Agregar al carrito
                                        </flux:button>
                                    </form>
                                @else
                                    <flux:button type="button" variant="primary" icon="shopping-cart" class="w-full" disabled>
                                        Producto agotado
                                    </flux:button>
                                @endif
                            @else
                                <flux:button href="{{ route('login') }}" variant="primary" icon="shopping-cart" class="techstore-primary-button w-full">
                                    Inicia sesión para comprar
                                </flux:button>
                            @endauth
                        </div>
                    </div>
                </flux:card>

                <div class="flex items-center gap-3 rounded-2xl border border-[#E2E8F0] bg-white px-4 py-3 text-sm text-zinc-600 dark:border-[#27364D] dark:bg-[#111C31] dark:text-zinc-300">
                    <flux:icon name="shield-check" variant="outline" class="size-5 shrink-0 text-[#16A34A]" />
                    <span>Compra con confianza en TechStore.</span>
                </div>
            </div>
        </section>
    </div>
</x-layouts::app>
