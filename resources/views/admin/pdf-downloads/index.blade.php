<x-layouts::app>
    <section class="mx-auto max-w-6xl">
        <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
            <div>
                <flux:badge color="blue">Administración</flux:badge>
                <h1 class="mt-3 text-2xl font-semibold">Historial de descargas PDF</h1>
                <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">Consulta quién descargó cada PDF, el archivo y la fecha.</p>
            </div>
            <flux:button href="{{ route('home') }}" variant="ghost">Volver al panel</flux:button>
        </div>
        <flux:table>
            <flux:table.columns>
                <flux:table.column>Fecha</flux:table.column>
                <flux:table.column>Usuario</flux:table.column>
                <flux:table.column>Archivo</flux:table.column>
                <flux:table.column>Origen</flux:table.column>
                <flux:table.column>IP</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @forelse ($pdfDownloads as $pdfDownload)
                    <flux:table.row>
                        <flux:table.cell>{{ $pdfDownload->downloaded_at->format('d/m/Y H:i:s') }}</flux:table.cell>
                        <flux:table.cell>
                            {{ $pdfDownload->user?->name ?? 'Usuario no disponible' }}
                            @if ($pdfDownload->user?->trashed())<span class="block text-xs text-amber-700">Usuario eliminado</span>@endif
                        </flux:table.cell>
                        <flux:table.cell>
                            {{ $pdfDownload->file_name }}
                            <span class="block break-all text-xs text-zinc-500">{{ $pdfDownload->file_path }}</span>
                        </flux:table.cell>
                        <flux:table.cell>
                            @if ($pdfDownload->productFile)
                                Archivo de producto
                                @if ($pdfDownload->productFile->product)
                                    <span class="block text-xs text-zinc-500">{{ $pdfDownload->productFile->product->name }}</span>
                                @endif
                            @else
                                Reporte
                            @endif
                        </flux:table.cell>
                        <flux:table.cell>{{ $pdfDownload->ip_address ?: '—' }}</flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="5" class="text-center">Todavía no hay descargas registradas.</flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
        @if ($pdfDownloads->hasPages())
            <div class="mt-6">{{ $pdfDownloads->links() }}</div>
        @endif
    </section>
</x-layouts::app>
