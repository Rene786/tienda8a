<x-layouts::app>
    <flux:card class="mx-auto max-w-3xl">
        <div class="mb-6">
            <flux:badge color="blue">Catálogo</flux:badge>
            <h1 class="mt-3 text-2xl font-semibold">Editar producto</h1>
            <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">Modifica los datos de {{ $product->name }}.</p>
        </div>

        <form action="{{ route('products.update', $product->id) }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf
            @method('PUT')
            <flux:input id="name" name="name" label="Nombre del producto" value="{{ old('name', $product->name) }}" required maxlength="150" />
            <flux:description class="-mt-3">Al guardar, el identificador URL se generará automáticamente a partir del nombre.</flux:description>
            <flux:field label="Descripción">
                <textarea id="description" name="description" rows="4" class="w-full rounded-lg border border-zinc-200 bg-white px-3 py-2 text-sm text-zinc-700 shadow-xs dark:border-white/10 dark:bg-white/10 dark:text-zinc-300">{{ old('description', $product->description) }}</textarea>
                <flux:error name="description" />
            </flux:field>
            <flux:select id="category_id" name="category_id" label="Categoría" required>
                @foreach (\App\Models\Category::where('status', true)->orderBy('name')->get() as $category)
                    <option value="{{ $category->id }}" @selected(old('category_id', $product->category_id) == $category->id)>{{ $category->name }}</option>
                @endforeach
            </flux:select>
            <div class="grid gap-5 sm:grid-cols-2">
                <flux:input id="price" type="number" name="price" label="Precio" value="{{ old('price', $product->price) }}" min="0.01" step="0.01" required />
                <flux:input id="stock" type="number" name="stock" label="Existencias" value="{{ old('stock', $product->stock) }}" min="0" required />
            </div>
            @foreach ([
                'image' => 'Imagen principal',
                'image_2' => 'Imagen 2',
                'image_3' => 'Imagen 3',
            ] as $field => $label)
                <flux:field :label="$label">
                    @if ($product->{$field})
                        <img src="{{ asset('storage/images/'.$product->{$field}) }}" alt="{{ $label }} de {{ $product->name }}" class="mb-3 size-24 rounded-lg object-cover">
                    @endif
                    <flux:input id="{{ $field }}" type="file" name="{{ $field }}" accept="image/*" />
                    <flux:error :name="$field" />
                </flux:field>
            @endforeach
            <div class="grid gap-5 sm:grid-cols-2">
                <flux:select id="featured" name="featured" label="Producto destacado">
                    <option value="0" @selected(old('featured', $product->featured ? '1' : '0') == '0')>No</option>
                    <option value="1" @selected(old('featured', $product->featured ? '1' : '0') == '1')>Sí</option>
                </flux:select>
                <flux:select id="status" name="status" label="Estado del producto">
                    <option value="1" @selected(old('status', $product->status ? '1' : '0') == '1')>Activo</option>
                    <option value="0" @selected(old('status', $product->status ? '1' : '0') == '0')>Inactivo</option>
                </flux:select>
            </div>
            <div class="flex justify-end gap-2 border-t border-zinc-200 pt-5 dark:border-white/10">
                <flux:button href="{{ route('products.index') }}" variant="ghost">Cancelar</flux:button>
                <flux:button type="submit" variant="primary">Actualizar producto</flux:button>
            </div>
        </form>
    </flux:card>
</x-layouts::app>
