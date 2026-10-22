<x-layouts::app>
    <section class="mx-auto max-w-6xl">
        <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
            <div>
                <flux:badge color="blue">Administración</flux:badge>
                <h1 class="mt-3 text-2xl font-semibold">Usuarios</h1>
                <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">Administra las cuentas y sus roles.</p>
            </div>
            <flux:button href="{{ route('users.create') }}" variant="primary">Nuevo usuario</flux:button>
        </div>
        @if (session('success'))
            <flux:callout variant="success" class="mb-4">{{ session('success') }}</flux:callout>
        @endif
        @if (session('error'))
            <flux:callout variant="danger" class="mb-4">{{ session('error') }}</flux:callout>
        @endif
        <x-admin.status-filter route-name="users.index" :options="$statusOptions" />
        <flux:table>
            <flux:table.columns>
                <flux:table.column>Nombre</flux:table.column>
                <flux:table.column>Correo electrónico</flux:table.column>
                <flux:table.column>Rol</flux:table.column>
                <flux:table.column>Registro</flux:table.column>
                <flux:table.column>Acciones</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @forelse ($users as $user)
                    <flux:table.row>
                        <flux:table.cell variant="strong">{{ $user->name }}</flux:table.cell>
                        <flux:table.cell>{{ $user->email }}</flux:table.cell>
                        <flux:table.cell>
                            <flux:badge :color="$user->role === 'admin' ? 'blue' : 'green'">{{ $user->role === 'admin' ? 'Administrador' : 'Cliente' }}</flux:badge>
                            @if ($user->trashed())
                                <span class="mt-1 block"><flux:badge color="red">Eliminado</flux:badge></span>
                            @endif
                        </flux:table.cell>
                        <flux:table.cell>{{ $user->created_at?->format('d/m/Y') }}</flux:table.cell>
                        <flux:table.cell>
                            @unless ($user->trashed())
                                <div class="flex items-center gap-2">
                                    <flux:button href="{{ route('users.edit', $user->id) }}" size="sm" variant="ghost">Editar</flux:button>
                                    <form action="{{ route('users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('¿Seguro que deseas eliminar este usuario?')">
                                        @csrf
                                        @method('DELETE')
                                        <flux:button type="submit" size="sm" variant="danger">Eliminar</flux:button>
                                    </form>
                                </div>
                            @endunless
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="5" class="text-center">Todavía no hay usuarios registrados.</flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
        @if ($users->hasPages())
            <div class="mt-6">{{ $users->links() }}</div>
        @endif
    </section>
</x-layouts::app>
