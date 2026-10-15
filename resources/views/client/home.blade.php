<x-layouts::app title="Inicio | TechStore">
    <flux:card class="overflow-hidden bg-gradient-to-br from-[#0F172A] via-[#1D4ED8] to-[#2563EB] px-6 py-16 text-center text-white shadow-xl sm:px-12 sm:py-20">
        <div class="mx-auto max-w-3xl">
            <flux:badge color="blue" class="mb-5">Tecnología para todos los días</flux:badge>
            <h1 class="text-4xl font-semibold tracking-tight sm:text-6xl">La tecnología que buscas, más cerca.</h1>
            <p class="mx-auto mt-5 max-w-2xl text-base leading-7 text-zinc-300 sm:text-lg">
                Descubre una forma sencilla de explorar tecnología, conocer opciones y elegir con confianza.
            </p>
            <div class="mt-9 flex flex-col justify-center gap-3 sm:flex-row">
                @auth
                    <flux:button href="{{ route('products.index') }}" variant="primary">Explorar productos</flux:button>
                    <flux:button href="{{ route('categories.index') }}" variant="ghost">Ver categorías</flux:button>
                @else
                    <flux:button href="{{ route('about') }}" variant="primary">Conoce TechStore</flux:button>
                    <flux:button href="{{ route('contact') }}" variant="ghost">Contáctanos</flux:button>
                    <flux:button href="{{ route('register') }}" variant="primary">Crear cuenta</flux:button>
                @endauth
            </div>
        </div>
    </flux:card>

    <section class="grid gap-5 py-10 sm:grid-cols-3">
        <flux:card>
            <flux:badge color="blue" class="mb-4">01</flux:badge>
            <h2 class="text-lg font-semibold">Selección sencilla</h2>
            <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">Explora productos organizados para encontrar lo que necesitas.</p>
        </flux:card>
        <flux:card>
            <flux:badge color="green" class="mb-4">02</flux:badge>
            <h2 class="text-lg font-semibold">Disponibilidad clara</h2>
            <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">Consulta el estado y las existencias de cada producto.</p>
        </flux:card>
        <flux:card>
            <flux:badge color="blue" class="mb-4">03</flux:badge>
            <h2 class="text-lg font-semibold">Gestión práctica</h2>
            <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">Administra productos y categorías desde un solo lugar.</p>
        </flux:card>
    </section>

    <flux:card class="flex flex-col items-start justify-between gap-5 bg-white dark:bg-[#0B1220] sm:flex-row sm:items-center">
        <div>
            <flux:badge color="blue" class="mb-3">TechStore</flux:badge>
            <h2 class="text-xl font-semibold">¿Quieres saber más o necesitas ayuda?</h2>
            <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">Conoce nuestra propuesta o visita la página de contacto.</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <flux:button href="{{ route('about') }}" variant="ghost">Acerca de nosotros</flux:button>
            <flux:button href="{{ route('contact') }}" variant="primary">Contacto</flux:button>
        </div>
    </flux:card>
</x-layouts::app>
