<x-layouts::app>
    <flux:card class="mx-auto max-w-2xl">
        <div class="mb-6">
            <flux:badge color="blue">Administración</flux:badge>
            <h1 class="mt-3 text-2xl font-semibold">Editar archivo</h1>
            <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">Actualiza los datos del archivo asociado al producto.</p>
        </div>
        <form action="{{ route('product-files.update', $productFile) }}" method="POST" class="space-y-5">
            @csrf
            @method('PUT')
            <flux:select id="product_id" name="product_id" label="Producto" required>
                @foreach ($products as $product)
                    <option value="{{ $product->id }}" @selected(old('product_id', $productFile->product_id) == $product->id)>{{ $product->name }}{{ $product->trashed() ? ' (eliminado)' : '' }}</option>
                @endforeach
            </flux:select>
            <flux:input id="name" name="name" label="Nombre del archivo" value="{{ old('name', $productFile->name) }}" maxlength="150" required />
            <flux:input id="file_path" name="file_path" label="Ruta o URL del archivo" value="{{ old('file_path', $productFile->file_path) }}" maxlength="255" required />
            <flux:input id="file_type" name="file_type" label="Tipo de archivo" value="{{ old('file_type', $productFile->file_type) }}" maxlength="50" />
            <div class="flex justify-end gap-2 border-t border-zinc-200 pt-5 dark:border-white/10">
                <flux:button href="{{ route('product-files.index') }}" variant="ghost">Cancelar</flux:button>
                <flux:button type="submit" variant="primary">Guardar cambios</flux:button>
            </div>
        </form>
    </flux:card>
</x-layouts::app>
