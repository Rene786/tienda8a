<x-layouts::app>
    <flux:card class="overflow-hidden bg-gradient-to-br from-[#0F172A] via-[#1D4ED8] to-[#2563EB] px-6 py-16 text-center text-white shadow-xl sm:px-12 sm:py-20">
        <div class="mx-auto max-w-3xl">
            <flux:badge color="blue" class="mb-5">Tecnología para todos los días</flux:badge>
            <h1 class="text-4xl font-semibold tracking-tight sm:text-6xl">Tu próxima mejora empieza aquí.</h1>
            <p class="mx-auto mt-5 max-w-2xl text-base leading-7 text-zinc-300 sm:text-lg">
                Encuentra tecnología confiable, precios claros y una experiencia de compra sencilla.
            </p>
            <div class="mt-9 flex flex-col justify-center gap-3 sm:flex-row">
                @auth
                    <flux:button href="{{ route('products.index') }}" variant="primary">Explorar productos</flux:button>
                    <flux:button href="{{ route('categories.index') }}" variant="ghost">Administrar categorías</flux:button>
                @else
                    <flux:button href="{{ route('register') }}" variant="primary">Crear cuenta</flux:button>
                    <flux:button href="{{ route('login') }}" variant="ghost">Iniciar sesión</flux:button>
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
</x-layouts::app>
