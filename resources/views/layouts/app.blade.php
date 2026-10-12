<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#0F172A">
    <title>{{ $title ?? 'TechStore' }}</title>

    @fonts
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @fluxAppearance
    @livewireStyles
</head>
<body class="flex min-h-screen flex-col bg-[#F8FAFC] text-[#1E293B] antialiased dark:bg-zinc-900 dark:text-white">
    <header class="border-b border-white/10 bg-[#0F172A] text-white">
        <div class="mx-auto w-full max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-white/10 py-3">
                <a href="{{ auth()->check() ? route('home') : url('/') }}" class="text-lg font-semibold tracking-tight text-white">
                    TechStore
                </a>
                @auth
                    <form method="POST" action="/logout">
                        @csrf
                        <flux:button type="submit" variant="ghost" size="sm">Salir</flux:button>
                    </form>
                @else
                    <div class="flex items-center gap-2">
                        <flux:button href="{{ route('login') }}" variant="ghost" size="sm">Iniciar sesión</flux:button>
                        <flux:button href="{{ route('register') }}" variant="primary" size="sm">Crear cuenta</flux:button>
                    </div>
                @endauth
            </div>

            @auth
                <flux:navbar scrollable class="-my-px">
                    <flux:navbar.item href="{{ route('home') }}">Inicio</flux:navbar.item>
                    <flux:navbar.item href="{{ route('products.index') }}">Productos</flux:navbar.item>
                    <flux:navbar.item href="{{ route('product-files.index') }}">Archivos</flux:navbar.item>
                    <flux:navbar.item href="{{ route('categories.index') }}">Categorías</flux:navbar.item>
                    <flux:navbar.item href="{{ route('carts.index') }}">Carritos</flux:navbar.item>
                    <flux:navbar.item href="{{ route('cart-items.index') }}">Artículos</flux:navbar.item>
                    <flux:navbar.item href="{{ route('orders.index') }}">Pedidos</flux:navbar.item>
                    <flux:navbar.item href="{{ route('order-items.index') }}">Artículos de pedido</flux:navbar.item>
                    <flux:navbar.item href="{{ route('users.index') }}">Usuarios</flux:navbar.item>
                </flux:navbar>
            @endauth
        </div>
    </header>

    <main class="mx-auto w-full max-w-7xl flex-1 px-4 py-8 sm:px-6 sm:py-10 lg:px-8">
        {{ $slot }}
    </main>

    <footer class="border-t border-[#E2E8F0] bg-[#F8FAFC] dark:border-[#27364D] dark:bg-[#0B1220]">
        <div class="mx-auto max-w-7xl px-5 py-5 text-sm text-[#1E293B]/70 dark:text-zinc-300 sm:px-8">
            © {{ date('Y') }} TechStore
        </div>
    </footer>

    @livewireScripts
    @fluxScripts
</body>
</html>
