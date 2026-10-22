@props(['routeName', 'options'])

<flux:card class="mb-4 border-[#E2E8F0] bg-white shadow-sm dark:border-[#27364D] dark:bg-[#111C31]">
    <form action="{{ route($routeName) }}" method="GET" class="flex flex-wrap items-end gap-3">
        <flux:field class="min-w-56">
            <flux:label for="record-status">Estado</flux:label>
            <flux:select id="record-status" name="status">
                @foreach ($options as $value => $label)
                    <option value="{{ $value }}" @selected(request('status', 'all') === $value)>{{ $label }}</option>
                @endforeach
            </flux:select>
        </flux:field>
        <flux:button type="submit" variant="primary" class="techstore-primary-button">Filtrar</flux:button>
        @if (request('status', 'all') !== 'all')
            <flux:button href="{{ route($routeName) }}" variant="ghost">Limpiar</flux:button>
        @endif
    </form>
</flux:card>
