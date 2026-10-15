<x-layouts::app>
    <flux:card class="mx-auto max-w-2xl">
        <div class="mb-6">
            <flux:badge color="blue">Administración</flux:badge>
            <h1 class="mt-3 text-2xl font-semibold">Agregar archivo de producto</h1>
            <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">Registra la ruta pública o URL del documento.</p>
        </div>
        <form action="{{ route('product-files.store') }}" method="POST" class="space-y-5">
            @csrf
            <flux:select id="product_id" name="product_id" label="Producto" placeholder="Selecciona un producto" required>
                @foreach ($products as $product)
                    <option value="{{ $product->id }}" @selected(old('product_id') == $product->id)>{{ $product->name }}</option>
                @endforeach
            </flux:select>
            <flux:input id="name" name="name" label="Nombre del archivo" value="{{ old('name') }}" maxlength="150" required placeholder="Manual de usuario" />
            <flux:input id="file_path" name="file_path" label="Ruta o URL del archivo" value="{{ old('file_path') }}" maxlength="255" required placeholder="archivos/manual.pdf o https://..." description="Para archivos locales, guarda el archivo dentro de storage/app/public y escribe su ruta relativa." />
            <flux:input id="file_type" name="file_type" label="Tipo de archivo" value="{{ old('file_type') }}" maxlength="50" placeholder="PDF, ZIP, manual" />
            @if ($products->isEmpty())
                <flux:callout variant="warning">No hay productos activos para asociar.</flux:callout>
            @endif
            <div class="flex justify-end gap-2 border-t border-zinc-200 pt-5 dark:border-white/10">
                <flux:button href="{{ route('product-files.index') }}" variant="ghost">Cancelar</flux:button>
                <flux:button type="submit" variant="primary" :disabled="$products->isEmpty()">Guardar archivo</flux:button>
            </div>
        </form>
    </flux:card>
</x-layouts::app>
