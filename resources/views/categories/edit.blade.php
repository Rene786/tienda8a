<x-layouts::app>
    <flux:card class="mx-auto max-w-2xl">
        <div class="mb-6">
            <flux:badge color="blue">Administración</flux:badge>
            <h1 class="mt-3 text-2xl font-semibold">Editar categoría</h1>
            <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">Modifica los datos de {{ $category->name }}.</p>
        </div>

        <form action="{{ route('categories.update', $category->id) }}" method="POST" class="space-y-5">
            @csrf
            @method('PUT')
            <flux:input id="name" name="name" label="Nombre de la categoría" value="{{ old('name', $category->name) }}" required maxlength="100" />
            <flux:field label="Descripción">
                <textarea id="description" name="description" rows="4" class="w-full rounded-lg border border-zinc-200 bg-white px-3 py-2 text-sm text-zinc-700 shadow-xs dark:border-white/10 dark:bg-white/10 dark:text-zinc-300">{{ old('description', $category->description) }}</textarea>
                <flux:error name="description" />
            </flux:field>
            <flux:select id="status" name="status" label="Estado de la categoría" required>
                <option value="1" @selected(old('status', $category->status ? '1' : '0') == '1')>Activa</option>
                <option value="0" @selected(old('status', $category->status ? '1' : '0') == '0')>Inactiva</option>
            </flux:select>
            <div class="flex justify-end gap-2 border-t border-zinc-200 pt-5 dark:border-white/10">
                <flux:button href="{{ route('categories.index') }}" variant="ghost">Cancelar</flux:button>
                <flux:button type="submit" variant="primary">Actualizar categoría</flux:button>
            </div>
        </form>
    </flux:card>
</x-layouts::app>
