<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function catalog(Request $request): View
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'category' => ['nullable', 'integer', Rule::exists('categories', 'id')->where('status', true)],
            'sort' => ['nullable', Rule::in(['name_asc', 'name_desc', 'featured'])],
        ]);

        $products = Product::query()
            ->with('category')
            ->where('status', true)
            ->whereHas('category', fn ($query) => $query->where('status', true))
            ->when($filters['search'] ?? null, function ($query, string $search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('name', 'like', '%'.$search.'%')
                        ->orWhere('description', 'like', '%'.$search.'%');
                });
            })
            ->when($filters['category'] ?? null, fn ($query, int $categoryId) => $query->where('category_id', $categoryId))
            ->when(
                ($filters['sort'] ?? 'featured') === 'name_asc',
                fn ($query) => $query->orderBy('name')
            )
            ->when(
                ($filters['sort'] ?? 'featured') === 'name_desc',
                fn ($query) => $query->orderByDesc('name')
            )
            ->when(
                ($filters['sort'] ?? 'featured') === 'featured',
                fn ($query) => $query->orderByDesc('featured')->orderBy('name')
            )
            ->paginate(12)
            ->withQueryString();

        $categories = Category::query()
            ->where('status', true)
            ->orderBy('name')
            ->get();

        return view('client.store', compact('products', 'categories'));
    }

    public function catalogProduct(string $slug): View
    {
        $product = Product::query()
            ->with('category')
            ->where('slug', $slug)
            ->where('status', true)
            ->whereHas('category', fn ($query) => $query->where('status', true))
            ->firstOrFail();

        $images = collect([$product->image, $product->image_2, $product->image_3])
            ->filter()
            ->values();

        return view('client.product', compact('product', 'images'));
    }

    public function index(Request $request): View
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'category' => ['nullable', 'integer', Rule::exists('categories', 'id')],
            'sort' => ['nullable', Rule::in(['name_asc', 'name_desc', 'featured'])],
            'status' => ['nullable', Rule::in(['all', 'active', 'inactive', 'deleted'])],
        ]);

        $products = Product::query()
            ->withTrashed()
            ->with('category')
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
            ->when($filters['search'] ?? null, function ($query, string $search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('name', 'like', '%'.$search.'%')
                        ->orWhere('description', 'like', '%'.$search.'%');
                });
            })
            ->when($filters['category'] ?? null, fn ($query, int $categoryId) => $query->where('category_id', $categoryId))
            ->when(
                ($filters['sort'] ?? 'featured') === 'name_asc',
                fn ($query) => $query->orderBy('name')
            )
            ->when(
                ($filters['sort'] ?? 'featured') === 'name_desc',
                fn ($query) => $query->orderByDesc('name')
            )
            ->when(
                ($filters['sort'] ?? 'featured') === 'featured',
                fn ($query) => $query->orderByDesc('featured')->orderBy('name')
            )
            ->paginate(12)
            ->withQueryString();

        $categories = Category::query()
            ->orderBy('name')
            ->get();

        $statusOptions = [
            'all' => 'Todos (sin eliminar)',
            'active' => 'Activos',
            'inactive' => 'Inactivos',
            'deleted' => 'Eliminados',
        ];

        return view('admin.products.index', compact('products', 'categories', 'statusOptions'));
    }

    public function create(): View
    {
        return view('admin.products.create');
    }

    public function store(Request $request): RedirectResponse
    {

        $validated = $request->validate([
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'max:2048'],
            'image_2' => ['nullable', 'image', 'max:2048'],
            'image_3' => ['nullable', 'image', 'max:2048'],
            'price' => ['required', 'numeric', 'min:0.01', 'max:99999999.99'],
            'stock' => ['required', 'integer', 'min:0'],
            'featured' => ['required', 'boolean'],
            'status' => ['required', 'boolean'],
        ]);

        $validated['slug'] = $this->uniqueSlug($validated['name']);

        foreach (['image', 'image_2', 'image_3'] as $imageField) {
            if ($request->hasFile($imageField)) {
                $image = $request->file($imageField);

                $imageName = Str::uuid().'.'.$image->extension();

                $image->storeAs('', $imageName, 'images');

                $validated[$imageField] = $imageName;
            }
        }

        Product::create($validated);

        return redirect()
            ->route('products.index')
            ->with('success', 'El producto se registró correctamente.');
    }

    public function edit(int $id): View
    {
        $product = Product::findOrFail($id);

        return view('admin.products.edit', compact('product'));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $product = Product::findOrFail($id);

        $validated = $request->validate([
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'max:2048'],
            'image_2' => ['nullable', 'image', 'max:2048'],
            'image_3' => ['nullable', 'image', 'max:2048'],
            'price' => ['required', 'numeric', 'min:0.01', 'max:99999999.99'],
            'stock' => ['required', 'integer', 'min:0'],
            'featured' => ['required', 'boolean'],
            'status' => ['required', 'boolean'],
        ]);

        $validated['slug'] = $this->uniqueSlug($validated['name'], $product->id);

        foreach (['image', 'image_2', 'image_3'] as $imageField) {
            if ($request->hasFile($imageField)) {
                $image = $request->file($imageField);
                $imageName = time().'_'.$image->getClientOriginalName();

                $image->storeAs('/', $imageName, 'images');
                $validated[$imageField] = $imageName;
            } else {
                unset($validated[$imageField]);
            }
        }

        $product->update($validated);

        return redirect()
            ->route('products.index')
            ->with('success', 'El producto se actualizó correctamente.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $product = Product::findOrFail($id);
        $product->delete();

        return redirect()->route('products.index')->with('success', 'El producto se eliminó correctamente.');
    }

    private function uniqueSlug(string $name, ?int $ignoreProductId = null): string
    {
        $baseSlug = Str::substr(Str::slug($name) ?: 'producto', 0, 180);
        $slug = $baseSlug;
        $suffix = 2;

        while (Product::withTrashed()
            ->where('slug', $slug)
            ->when($ignoreProductId, fn ($query) => $query->where('id', '!=', $ignoreProductId))
            ->exists()) {
            $suffixText = '-'.$suffix++;
            $slug = Str::substr($baseSlug, 0, 180 - Str::length($suffixText)).$suffixText;
        }

        return $slug;
    }
}
