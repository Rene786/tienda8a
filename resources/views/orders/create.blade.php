<x-layouts::app>
    <flux:card class="mx-auto max-w-2xl">
        <div class="mb-6">
            <flux:badge color="blue">Administración</flux:badge>
            <h1 class="mt-3 text-2xl font-semibold">Registrar pedido</h1>
            <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">Completa la información del pedido.</p>
        </div>
        <form action="{{ route('orders.store') }}" method="POST" class="space-y-5">
            @csrf
            <flux:select id="user_id" name="user_id" label="Cliente" placeholder="Selecciona un cliente" required>
                @foreach ($users as $user)
                    <option value="{{ $user->id }}" @selected(old('user_id') == $user->id)>{{ $user->name }} — {{ $user->email }}</option>
                @endforeach
            </flux:select>
            <div class="grid gap-5 sm:grid-cols-2">
                <flux:input id="order_number" name="order_number" label="Número de pedido" value="{{ old('order_number') }}" maxlength="50" required placeholder="ORD-0001" />
                <flux:input id="total" name="total" type="number" label="Total" min="0" max="99999999.99" step="0.01" value="{{ old('total', '0.00') }}" required />
            </div>
            <div class="grid gap-5 sm:grid-cols-2">
                <flux:select id="status" name="status" label="Estado del pedido" required>
                    @foreach ($orderStatuses as $status)
                        <option value="{{ $status }}" @selected(old('status', 'pending') === $status)>{{ ucfirst($status) }}</option>
                    @endforeach
                </flux:select>
                <flux:select id="payment_status" name="payment_status" label="Estado del pago" required>
                    @foreach ($paymentStatuses as $status)
                        <option value="{{ $status }}" @selected(old('payment_status', 'pending') === $status)>{{ ucfirst($status) }}</option>
                    @endforeach
                </flux:select>
            </div>
            <flux:field label="Dirección de envío">
                <textarea id="shipping_address" name="shipping_address" rows="3" class="w-full rounded-lg border border-zinc-200 bg-white px-3 py-2 text-sm text-zinc-700 shadow-xs dark:border-white/10 dark:bg-white/10 dark:text-zinc-300">{{ old('shipping_address') }}</textarea>
                <flux:error name="shipping_address" />
            </flux:field>
            @if ($users->isEmpty())
                <flux:callout variant="warning">No hay clientes activos para asignar al pedido.</flux:callout>
            @endif
            <div class="flex justify-end gap-2 border-t border-zinc-200 pt-5 dark:border-white/10">
                <flux:button href="{{ route('orders.index') }}" variant="ghost">Cancelar</flux:button>
                <flux:button type="submit" variant="primary" :disabled="$users->isEmpty()">Guardar pedido</flux:button>
            </div>
        </form>
    </flux:card>
</x-layouts::app>
