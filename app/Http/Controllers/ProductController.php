<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(): View
    {
        $products = Product::with('category')
            ->latest()
            ->paginate(10);

        return view('products.index', compact('products'));
    }

    public function create(): View
    {
        return view('products.create');
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

        return view('products.edit', compact('product'));
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
