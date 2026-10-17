<x-layouts::app>
    <section class="mx-auto max-w-6xl">
        <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
            <div>
                <flux:badge color="blue">Administración</flux:badge>
                <h1 class="mt-3 text-2xl font-semibold">Categorías de TechStore</h1>
                <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">Administra las categorías de productos.</p>
            </div>
            <flux:button href="{{ route('categories.create') }}" variant="primary">Nueva categoría</flux:button>
        </div>
        @if (session('success'))
            <flux:callout variant="success" class="mb-4">{{ session('success') }}</flux:callout>
        @endif
        <flux:table>
            <flux:table.columns>
                <flux:table.column>Categoría</flux:table.column>
                <flux:table.column>Descripción</flux:table.column>
                <flux:table.column>Productos</flux:table.column>
                <flux:table.column>Estado</flux:table.column>
                <flux:table.column>Acciones</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @forelse ($categories as $category)
                    <flux:table.row>
                        <flux:table.cell variant="strong">{{ $category->name }}</flux:table.cell>
                        <flux:table.cell>{{ $category->description ?: '—' }}</flux:table.cell>
                        <flux:table.cell>{{ $category->products_count }}</flux:table.cell>
                        <flux:table.cell>
                            <flux:badge :color="$category->status ? 'green' : 'zinc'">{{ $category->status ? 'Activa' : 'Inactiva' }}</flux:badge>
                        </flux:table.cell>
                        <flux:table.cell>
                            <div class="flex items-center gap-2">
                                <flux:button href="{{ route('categories.edit', $category->id) }}" size="sm" variant="ghost">Editar</flux:button>
                                <form action="{{ route('categories.destroy', $category->id) }}" method="POST" onsubmit="return confirm('¿Seguro que deseas eliminar esta categoría?')">
                                    @csrf
                                    @method('DELETE')
                                    <flux:button type="submit" size="sm" variant="danger">Eliminar</flux:button>
                                </form>
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="5" class="text-center">Todavía no hay categorías registradas.</flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
        @if ($categories->hasPages())
            <div class="mt-6">{{ $categories->links() }}</div>
        @endif
    </section>
</x-layouts::app>
