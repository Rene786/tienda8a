<x-layouts::app>
    <flux:card class="mx-auto max-w-2xl">
        <div class="mb-6">
            <flux:badge color="blue">Administración</flux:badge>
            <h1 class="mt-3 text-2xl font-semibold">Editar pedido</h1>
            <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">Actualiza los datos del pedido {{ $order->order_number }}.</p>
        </div>
        <form action="{{ route('orders.update', $order) }}" method="POST" class="space-y-5">
            @csrf
            @method('PUT')
            <flux:select id="user_id" name="user_id" label="Cliente" required>
                @foreach ($users as $user)
                    <option value="{{ $user->id }}" @selected(old('user_id', $order->user_id) == $user->id)>{{ $user->name }} — {{ $user->email }}{{ $user->trashed() ? ' (eliminado)' : '' }}</option>
                @endforeach
            </flux:select>
            <div class="grid gap-5 sm:grid-cols-2">
                <flux:input id="order_number" name="order_number" label="Número de pedido" value="{{ old('order_number', $order->order_number) }}" maxlength="50" required />
                <flux:input id="total" name="total" type="number" label="Total" min="0" max="99999999.99" step="0.01" value="{{ old('total', $order->total) }}" required />
            </div>
            <div class="grid gap-5 sm:grid-cols-2">
                <flux:select id="status" name="status" label="Estado del pedido" required>
                    @foreach ($orderStatuses as $status)
                        <option value="{{ $status }}" @selected(old('status', $order->status) === $status)>{{ ucfirst($status) }}</option>
                    @endforeach
                </flux:select>
                <flux:select id="payment_status" name="payment_status" label="Estado del pago" required>
                    @foreach ($paymentStatuses as $status)
                        <option value="{{ $status }}" @selected(old('payment_status', $order->payment_status) === $status)>{{ ucfirst($status) }}</option>
                    @endforeach
                </flux:select>
            </div>
            <flux:field label="Dirección de envío">
                <textarea id="shipping_address" name="shipping_address" rows="3" class="w-full rounded-lg border border-zinc-200 bg-white px-3 py-2 text-sm text-zinc-700 shadow-xs dark:border-white/10 dark:bg-white/10 dark:text-zinc-300">{{ old('shipping_address', $order->shipping_address) }}</textarea>
                <flux:error name="shipping_address" />
            </flux:field>
            <div class="flex justify-end gap-2 border-t border-zinc-200 pt-5 dark:border-white/10">
                <flux:button href="{{ route('orders.index') }}" variant="ghost">Cancelar</flux:button>
                <flux:button type="submit" variant="primary">Guardar cambios</flux:button>
            </div>
        </form>
    </flux:card>
</x-layouts::app>
