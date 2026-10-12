<x-layouts::app>
    <flux:card class="mx-auto max-w-3xl">
        <div class="mb-6">
            <flux:badge color="blue">Catálogo</flux:badge>
            <h1 class="mt-3 text-2xl font-semibold">Registrar producto</h1>
            <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">Introduce los datos del producto para TechStore.</p>
        </div>

        <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf
            <flux:input id="name" name="name" label="Nombre del producto" value="{{ old('name') }}" required maxlength="150" placeholder="Ej. Teclado mecánico" />
            <flux:description class="-mt-3">El identificador URL se generará automáticamente a partir del nombre del producto.</flux:description>
            <flux:field label="Descripción">
                <textarea id="description" name="description" rows="4" class="w-full rounded-lg border border-zinc-200 bg-white px-3 py-2 text-sm text-zinc-700 shadow-xs dark:border-white/10 dark:bg-white/10 dark:text-zinc-300">{{ old('description') }}</textarea>
                <flux:error name="description" />
            </flux:field>
            <flux:select id="category_id" name="category_id" label="Categoría" placeholder="Selecciona una categoría" required>
                @foreach (\App\Models\Category::where('status', true)->orderBy('name')->get() as $category)
                    <option value="{{ $category->id }}" @selected(old('category_id') == $category->id)>{{ $category->name }}</option>
                @endforeach
            </flux:select>
            <div class="grid gap-5 sm:grid-cols-2">
                <flux:input id="price" type="number" name="price" label="Precio" value="{{ old('price') }}" min="0.01" step="0.01" required />
                <flux:input id="stock" type="number" name="stock" label="Existencias" value="{{ old('stock', 0) }}" min="0" required />
            </div>
            @foreach ([
                'image' => 'Imagen principal del producto',
                'image_2' => 'Imagen 2',
                'image_3' => 'Imagen 3',
            ] as $field => $label)
                <flux:input id="{{ $field }}" type="file" name="{{ $field }}" label="{{ $label }}" accept="image/*" />
            @endforeach
            <div class="grid gap-5 sm:grid-cols-2">
                <flux:select id="featured" name="featured" label="Producto destacado">
                    <option value="0" @selected(old('featured', '0') == '0')>No</option>
                    <option value="1" @selected(old('featured') == '1')>Sí</option>
                </flux:select>
                <flux:select id="status" name="status" label="Estado del producto">
                    <option value="1" @selected(old('status', '1') == '1')>Activo</option>
                    <option value="0" @selected(old('status') == '0')>Inactivo</option>
                </flux:select>
            </div>
            <div class="flex justify-end gap-2 border-t border-zinc-200 pt-5 dark:border-white/10">
                <flux:button href="{{ route('products.index') }}" variant="ghost">Cancelar</flux:button>
                <flux:button type="submit" variant="primary">Guardar producto</flux:button>
            </div>
        </form>
    </flux:card>
</x-layouts::app>
