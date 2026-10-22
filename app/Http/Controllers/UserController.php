<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'status' => ['nullable', Rule::in(['all', 'deleted'])],
        ]);

        $users = User::withTrashed()
            ->when($filters['status'] ?? 'all', function ($query, string $status): void {
                if ($status === 'deleted') {
                    $query->onlyTrashed();

                    return;
                }

                $query->withoutTrashed();
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $statusOptions = [
            'all' => 'Todos (sin eliminar)',
            'deleted' => 'Eliminados',
        ];

        return view('admin.users.index', compact('users', 'statusOptions'));
    }

    public function create(): View
    {
        return view('admin.users.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'role' => ['required', Rule::in(['admin', 'cliente'])],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
        ]);
        $user->role = $validated['role'];
        $user->save();

        return redirect()->route('users.index')->with('success', 'El usuario se registró correctamente.');
    }

    public function edit(int $id): View
    {
        $user = User::findOrFail($id);

        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'role' => ['required', Rule::in(['admin', 'cliente'])],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        $user->fill([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);
        $user->role = $validated['role'];

        if (! empty($validated['password'])) {
            $user->password = $validated['password'];
        }

        $user->save();

        return redirect()->route('users.index')->with('success', 'El usuario se actualizó correctamente.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $user = User::findOrFail($id);

        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')->with('error', 'No puedes eliminar tu propia cuenta desde esta pantalla.');
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', 'El usuario se eliminó correctamente.');
    }
}
