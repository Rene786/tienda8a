<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductFile;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProductFileController extends Controller
{
    public function index(): View
    {
        $productFiles = ProductFile::with(['product' => fn ($query) => $query->withTrashed()])
            ->latest()
            ->paginate(10);

        return view('admin.products.files.index', compact('productFiles'));
    }

    public function create(): View
    {
        $products = Product::where('status', true)->orderBy('name')->get();

        return view('admin.products.files.create', compact('products'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'product_id' => ['required', 'integer', Rule::exists('products', 'id')->whereNull('deleted_at')],
            'name' => ['required', 'string', 'max:150'],
            'file_path' => ['required', 'string', 'max:255'],
            'file_type' => ['nullable', 'string', 'max:50'],
        ]);

        ProductFile::create($validated);

        return redirect()->route('product-files.index')->with('success', 'El archivo se registró correctamente.');
    }

    public function edit(ProductFile $productFile): View
    {
        $products = $this->productsForEdit($productFile);

        return view('admin.products.files.edit', compact('productFile', 'products'));
    }

    public function update(Request $request, ProductFile $productFile): RedirectResponse
    {
        $validated = $request->validate([
            'product_id' => ['required', 'integer', Rule::exists('products', 'id')->where(fn ($query) => $query->whereNull('deleted_at')->orWhere('id', $productFile->product_id))],
            'name' => ['required', 'string', 'max:150'],
            'file_path' => ['required', 'string', 'max:255'],
            'file_type' => ['nullable', 'string', 'max:50'],
        ]);

        $productFile->update($validated);

        return redirect()->route('product-files.index')->with('success', 'El archivo se actualizó correctamente.');
    }

    public function destroy(ProductFile $productFile): RedirectResponse
    {
        $productFile->delete();

        return redirect()->route('product-files.index')->with('success', 'El archivo se eliminó correctamente.');
    }

    private function productsForEdit(ProductFile $productFile): Collection
    {
        $products = Product::where('status', true)->orderBy('name')->get();

        if (! $products->contains('id', $productFile->product_id)) {
            $products->push(Product::withTrashed()->find($productFile->product_id));
        }

        return $products->filter();
    }
}
