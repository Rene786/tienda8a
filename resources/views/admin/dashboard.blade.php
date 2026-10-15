<x-layouts::app>
    <section class="mx-auto max-w-6xl">
        <div class="mb-8 flex flex-wrap items-end justify-between gap-4">
            <div class="max-w-2xl">
                <flux:badge color="blue">Panel de administración</flux:badge>
                <h1 class="mt-3 text-3xl font-semibold tracking-tight text-[#0F172A] dark:text-white">Resumen de TechStore</h1>
                <p class="mt-2 text-sm leading-6 text-[#1E293B]/75 dark:text-zinc-400">Consulta las ventas y el estado general de tu tienda.</p>
            </div>
        </div>

        <div class="mb-8 grid gap-5 sm:grid-cols-2 xl:grid-cols-3">
            <flux:card class="border-[#E2E8F0] border-t-2 border-t-[#2563EB] bg-white shadow-sm dark:border-[#27364D] dark:bg-[#111C31]">
                <flux:text class="text-sm text-zinc-500 dark:text-zinc-400">Ventas de hoy</flux:text>
                <flux:heading size="xl" class="mt-2 text-[#2563EB]">${{ number_format($dailySales, 2) }}</flux:heading>
                <flux:text class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">Pedidos pagados hoy</flux:text>
                <flux:button href="{{ route('reports.dashboard', 'daily-sales') }}" variant="ghost" size="sm" icon="document-arrow-down" class="mt-3">Descargar PDF</flux:button>
            </flux:card>
            <flux:card class="border-[#E2E8F0] border-t-2 border-t-[#16A34A] bg-white shadow-sm dark:border-[#27364D] dark:bg-[#111C31]">
                <flux:text class="text-sm text-zinc-500 dark:text-zinc-400">Ventas del mes</flux:text>
                <flux:heading size="xl" class="mt-2 text-[#16A34A]">${{ number_format($monthlySales, 2) }}</flux:heading>
                <flux:text class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">Pedidos pagados este mes</flux:text>
                <flux:button href="{{ route('reports.dashboard', 'monthly-sales') }}" variant="ghost" size="sm" icon="document-arrow-down" class="mt-3">Descargar PDF</flux:button>
            </flux:card>
            <flux:card class="border-[#E2E8F0] border-t-2 border-t-[#7C3AED] bg-white shadow-sm dark:border-[#27364D] dark:bg-[#111C31]">
                <flux:text class="text-sm text-zinc-500 dark:text-zinc-400">Productos registrados</flux:text>
                <flux:heading size="xl" class="mt-2 text-[#7C3AED]">{{ number_format($registeredProducts) }}</flux:heading>
                <flux:text class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">Productos vigentes en el catálogo</flux:text>
                <flux:button href="{{ route('reports.dashboard', 'products') }}" variant="ghost" size="sm" icon="document-arrow-down" class="mt-3">Descargar PDF</flux:button>
            </flux:card>
            <flux:card class="border-[#E2E8F0] border-t-2 border-t-[#0891B2] bg-white shadow-sm dark:border-[#27364D] dark:bg-[#111C31]">
                <flux:text class="text-sm text-zinc-500 dark:text-zinc-400">Usuarios registrados</flux:text>
                <flux:heading size="xl" class="mt-2 text-[#0891B2]">{{ number_format($registeredUsers) }}</flux:heading>
                <flux:text class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">Cuentas registradas vigentes</flux:text>
                <flux:button href="{{ route('reports.dashboard', 'users') }}" variant="ghost" size="sm" icon="document-arrow-down" class="mt-3">Descargar PDF</flux:button>
            </flux:card>
            <flux:card class="border-[#E2E8F0] border-t-2 border-t-[#D97706] bg-white shadow-sm dark:border-[#27364D] dark:bg-[#111C31]">
                <flux:text class="text-sm text-zinc-500 dark:text-zinc-400">Valor del inventario</flux:text>
                <flux:heading size="xl" class="mt-2 text-[#D97706]">${{ number_format($inventoryValue, 2) }}</flux:heading>
                <flux:text class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">Precio por existencias disponibles</flux:text>
                <flux:button href="{{ route('reports.dashboard', 'inventory') }}" variant="ghost" size="sm" icon="document-arrow-down" class="mt-3">Descargar PDF</flux:button>
            </flux:card>
        </div>

        {{-- <section class="grid gap-5 sm:grid-cols-2">
            <a href="{{ route('products.index') }}" class="group block">
                <flux:card class="h-full transition hover:border-blue-400 hover:shadow-md">
                    <flux:badge color="blue">Catálogo</flux:badge>
                    <h2 class="mt-3 text-xl font-semibold">Administrar productos</h2>
                    <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">Crea, edita y consulta los productos de la tienda.</p>
                    <p class="mt-5 text-sm font-medium text-blue-600 group-hover:text-blue-700">Ver productos →</p>
                </flux:card>
            </a>
            <a href="{{ route('categories.index') }}" class="group block">
                <flux:card class="h-full transition hover:border-blue-400 hover:shadow-md">
                    <flux:badge color="green">Organización</flux:badge>
                    <h2 class="mt-3 text-xl font-semibold">Administrar categorías</h2>
                    <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">Organiza los productos en categorías claras.</p>
                    <p class="mt-5 text-sm font-medium text-blue-600 group-hover:text-blue-700">Ver categorías →</p>
                </flux:card>
            </a>
        </section> --}}
    </section>
</x-layouts::app>
