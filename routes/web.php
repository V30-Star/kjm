<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UsersController;
use Illuminate\Support\Facades\Route;

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard'); // simple placeholder
    })->name('dashboard');

    Route::get('/users', [UsersController::class, 'index'])->name('users.index');
    Route::post('/users/data', [UsersController::class, 'dataServer'])->name('users.data'); // server-side
});
