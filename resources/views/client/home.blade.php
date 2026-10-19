<x-layouts::app title="Inicio | TechStore">
    <section class="relative isolate overflow-hidden rounded-3xl bg-gradient-to-br from-[#0F172A] via-[#1D4ED8] to-[#2563EB] text-white shadow-xl">
        <div aria-hidden="true" class="absolute -right-24 -top-32 -z-10 size-96 rounded-full border border-white/10"></div>
        <div aria-hidden="true" class="absolute -right-10 -top-16 -z-10 size-64 rounded-full border border-white/10"></div>
        <div class="grid items-center gap-8 px-6 py-8 sm:px-10 sm:py-10 lg:grid-cols-2 lg:gap-10 lg:px-12 lg:py-12">
            <div class="max-w-xl">
                <flux:badge color="blue" icon="sparkles" class="mb-4">Tecnología para todos los días</flux:badge>
                <h1 class="text-3xl font-semibold tracking-tight sm:text-4xl xl:text-5xl">Lo que necesitas para tu próximo gran proyecto.</h1>
                <p class="mt-4 max-w-lg text-base leading-7 text-blue-100 sm:text-lg">
                    Explora tecnología seleccionada, compara opciones y encuentra los detalles que necesitas para elegir con confianza.
                </p>
                <div class="mt-6 flex flex-wrap gap-3">
                    <flux:button href="{{ route('store') }}" variant="primary" icon="arrow-right" class="techstore-primary-button">Explorar catálogo</flux:button>
                    <flux:button href="{{ route('about') }}" variant="ghost">Conoce TechStore</flux:button>
                    @auth
                        <flux:button href="{{ route('products.index') }}" variant="ghost">Administrar productos</flux:button>
                        <flux:button href="{{ route('categories.index') }}" variant="ghost">Ver categorías</flux:button>
                    @endauth
                </div>
                @guest
                    <p class="mt-5 text-sm text-blue-100">
                        ¿Ya tienes una cuenta?
                        <a href="{{ route('login') }}" class="font-semibold text-white underline decoration-white/50 underline-offset-4 hover:decoration-white">Inicia sesión</a>
                    </p>
                @endguest
            </div>

            <div class="relative mx-auto w-full max-w-lg lg:ml-auto">
                @if ($featuredProducts->isNotEmpty())
                    @php($heroProduct = $featuredProducts->first())
                    <a href="{{ route('store.show', $heroProduct->slug) }}" class="group relative block overflow-hidden rounded-2xl border border-white/15 bg-[#0F172A]/40 p-3 shadow-2xl backdrop-blur-sm transition hover:border-white/30 sm:p-4">
                        <div class="relative aspect-[4/3] overflow-hidden rounded-xl bg-[#0F172A]/30">
                            <img src="{{ asset('storage/images/'.$heroProduct->image) }}" alt="{{ $heroProduct->name }}" fetchpriority="high" class="size-full object-cover transition duration-500 group-hover:scale-105">
                            <div class="absolute inset-0 bg-gradient-to-t from-[#0F172A]/80 via-transparent to-transparent"></div>
                            <div class="absolute inset-x-0 bottom-0 flex items-end justify-between gap-4 p-4 sm:p-6">
                                <div class="min-w-0">
                                    <flux:badge color="blue" icon="sparkles" class="mb-2">Producto destacado</flux:badge>
                                    <h2 class="truncate text-xl font-semibold text-white sm:text-2xl">{{ $heroProduct->name }}</h2>
                                    <p class="mt-1 text-sm text-blue-100">{{ $heroProduct->category->name }}</p>
                                </div>
                                <span class="shrink-0 rounded-xl bg-white px-3 py-2 text-sm font-semibold text-[#0F172A] sm:px-4 sm:text-base">
                                    ${{ number_format((float) $heroProduct->price, 2) }}
                                </span>
                            </div>
                        </div>
                    </a>
                @else
                    <div class="relative flex aspect-[4/3] items-center justify-center overflow-hidden rounded-2xl border border-white/15 bg-[#0F172A]/30 shadow-2xl backdrop-blur-sm">
                        <div aria-hidden="true" class="absolute size-64 rounded-full bg-[#2563EB]/30 blur-3xl"></div>
                        <div class="relative flex size-40 items-center justify-center rounded-[2rem] border border-white/20 bg-white/10 shadow-2xl backdrop-blur">
                            <flux:icon name="cpu-chip" variant="outline" class="size-20 text-white" />
                        </div>
                        <span class="absolute bottom-6 right-6 rounded-full border border-white/20 bg-[#0F172A]/40 px-4 py-2 text-xs font-medium tracking-wide text-blue-100">DESCUBRE TECHSTORE</span>
                    </div>
                @endif
            </div>
        </div>
    </section>

    <section aria-label="Ventajas de TechStore" class="grid gap-3 border-b border-[#E2E8F0] py-6 dark:border-[#27364D] sm:grid-cols-3 sm:py-7">
        <div class="flex items-center gap-3 px-2 sm:justify-center">
            <span class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-[#2563EB]/10 text-[#2563EB]">
                <flux:icon name="squares-2x2" variant="outline" class="size-5" />
            </span>
            <div>
                <h2 class="text-sm font-semibold">Productos organizados</h2>
                <p class="text-xs text-zinc-600 dark:text-zinc-400">Explora el catálogo por categoría.</p>
            </div>
        </div>
        <div class="flex items-center gap-3 px-2 sm:justify-center">
            <span class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-[#16A34A]/10 text-[#16A34A]">
                <flux:icon name="check-circle" variant="outline" class="size-5" />
            </span>
            <div>
                <h2 class="text-sm font-semibold">Disponibilidad clara</h2>
                <p class="text-xs text-zinc-600 dark:text-zinc-400">Consulta las existencias de cada producto.</p>
            </div>
        </div>
        <div class="flex items-center gap-3 px-2 sm:justify-center">
            <span class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-[#2563EB]/10 text-[#2563EB]">
                <flux:icon name="magnifying-glass" variant="outline" class="size-5" />
            </span>
            <div>
                <h2 class="text-sm font-semibold">Encuentra lo que buscas</h2>
                <p class="text-xs text-zinc-600 dark:text-zinc-400">Filtra productos en pocos pasos.</p>
            </div>
        </div>
    </section>

    @if ($featuredProducts->isNotEmpty())
        <section class="py-10 sm:py-12" aria-labelledby="featured-products-title" x-data="{
            autoplayTimer: null,
            scrollFeatured(direction) {
                const track = this.$refs.featuredTrack;
                const card = track.querySelector('[data-featured-card]');

                if (!card || track.scrollWidth <= track.clientWidth + 8) {
                    return;
                }

                const gap = parseFloat(getComputedStyle(track).columnGap) || 0;
                const step = card.getBoundingClientRect().width + gap;
                const atStart = track.scrollLeft <= 8;
                const atEnd = track.scrollLeft + track.clientWidth >= track.scrollWidth - 8;
                const behavior = window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 'auto' : 'smooth';

                if (direction > 0 && atEnd) {
                    track.scrollTo({ left: 0, behavior });
                } else if (direction < 0 && atStart) {
                    track.scrollTo({ left: track.scrollWidth, behavior });
                } else {
                    track.scrollBy({ left: direction * step, behavior });
                }
            },
            startFeaturedAutoplay() {
                if (this.autoplayTimer) {
                    return;
                }

                this.autoplayTimer = window.setInterval(() => this.scrollFeatured(1), 3000);
            },
            stopFeaturedAutoplay() {
                window.clearInterval(this.autoplayTimer);
                this.autoplayTimer = null;
            },
        }" x-init="startFeaturedAutoplay()" x-on:mouseenter="stopFeaturedAutoplay()" x-on:mouseleave="startFeaturedAutoplay()" x-on:focusin="stopFeaturedAutoplay()" x-on:focusout="if (!$el.contains($event.relatedTarget)) startFeaturedAutoplay()" x-on:touchstart="stopFeaturedAutoplay()" x-on:touchend="startFeaturedAutoplay()">
            <div class="mb-5 flex items-end justify-between gap-4 sm:mb-6">
                <div>
                    <flux:badge color="blue" icon="sparkles">Selección especial</flux:badge>
                    <h2 id="featured-products-title" class="mt-2 text-2xl font-semibold tracking-tight sm:text-3xl">Productos destacados</h2>
                    <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">Una selección para inspirar tu próxima elección.</p>
                </div>
                <div class="flex shrink-0 items-center gap-3">
                    <a href="{{ route('store') }}" class="hidden text-sm font-semibold text-[#2563EB] hover:text-[#1D4ED8] sm:inline-flex sm:items-center sm:gap-1">
                        Ver catálogo
                        <flux:icon name="arrow-right" variant="micro" class="size-4" />
                    </a>
                    <div class="hidden gap-2 md:flex">
                        <flux:button type="button" variant="ghost" icon="arrow-left" aria-label="Productos anteriores" x-on:click="scrollFeatured(-1)" />
                        <flux:button type="button" variant="ghost" icon="arrow-right" aria-label="Productos siguientes" x-on:click="scrollFeatured(1)" />
                    </div>
                </div>
            </div>

            <div x-ref="featuredTrack" class="flex snap-x snap-mandatory gap-5 overflow-x-auto [scrollbar-width:none] [&::-webkit-scrollbar]:hidden" aria-label="Carrusel de productos destacados">
                @foreach ($featuredProducts as $product)
                    <a href="{{ route('store.show', $product->slug) }}" data-featured-card class="group w-[92%] shrink-0 snap-start sm:w-[68%] xl:w-[38%]">
                        <flux:card class="h-full overflow-hidden p-0 transition duration-200 group-hover:-translate-y-1 group-hover:shadow-lg">
                            <div class="relative aspect-[4/3] overflow-hidden bg-[#0F172A]/[0.03] dark:bg-white/5">
                                <img src="{{ asset('storage/images/'.$product->image) }}" alt="{{ $product->name }}" loading="lazy" decoding="async" class="size-full object-cover transition duration-300 group-hover:scale-105">
                                <div class="absolute left-3 top-3">
                                    <flux:badge color="blue" icon="sparkles">Destacado</flux:badge>
                                </div>
                            </div>
                            <div class="space-y-3 p-5">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="min-w-0">
                                        <flux:badge color="blue" size="sm">{{ $product->category->name }}</flux:badge>
                                        <h3 class="mt-2 truncate text-lg font-semibold">{{ $product->name }}</h3>
                                    </div>
                                    <span class="shrink-0 font-semibold text-[#16A34A]">${{ number_format((float) $product->price, 2) }}</span>
                                </div>
                                <p class="line-clamp-2 min-h-10 text-sm leading-5 text-zinc-600 dark:text-zinc-400">
                                    {{ $product->description ?: 'Conoce todos los detalles de este producto.' }}
                                </p>
                                <span class="inline-flex items-center gap-2 text-sm font-medium text-[#2563EB]">
                                    Ver producto
                                    <flux:icon name="arrow-right" variant="micro" class="size-4 transition-transform group-hover:translate-x-1" />
                                </span>
                            </div>
                        </flux:card>
                    </a>
                @endforeach
            </div>
            <div class="mt-1 flex items-center justify-between md:hidden">
                <a href="{{ route('store') }}" class="inline-flex items-center gap-1 text-sm font-semibold text-[#2563EB] hover:text-[#1D4ED8]">
                    Ver catálogo
                    <flux:icon name="arrow-right" variant="micro" class="size-4" />
                </a>
                <div class="flex gap-2">
                    <flux:button type="button" variant="ghost" icon="arrow-left" aria-label="Productos anteriores" x-on:click="scrollFeatured(-1)" />
                    <flux:button type="button" variant="ghost" icon="arrow-right" aria-label="Productos siguientes" x-on:click="scrollFeatured(1)" />
                </div>
            </div>
        </section>
    @endif

    <section class="grid gap-4 py-8 sm:grid-cols-3 sm:gap-5 sm:py-10">
        <flux:card class="group transition duration-200 hover:-translate-y-1 hover:shadow-md">
            <span class="mb-5 flex size-11 items-center justify-center rounded-xl bg-[#2563EB]/10 text-[#2563EB] transition group-hover:bg-[#2563EB] group-hover:text-white">
                <flux:icon name="squares-2x2" variant="outline" class="size-5" />
            </span>
            <h2 class="text-lg font-semibold">Explora a tu ritmo</h2>
            <p class="mt-2 text-sm leading-6 text-zinc-600 dark:text-zinc-400">Encuentra productos organizados para ayudarte a descubrir nuevas opciones.</p>
        </flux:card>
        <flux:card class="group transition duration-200 hover:-translate-y-1 hover:shadow-md">
            <span class="mb-5 flex size-11 items-center justify-center rounded-xl bg-[#16A34A]/10 text-[#16A34A] transition group-hover:bg-[#16A34A] group-hover:text-white">
                <flux:icon name="clipboard-document-check" variant="outline" class="size-5" />
            </span>
            <h2 class="text-lg font-semibold">Decide con información</h2>
            <p class="mt-2 text-sm leading-6 text-zinc-600 dark:text-zinc-400">Revisa detalles, precios y disponibilidad antes de elegir.</p>
        </flux:card>
        <flux:card class="group transition duration-200 hover:-translate-y-1 hover:shadow-md">
            <span class="mb-5 flex size-11 items-center justify-center rounded-xl bg-[#2563EB]/10 text-[#2563EB] transition group-hover:bg-[#2563EB] group-hover:text-white">
                <flux:icon name="chat-bubble-left-right" variant="outline" class="size-5" />
            </span>
            <h2 class="text-lg font-semibold">Estamos para ayudarte</h2>
            <p class="mt-2 text-sm leading-6 text-zinc-600 dark:text-zinc-400">Conoce más sobre TechStore o ponte en contacto con nosotros.</p>
        </flux:card>
    </section>

    <section aria-labelledby="home-journey-title" class="overflow-hidden rounded-2xl border border-[#E2E8F0] bg-white dark:border-[#27364D] dark:bg-[#0B1220]">
        <div class="flex flex-col gap-5 p-6 sm:flex-row sm:items-center sm:justify-between sm:p-8">
            <div class="max-w-2xl">
                <flux:badge color="blue" class="mb-3">Conoce TechStore</flux:badge>
                <h2 id="home-journey-title" class="text-2xl font-semibold tracking-tight">Recorre la tienda y descubre lo nuevo.</h2>
                <p class="mt-2 text-sm leading-6 text-zinc-600 dark:text-zinc-400">Conoce nuestras páginas, explora las opciones del catálogo y encuentra accesos directos a cada sección.</p>
            </div>
            <flux:button href="{{ route('about') }}#recorrido" variant="primary" icon="arrow-right" class="techstore-primary-button shrink-0">Ver recorrido</flux:button>
        </div>
    </section>

    <section aria-labelledby="home-contact-title" class="overflow-hidden rounded-2xl border border-[#E2E8F0] bg-white dark:border-[#27364D] dark:bg-[#0B1220]">
        <div class="flex flex-col gap-6 p-6 sm:flex-row sm:items-center sm:justify-between sm:p-8">
            <div class="max-w-2xl">
                <flux:badge color="blue" class="mb-3">Sigue explorando</flux:badge>
                <h2 id="home-contact-title" class="text-2xl font-semibold tracking-tight">Tu próxima gran idea empieza aquí.</h2>
                <p class="mt-2 text-sm leading-6 text-zinc-600 dark:text-zinc-400">Explora el catálogo o conoce más sobre la experiencia TechStore.</p>
            </div>
            <div class="flex shrink-0 flex-wrap gap-3">
                <flux:button href="{{ route('store') }}" variant="primary" icon="arrow-right" class="techstore-primary-button">Ver catálogo</flux:button>
                <flux:button href="{{ route('contact') }}" variant="ghost">Contacto</flux:button>
            </div>
        </div>
    </section>
</x-layouts::app>
