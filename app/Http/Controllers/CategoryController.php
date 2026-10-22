<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'status' => ['nullable', Rule::in(['all', 'active', 'inactive', 'deleted'])],
        ]);

        $categories = Category::withTrashed()
            ->withCount('products')
            ->when($filters['status'] ?? 'all', function ($query, string $status): void {
                if ($status === 'deleted') {
                    $query->onlyTrashed();

                    return;
                }

                $query->withoutTrashed();

                if (in_array($status, ['active', 'inactive'], true)) {
                    $query->where('status', $status === 'active');
                }
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $statusOptions = [
            'all' => 'Todas (sin eliminar)',
            'active' => 'Activas',
            'inactive' => 'Inactivas',
            'deleted' => 'Eliminadas',
        ];

        return view('admin.categories.index', compact('categories', 'statusOptions'));
    }

    public function create(): View
    {
        return view('admin.categories.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:categories,name'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'boolean'],
        ]);

        Category::create($validated);

        return redirect()->route('categories.index')->with('success', 'La categoría se registró correctamente.');
    }

    public function edit(int $id): View
    {
        $category = Category::findOrFail($id);

        return view('admin.categories.edit', compact('category'));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $category = Category::findOrFail($id);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', Rule::unique('categories', 'name')->ignore($category->id)],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'boolean'],
        ]);

        $category->update($validated);

        return redirect()->route('categories.index')->with('success', 'La categoría se actualizó correctamente.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $category = Category::findOrFail($id);
        $category->delete();

        return redirect()->route('categories.index')->with('success', 'La categoría se eliminó correctamente.');
    }
}
