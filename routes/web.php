<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register.form');
Route::post('/register', [AuthController::class, 'register'])->name('register');
