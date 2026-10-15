
<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\CartItemController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderItemController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductFileController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;


//Rutas para clientes
Route::get('/', function () {
    return view('client.home');
});
route::get('/contact', function () {
    return view('client.contact');
})->name('contact');
route::get('/about', function () {
    return view('client.aboutUs');
})->name('about');

//sin usar
route::get('/store', function () {
    return view('client.store');
})->name('store');

//Rutas para autenticación
Route::livewire('/login', 'auth.login')->name('login');

Route::livewire('/register', 'auth.register')->name('register');


//rutas para el dashboard y reportes
Route::middleware('auth')->group(function () {
    Route::get('/home', [DashboardController::class, 'index'])->name('home');
    Route::get('/reports/dashboard/{metric}', [DashboardController::class, 'report'])
        ->whereIn('metric', ['daily-sales', 'monthly-sales', 'products', 'users', 'inventory'])
        ->name('reports.dashboard');
});



// Rutas para administrar productos
Route::middleware('auth')->group(function () {
    Route::resource('categories', CategoryController::class)->except('show');
    Route::resource('carts', CartController::class)->except('show');
    Route::resource('cart-items', CartItemController::class)->except('show')->parameters(['cart-items' => 'cartItem']);
    Route::resource('products', ProductController::class);
    Route::resource('product-files', ProductFileController::class)->except('show')->parameters(['product-files' => 'productFile']);
    Route::resource('orders', OrderController::class)->except('show');
    Route::resource('order-items', OrderItemController::class)->except('show')->parameters(['order-items' => 'orderItem']);
    Route::resource('users', UserController::class)->except('show');
});
