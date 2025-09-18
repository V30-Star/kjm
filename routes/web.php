<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\MerekController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\GroupProductController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PurchaseController;
use Illuminate\Support\Facades\Route;

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard'); // simple placeholder
    })->name('dashboard');

    Route::get('/users', [UsersController::class, 'index'])->name('users.index');
    Route::post('/users/data', [UsersController::class, 'dataServer'])->name('users.data');
    Route::get('/users/create', [UsersController::class, 'create'])->name('users.create');
    Route::post('/users', [UsersController::class, 'store'])->name('users.store');
    Route::get('/users/{user}/edit', [UsersController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [UsersController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UsersController::class, 'destroy'])->name('users.destroy');

    Route::get('/groupproduct',            [GroupProductController::class, 'index'])->name('groupproduct.index');
    Route::post('/groupproduct/data',      [GroupProductController::class, 'dataServer'])->name('groupproduct.data');
    Route::get('/groupproduct/create',     [GroupProductController::class, 'create'])->name('groupproduct.create');
    Route::post('/groupproduct',           [GroupProductController::class, 'store'])->name('groupproduct.store');
    Route::get('/groupproduct/{gp}/edit',  [GroupProductController::class, 'edit'])->name('groupproduct.edit');
    Route::put('/groupproduct/{gp}',       [GroupProductController::class, 'update'])->name('groupproduct.update');
    Route::delete('/groupproduct/{gp}',    [GroupProductController::class, 'destroy'])->name('groupproduct.destroy');

    Route::get('/merek',             [MerekController::class, 'index'])->name('merek.index');
    Route::post('/merek/data',       [MerekController::class, 'dataServer'])->name('merek.data');
    Route::get('/merek/create',      [MerekController::class, 'create'])->name('merek.create');
    Route::post('/merek',            [MerekController::class, 'store'])->name('merek.store');
    Route::get('/merek/{merek}/edit', [MerekController::class, 'edit'])->name('merek.edit');
    Route::put('/merek/{merek}',     [MerekController::class, 'update'])->name('merek.update');
    Route::delete('/merek/{merek}',  [MerekController::class, 'destroy'])->name('merek.destroy');

    Route::get('/product',             [ProductController::class, 'index'])->name('product.index');
    Route::post('/product/data',       [ProductController::class, 'dataServer'])->name('product.data');
    Route::get('/product/create',      [ProductController::class, 'create'])->name('product.create');
    Route::post('/product',            [ProductController::class, 'store'])->name('product.store');
    Route::get('/product/{product}/edit', [ProductController::class, 'edit'])->name('product.edit');
    Route::put('/product/{product}',   [ProductController::class, 'update'])->name('product.update');
    Route::delete('/product/{product}', [ProductController::class, 'destroy'])->name('product.destroy');

    Route::get('/pembelian',                [PurchaseController::class, 'index'])->name('pembelian.index');
    Route::post('/pembelian/data',          [PurchaseController::class, 'data'])->name('pembelian.data');
    Route::get('/pembelian/create',         [PurchaseController::class, 'create'])->name('pembelian.create');
    Route::post('/pembelian',               [PurchaseController::class, 'store'])->name('pembelian.store');
    Route::get('/pembelian/{purchase}/edit', [PurchaseController::class, 'edit'])->name('pembelian.edit');
    Route::put('/pembelian/{purchase}',     [PurchaseController::class, 'update'])->name('pembelian.update');
    Route::delete('/pembelian/{purchase}',  [PurchaseController::class, 'destroy'])->name('pembelian.destroy');

    // Select2 AJAX search produk (supaya gampang input banyak item)
    Route::get('/products/search',          [PurchaseController::class, 'searchProducts'])->name('products.search');
    // routes/web.php
    Route::get('pembelian/{pembelian}/print', [PurchaseController::class, 'print'])
        ->name('pembelian.print');
});
