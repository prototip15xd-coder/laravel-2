<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\AdminOrderController;
use App\Http\Controllers\Admin\AdminProductController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\ProfileController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\YooKassaController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ProductController::class, 'index'])->name('products.index');
Route::post('/payments/yookassa/webhook', [YooKassaController::class, 'webhook'])->name('payments.yookassa.webhook');


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

    Route::prefix('profile')->group(function () {
        Route::get('/', [AuthController::class, 'showProfile'])->name('profile.form');
        Route::patch('/{id}', [AuthController::class, 'updateProfile'])->name('profile.update');
        Route::delete('/delete', [ProfileController::class, 'destroy'])->name('profile.destroy');
    });

    Route::get('/change-password', [AuthController::class, 'showChangePasswordForm'])->name('password.form');
    Route::post('/change-password', [AuthController::class, 'updatePassword'])->name('password.update');

    // products
    //    Route::get('/products', [ProductController::class, 'index'])->name('products.index');

    Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');

    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::get('/categories/{category:slug}', [CategoryController::class, 'show'])->name('categories.show');

    Route::prefix('cart')->group(function () {
        Route::get('/', [CartController::class, 'index'])->name('cart.index');
        Route::post('/items/{product}', [CartController::class, 'store'])->name('cart.items.store');
        Route::patch('/items/{product}', [CartController::class, 'update'])->name('cart.items.update');
        Route::delete('/items/{product}', [CartController::class, 'destroy'])->name('cart.items.destroy');
        Route::delete('/', [CartController::class, 'clear'])->name('cart.clear');
    });

    Route::prefix('orders')->group(function () {
        Route::get('/', [OrderController::class, 'index'])->name('orders.index');
        Route::post('/', [OrderController::class, 'store'])->name('orders.store');
        Route::get('/{order}', [OrderController::class, 'show'])->name('orders.show');
        Route::patch('/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.status.update');
        Route::post('/{order}/pay', [OrderController::class, 'pay'])->name('orders.pay');
    });

    Route::prefix('addresses')->group(function () {
        Route::get('/create', [AuthController::class, 'create'])->name('addresses.create');
        Route::post('/', [AuthController::class, 'store'])->name('addresses.store');
        Route::delete('/{address}', [AuthController::class, 'destroy'])->name('addresses.destroy');
        Route::patch('/{address}/set-default', [AuthController::class, 'setDefault'])->name('addresses.set-default');
    });

    Route::prefix('email')->group(function () {
        Route::get('/verify', [AuthController::class, 'verify'])->name('email.verify');
        Route::get('/verify/{id}/{hash}', [AuthController::class, 'signed'])->name('verification.verify');
        Route::post('/verification-notification', [AuthController::class, 'send'])->name('verification.send');
    });

    //    Route::post('/payments/yookassa/webhook', [YooKassaController::class, 'webhook'])->name('payments.yookassa.webhook');

    Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {

        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        Route::prefix('users')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\AdminController::class, 'index'])->name('users.index');
            Route::get('/create', [App\Http\Controllers\Admin\AdminController::class, 'create'])->name('users.create');
            Route::post('/', [App\Http\Controllers\Admin\AdminController::class, 'store'])->name('users.store');
            Route::get('/{user}', [App\Http\Controllers\Admin\AdminController::class, 'show'])->name('users.show');
            Route::get('/{user}/edit', [App\Http\Controllers\Admin\AdminController::class, 'edit'])->name('users.edit');
            Route::delete('/{user}', [App\Http\Controllers\Admin\AdminController::class, 'destroy'])->name('users.destroy');
            Route::post('/{user}/reset-password', [App\Http\Controllers\Admin\AdminController::class, 'resetPassword'])->name('users.reset-password');
            Route::patch('/{user}', [App\Http\Controllers\Admin\AdminController::class, 'update'])->name('users.update');
        });

        Route::prefix('orders')->group(function () {
            Route::get('/', [AdminOrderController::class, 'index'])->name('orders.index');
            Route::get('/create', [AdminOrderController::class, 'create'])->name('orders.create');
            Route::post('/', [AdminOrderController::class, 'store'])->name('orders.store');
            Route::get('/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
            Route::get('/{order}/edit', [AdminOrderController::class, 'edit'])->name('orders.edit');
            Route::patch('/{order}', [AdminOrderController::class, 'update'])->name('orders.update');
            Route::delete('/{order}', [AdminOrderController::class, 'destroy'])->name('orders.destroy');
        });

        Route::prefix('products')->group(function () {
            Route::get('/', [AdminProductController::class, 'index'])->name('products.index');
            Route::get('/create', [AdminProductController::class, 'create'])->name('products.create');
            Route::post('/store', [AdminProductController::class, 'store'])->name('products.store');
            Route::get('/{product}', [AdminProductController::class, 'show'])->name('products.show');
            Route::delete('/{product}', [AdminProductController::class, 'destroy'])->name('products.destroy');
            Route::get('/{product}/edit', [AdminProductController::class, 'edit'])->name('products.edit');
            Route::patch('/{product}', [AdminProductController::class, 'update'])->name('products.update');
        });
    });
});
