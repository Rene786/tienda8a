<x-layouts::app>
    <section class="mx-auto max-w-6xl">
        <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
            <div>
                <flux:badge color="blue">Administración</flux:badge>
                <h1 class="mt-3 text-2xl font-semibold">Archivos de productos</h1>
                <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">Administra documentos y descargas asociadas a los productos.</p>
            </div>
            <flux:button href="{{ route('product-files.create') }}" variant="primary">Agregar archivo</flux:button>
        </div>
        @if (session('success'))
            <flux:callout variant="success" class="mb-4">{{ session('success') }}</flux:callout>
        @endif
        <flux:table>
            <flux:table.columns>
                <flux:table.column>Nombre</flux:table.column>
                <flux:table.column>Producto</flux:table.column>
                <flux:table.column>Tipo</flux:table.column>
                <flux:table.column>Archivo</flux:table.column>
                <flux:table.column>Acciones</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @forelse ($productFiles as $productFile)
                    <flux:table.row>
                        <flux:table.cell variant="strong">{{ $productFile->name }}</flux:table.cell>
                        <flux:table.cell>
                            {{ $productFile->product?->name ?? 'Producto no disponible' }}
                            @if ($productFile->product?->trashed())<span class="block text-xs text-amber-700">Producto eliminado</span>@endif
                        </flux:table.cell>
                        <flux:table.cell>{{ $productFile->file_type ?: '—' }}</flux:table.cell>
                        <flux:table.cell>
                            <flux:button href="{{ $productFile->isPdf() ? route('product-files.download', $productFile) : (str_starts_with($productFile->file_path, 'http') ? $productFile->file_path : asset('storage/'.$productFile->file_path)) }}" target="_blank" rel="noopener noreferrer" size="sm" variant="ghost">Ver archivo</flux:button>
                        </flux:table.cell>
                        <flux:table.cell>
                            <div class="flex items-center gap-2">
                                <flux:button href="{{ route('product-files.edit', $productFile) }}" size="sm" variant="ghost">Editar</flux:button>
                                <form action="{{ route('product-files.destroy', $productFile) }}" method="POST" onsubmit="return confirm('¿Seguro que deseas eliminar este archivo?')">
                                    @csrf
                                    @method('DELETE')
                                    <flux:button type="submit" size="sm" variant="danger">Eliminar</flux:button>
                                </form>
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="5" class="text-center">Todavía no hay archivos registrados.</flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
        @if ($productFiles->hasPages())
            <div class="mt-6">{{ $productFiles->links() }}</div>
        @endif
    </section>
</x-layouts::app>
