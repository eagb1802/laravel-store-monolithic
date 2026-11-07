<?php

use Illuminate\Support\Facades\Route;

# Controladores
use App\Http\Controllers\ProductController;

Route::get('/', function () {
    return view('welcome');
});

# Rutas de productos

# https://store-api.com/products [GET]
Route::get('/products', [ProductController::class, 'getAll']);

# https://store-api.com/products/search [GET]
Route::get('/product/search', [ProductController::class, 'findByName']);

# https://store-api.com/products/<id> [GET]
Route::get('/product/{id}', [ProductController::class, 'findById']);

Route::post('/product/create', [ProductController::class, 'insert']);

Route::put('/product/update/{id}', [ProductController::class, 'update']);
Route::delete('/product/delete/{id}', [ProductController::class, 'delete']);
