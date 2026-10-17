<x-layouts::app title="Acerca de TechStore">
    <div class="space-y-10">
        <flux:card class="overflow-hidden bg-gradient-to-br from-[#0F172A] via-[#1D4ED8] to-[#2563EB] px-6 py-16 text-center text-white shadow-xl sm:px-12 sm:py-20">
            <div class="mx-auto max-w-3xl">
                <flux:badge color="blue" class="mb-5">Acerca de nosotros</flux:badge>
                <h1 class="text-4xl font-semibold tracking-tight sm:text-6xl">La tecnología, más fácil para todos.</h1>
                <p class="mx-auto mt-5 max-w-2xl text-base leading-7 text-zinc-300 sm:text-lg">
                    En TechStore queremos que encontrar la tecnología que necesitas sea una experiencia clara, práctica y confiable.
                </p>
            </div>
        </flux:card>

        <section class="grid items-stretch gap-6 lg:grid-cols-2">
            <div class="flex flex-col justify-center py-4">
                <flux:badge color="blue" class="mb-4 w-fit">Nuestra idea</flux:badge>
                <h2 class="text-2xl font-semibold tracking-tight sm:text-3xl">Una tienda pensada para que elijas con confianza.</h2>
                <p class="mt-4 leading-7 text-zinc-600 dark:text-zinc-400">
                    Reunimos productos y categorías en un solo lugar para que puedas explorar opciones, conocer su disponibilidad y tomar una decisión con información clara.
                </p>
                <p class="mt-3 leading-7 text-zinc-600 dark:text-zinc-400">
                    Trabajamos para que cada paso, desde descubrir un producto hasta gestionar tu cuenta, resulte sencillo.
                </p>
            </div>

            <flux:card class="flex flex-col justify-center bg-white p-7 dark:bg-[#0B1220] sm:p-9">
                <h2 class="text-xl font-semibold">Lo que nos importa</h2>
                <ul class="mt-6 space-y-5">
                    <li class="flex gap-4">
                        <flux:badge color="blue">01</flux:badge>
                        <div>
                            <h3 class="font-medium">Claridad</h3>
                            <p class="mt-1 text-sm leading-6 text-zinc-600 dark:text-zinc-400">Información sencilla para comparar y elegir.</p>
                        </div>
                    </li>
                    <li class="flex gap-4">
                        <flux:badge color="green">02</flux:badge>
                        <div>
                            <h3 class="font-medium">Confianza</h3>
                            <p class="mt-1 text-sm leading-6 text-zinc-600 dark:text-zinc-400">Una experiencia transparente en cada visita.</p>
                        </div>
                    </li>
                    <li class="flex gap-4">
                        <flux:badge color="blue">03</flux:badge>
                        <div>
                            <h3 class="font-medium">Sencillez</h3>
                            <p class="mt-1 text-sm leading-6 text-zinc-600 dark:text-zinc-400">Herramientas prácticas para encontrar lo que buscas.</p>
                        </div>
                    </li>
                </ul>
            </flux:card>
        </section>

        <section class="grid gap-5 sm:grid-cols-3">
            <flux:card>
                <flux:badge color="blue" class="mb-4">Descubre</flux:badge>
                <h2 class="text-lg font-semibold">Explora a tu ritmo</h2>
                <p class="mt-2 text-sm leading-6 text-zinc-600 dark:text-zinc-400">Encuentra productos organizados por categorías.</p>
            </flux:card>
            <flux:card>
                <flux:badge color="green" class="mb-4">Elige</flux:badge>
                <h2 class="text-lg font-semibold">Decide con claridad</h2>
                <p class="mt-2 text-sm leading-6 text-zinc-600 dark:text-zinc-400">Consulta la información y disponibilidad de cada producto.</p>
            </flux:card>
            <flux:card>
                <flux:badge color="blue" class="mb-4">Cuenta con nosotros</flux:badge>
                <h2 class="text-lg font-semibold">Estamos para orientarte</h2>
                <p class="mt-2 text-sm leading-6 text-zinc-600 dark:text-zinc-400">Visita nuestra página de contacto si necesitas ayuda.</p>
            </flux:card>
        </section>

        <section id="recorrido" aria-labelledby="sitemap-title">
            <flux:card class="overflow-hidden">
                <div class="mx-auto max-w-3xl text-center">
                    <flux:badge color="blue" class="mb-4">Recorrido y accesos directos</flux:badge>
                    <h2 id="sitemap-title" class="text-2xl font-semibold tracking-tight sm:text-3xl">Un recorrido claro por TechStore</h2>
                    <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">Accede directamente al catálogo, sus nuevas opciones y las herramientas de gestión.</p>
                </div>

                <div aria-label="Mapa de sitio de TechStore" class="mx-auto mt-8 max-w-5xl">
                    <div class="flex justify-center">
                        <a href="{{ url('/') }}" class="rounded-xl border border-blue-200 bg-blue-50 px-6 py-3 text-center font-semibold text-blue-900 transition hover:border-blue-400 dark:border-blue-900 dark:bg-blue-950 dark:text-blue-100">
                            Inicio
                        </a>
                    </div>

                    <div class="mx-auto my-4 h-6 w-px bg-blue-200 dark:bg-blue-800"></div>

                    <section aria-labelledby="public-pages-title" class="rounded-2xl border border-zinc-200 bg-zinc-50 p-5 dark:border-zinc-700 dark:bg-zinc-800/50">
                        <h3 id="public-pages-title" class="mb-4 text-center text-sm font-semibold uppercase tracking-wide text-zinc-600 dark:text-zinc-300">Páginas públicas</h3>
                        <ul class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                            <li>
                                <a href="{{ route('about') }}" class="block rounded-xl border border-zinc-200 bg-white px-4 py-3 text-center font-medium transition hover:border-blue-400 hover:text-blue-700 dark:border-zinc-700 dark:bg-zinc-900 dark:hover:text-blue-300">
                                    Acerca de nosotros
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('store') }}" class="block rounded-xl border border-zinc-200 bg-white px-4 py-3 text-center font-medium transition hover:border-blue-400 hover:text-blue-700 dark:border-zinc-700 dark:bg-zinc-900 dark:hover:text-blue-300">
                                    Catálogo y detalles de productos
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('store', ['sort' => 'featured']) }}" class="block rounded-xl border border-zinc-200 bg-white px-4 py-3 text-center font-medium transition hover:border-blue-400 hover:text-blue-700 dark:border-zinc-700 dark:bg-zinc-900 dark:hover:text-blue-300">
                                    Catálogo: productos destacados
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('store', ['sort' => 'name_asc']) }}" class="block rounded-xl border border-zinc-200 bg-white px-4 py-3 text-center font-medium transition hover:border-blue-400 hover:text-blue-700 dark:border-zinc-700 dark:bg-zinc-900 dark:hover:text-blue-300">
                                    Catálogo: orden A–Z
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('store', ['sort' => 'name_desc']) }}" class="block rounded-xl border border-zinc-200 bg-white px-4 py-3 text-center font-medium transition hover:border-blue-400 hover:text-blue-700 dark:border-zinc-700 dark:bg-zinc-900 dark:hover:text-blue-300">
                                    Catálogo: orden Z–A
                                </a>
                            </li>
                            <li>
                                <a href="{{ url('/') }}#featured-products-title" class="block rounded-xl border border-zinc-200 bg-white px-4 py-3 text-center font-medium transition hover:border-blue-400 hover:text-blue-700 dark:border-zinc-700 dark:bg-zinc-900 dark:hover:text-blue-300">
                                    Carrusel de destacados
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('contact') }}" class="block rounded-xl border border-zinc-200 bg-white px-4 py-3 text-center font-medium transition hover:border-blue-400 hover:text-blue-700 dark:border-zinc-700 dark:bg-zinc-900 dark:hover:text-blue-300">
                                    Contacto
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('login') }}" class="block rounded-xl border border-zinc-200 bg-white px-4 py-3 text-center font-medium transition hover:border-blue-400 hover:text-blue-700 dark:border-zinc-700 dark:bg-zinc-900 dark:hover:text-blue-300">
                                    Iniciar sesión
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('register') }}" class="block rounded-xl border border-zinc-200 bg-white px-4 py-3 text-center font-medium transition hover:border-blue-400 hover:text-blue-700 dark:border-zinc-700 dark:bg-zinc-900 dark:hover:text-blue-300">
                                    Crear cuenta
                                </a>
                            </li>
                        </ul>
                    </section>

                    <div class="mx-auto my-4 h-6 w-px bg-blue-200 dark:bg-blue-800"></div>

                    <section aria-labelledby="management-pages-title" class="rounded-2xl border border-blue-200 bg-blue-50/70 p-5 dark:border-blue-900 dark:bg-blue-950/40">
                        <div class="mb-4 text-center">
                            <h3 id="management-pages-title" class="font-semibold text-blue-950 dark:text-blue-100">Área de gestión</h3>
                        </div>
                        <ul class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                            <li>
                                <div class="block rounded-xl border border-blue-200 bg-white px-4 py-3 text-center font-medium dark:border-blue-900 dark:bg-zinc-900">
                                    Panel principal
                                </div>
                            </li>
                            <li>
                                <div class="block rounded-xl border border-blue-200 bg-white px-4 py-3 text-center font-medium dark:border-blue-900 dark:bg-zinc-900">
                                    Productos
                                </div>
                            </li>
                            <li>
                                <div class="block rounded-xl border border-blue-200 bg-white px-4 py-3 text-center font-medium dark:border-blue-900 dark:bg-zinc-900">
                                    Archivos de productos
                                </div>
                            </li>
                            <li>
                                <div class="block rounded-xl border border-blue-200 bg-white px-4 py-3 text-center font-medium dark:border-blue-900 dark:bg-zinc-900">
                                    Categorías
                                </div>
                            </li>
                            <li>
                                <div class="block rounded-xl border border-blue-200 bg-white px-4 py-3 text-center font-medium dark:border-blue-900 dark:bg-zinc-900">
                                    Carritos
                                </div>
                            </li>
                            <li>
                                <div class="block rounded-xl border border-blue-200 bg-white px-4 py-3 text-center font-medium dark:border-blue-900 dark:bg-zinc-900">
                                    Artículos del carrito
                                </div>
                            </li>
                            <li>
                                <div class="block rounded-xl border border-blue-200 bg-white px-4 py-3 text-center font-medium dark:border-blue-900 dark:bg-zinc-900">
                                    Pedidos
                                </div>
                            </li>
                            <li>
                                <div class="block rounded-xl border border-blue-200 bg-white px-4 py-3 text-center font-medium dark:border-blue-900 dark:bg-zinc-900">
                                    Artículos de pedido
                                </div>
                            </li>
                            <li>
                                <div class="block rounded-xl border border-blue-200 bg-white px-4 py-3 text-center font-medium dark:border-blue-900 dark:bg-zinc-900">
                                    Usuarios
                                </div>
                            </li>
                            <li>
                                <div class="block rounded-xl border border-blue-200 bg-white px-4 py-3 text-center font-medium dark:border-blue-900 dark:bg-zinc-900">
                                    Historial de descargas PDF
                                </div>
                            </li>
                        </ul>
                    </section>
                </div>
            </flux:card>
        </section>
    </div>
</x-layouts::app>
