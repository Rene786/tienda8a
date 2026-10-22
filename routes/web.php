
<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\CartItemController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderItemController;
use App\Http\Controllers\PdfDownloadController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductFileController;
use App\Http\Controllers\UserController;
use App\Models\Product;
use Illuminate\Support\Facades\Route;

// Rutas para clientes
Route::get('/', function () {
    $featuredProducts = Product::query()
        ->with('category')
        ->where('featured', true)
        ->where('status', true)
        ->whereHas('category', fn ($query) => $query->where('status', true))
        ->orderBy('name')
        ->get();

    return view('client.home', compact('featuredProducts'));
});

Route::get('/contact', function () {
    return view('client.contact');
})->name('contact');
Route::post('/contact', [ContactController::class, 'send'])
    ->middleware('throttle:5,1')
    ->name('contact.send');
Route::get('/about', function () {
    return view('client.aboutUs');
})->name('about');

Route::get('/store', [ProductController::class, 'catalog'])->name('store');
Route::get('/store/{slug}', [ProductController::class, 'catalogProduct'])->name('store.show');

// Rutas para autenticación
Route::livewire('/login', 'auth.login')->name('login');
Route::livewire('/register', 'auth.register')->name('register');

// rutas para el dashboard y reportes
Route::middleware('auth')->group(function () {
    Route::get('/home', [DashboardController::class, 'index'])->name('home');
    Route::get('/pdf-downloads', [PdfDownloadController::class, 'index'])->name('pdf-downloads.index');
    Route::get('/reports/dashboard/{metric}', [DashboardController::class, 'report'])
        ->whereIn('metric', ['daily-sales', 'monthly-sales', 'products', 'users', 'inventory'])
        ->name('reports.dashboard');
});

// Rutas para administrar productos
Route::middleware('auth')->group(function () {
    Route::get('product-files/{productFile}/download', [ProductFileController::class, 'download'])
        ->name('product-files.download');
    Route::resource('categories', CategoryController::class)->except('show');
    Route::resource('carts', CartController::class)->except('show');
    Route::resource('cart-items', CartItemController::class)->except('show')->parameters(['cart-items' => 'cartItem']);
    Route::resource('products', ProductController::class);
    Route::resource('product-files', ProductFileController::class)->except('show')->parameters(['product-files' => 'productFile']);
    Route::resource('orders', OrderController::class)->except('show');
    Route::resource('order-items', OrderItemController::class)->except('show')->parameters(['order-items' => 'orderItem']);
    Route::resource('users', UserController::class)->except('show');
});
