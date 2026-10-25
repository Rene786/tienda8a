<x-layouts::app>
    <section class="mx-auto max-w-6xl">
        <div class="mb-6">
            <flux:badge color="blue">{{ $user->isAdmin() ? 'Administrador' : 'Cliente' }}</flux:badge>
            <h1 class="mt-3 text-2xl font-semibold">Mi perfil</h1>
            <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">Consulta y actualiza los datos de tu cuenta.</p>
        </div>

        @if (session('success'))
            <flux:callout variant="success" class="mb-6">{{ session('success') }}</flux:callout>
        @endif

        <div class="grid gap-6 lg:grid-cols-3">
            <flux:card class="h-fit">
                <flux:heading size="lg">Datos de la cuenta</flux:heading>
                <flux:text class="mt-1">Rol: {{ $user->isAdmin() ? 'Administrador' : 'Cliente' }}</flux:text>
                <form action="{{ route('profile.update') }}" method="POST" class="mt-5 space-y-5">
                    @csrf
                    @method('PATCH')
                    <div>
                        <flux:input id="name" name="name" label="Nombre completo" value="{{ old('name', $user->name) }}" required maxlength="255" autocomplete="name" />
                        @error('name', 'updateProfileInformation')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <flux:input id="email" type="email" name="email" label="Correo electrónico" value="{{ old('email', $user->email) }}" required maxlength="255" autocomplete="email" />
                        @error('email', 'updateProfileInformation')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="flex justify-end border-t border-zinc-200 pt-5 dark:border-white/10">
                        <flux:button type="submit" variant="primary">Guardar cambios</flux:button>
                    </div>
                </form>
            </flux:card>

            <div class="lg:col-span-2">
                <div class="mb-4">
                    <h2 class="text-xl font-semibold">Mis pedidos</h2>
                    <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">Historial de pedidos asociados a tu cuenta.</p>
                </div>

                <div class="overflow-x-auto rounded-xl border border-zinc-200 bg-white dark:border-white/10 dark:bg-zinc-900">
                    <flux:table>
                        <flux:table.columns>
                            <flux:table.column>Pedido</flux:table.column>
                            <flux:table.column>Fecha</flux:table.column>
                            <flux:table.column>Total</flux:table.column>
                            <flux:table.column>Estado</flux:table.column>
                            <flux:table.column>Pago</flux:table.column>
                            <flux:table.column>Artículos</flux:table.column>
                        </flux:table.columns>
                        <flux:table.rows>
                            @forelse ($orders as $order)
                                <flux:table.row>
                                    <flux:table.cell variant="strong">{{ $order->order_number }}</flux:table.cell>
                                    <flux:table.cell>{{ $order->created_at?->format('d/m/Y') }}</flux:table.cell>
                                    <flux:table.cell>${{ number_format((float) $order->total, 2) }}</flux:table.cell>
                                    <flux:table.cell><flux:badge color="blue">{{ ucfirst($order->status) }}</flux:badge></flux:table.cell>
                                    <flux:table.cell>{{ ucfirst($order->payment_status) }}</flux:table.cell>
                                    <flux:table.cell>{{ $order->items_count }}</flux:table.cell>
                                </flux:table.row>
                            @empty
                                <flux:table.row>
                                    <flux:table.cell colspan="6" class="text-center">Aún no tienes pedidos.</flux:table.cell>
                                </flux:table.row>
                            @endforelse
                        </flux:table.rows>
                    </flux:table>
                </div>

                @if ($orders->hasPages())
                    <div class="mt-6">{{ $orders->links() }}</div>
                @endif
            </div>
        </div>
    </section>
</x-layouts::app>
