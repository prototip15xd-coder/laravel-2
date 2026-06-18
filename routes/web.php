<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\AdminOrderController;
use App\Http\Controllers\Admin\AdminProductController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('guest')->group(function () {
    // registration
    Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register.form');
    Route::post('/register', [AuthController::class, 'register'])->name('register');

    // login
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login.form');
    Route::post('/login', [AuthController::class, 'login'])->name('login');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/profile', [AuthController::class, 'showProfile'])->name('profile.form');
    Route::patch('/profile/{id}', [AuthController::class, 'updateProfile'])->name('profile.update');

    Route::get('/change-password', [AuthController::class, 'showChangePasswordForm'])->name('password.form');
    Route::post('/change-password', [AuthController::class, 'updatePassword'])->name('password.update');

    // products
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');

    Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');

    Route::get('/categories', [CategoryController::class, 'index'])
        ->name('categories.index');

    Route::get('/categories/{category:slug}', [CategoryController::class, 'show'])
        ->name('categories.show');

    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/items/{product}', [CartController::class, 'store'])->name('cart.items.store');
    Route::patch('/cart/items/{product}', [CartController::class, 'update'])->name('cart.items.update');
    Route::delete('/cart/items/{product}', [CartController::class, 'destroy'])->name('cart.items.destroy');
    Route::delete('/cart', [CartController::class, 'clear'])->name('cart.clear');

    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::patch('/orders/{order}/status', [OrderController::class, 'updateStatus'])
        ->name('orders.status.update');

    // Адреса
    Route::get('/addresses/create', [AuthController::class, 'create'])->name('addresses.create');
    Route::post('/addresses', [AuthController::class, 'store'])->name('addresses.store');
    Route::delete('/addresses/{address}', [AuthController::class, 'destroy'])->name('addresses.destroy');
    Route::patch('/addresses/{address}/set-default', [AuthController::class, 'setDefault'])->name('addresses.set-default');

    Route::middleware(['auth', 'role:admin'])
        ->prefix('admin')
        ->name('admin.')
        ->group(function () {

            Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
            Route::get('/users', [App\Http\Controllers\Admin\AdminController::class, 'index'])->name('users.index');
            Route::get('/users/create', [App\Http\Controllers\Admin\AdminController::class, 'create'])->name('users.create');
            Route::post('/users', [App\Http\Controllers\Admin\AdminController::class, 'store'])->name('users.store');
            Route::get('/users/{user}', [App\Http\Controllers\Admin\AdminController::class, 'show'])->name('users.show');
            Route::get('/users/{user}/edit', [App\Http\Controllers\Admin\AdminController::class, 'edit'])->name('users.edit');
            Route::delete('/users/{user}', [App\Http\Controllers\Admin\AdminController::class, 'destroy'])->name('users.destroy');
            Route::post('/users/{user}/reset-password', [App\Http\Controllers\Admin\AdminController::class, 'resetPassword'])->name('users.reset-password');
            Route::patch('/users/{user}', [App\Http\Controllers\Admin\AdminController::class, 'update'])->name('users.update');

            Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
            Route::get('/orders/create', [AdminOrderController::class, 'create'])->name('orders.create');
            Route::post('/orders', [AdminOrderController::class, 'store'])->name('orders.store');
            Route::get('/orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
            Route::get('/orders/{order}/edit', [AdminOrderController::class, 'edit'])->name('orders.edit');
            Route::patch('/orders/{order}', [AdminOrderController::class, 'update'])->name('orders.update');
            Route::delete('/orders/{order}', [AdminOrderController::class, 'destroy'])->name('orders.destroy');
        });

    Route::middleware(['auth', 'role:admin'])
        ->prefix('admin')
        ->name('admin.')
        ->group(function () {
            Route::get('/products', [AdminProductController::class, 'index'])->name('products.index');
            Route::get('/products/create', [AdminProductController::class, 'create'])->name('products.create');
            Route::get('/products/{product}', [AdminProductController::class, 'show'])->name('products.show');
            Route::delete('/products/{product}', [AdminProductController::class, 'destroy'])->name('products.destroy');
            Route::get('/products/{product}/edit', [AdminProductController::class, 'edit'])->name('products.edit');
            Route::patch('/products/{product}', [AdminProductController::class, 'update'])->name('products.update');
        });
});
