<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductTypeController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ColorController;
use App\Http\Controllers\Api\ShapeController;
use App\Http\Controllers\Api\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Authentication routes

Route::post('/login', [AuthController::class, 'login']);

// Public product routes (read-only)
Route::get('/product-types', [ProductTypeController::class, 'index']);
Route::get('/product-types/{id}', [ProductTypeController::class, 'show']);

Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/{id}', [CategoryController::class, 'show']);

Route::get('/colors', [ColorController::class, 'index']);
Route::get('/colors/{id}', [ColorController::class, 'show']);

Route::get('/shapes', [ShapeController::class, 'index']);
Route::get('/shapes/{id}', [ShapeController::class, 'show']);

Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{id}', [ProductController::class, 'show']);

// Protected routes (require authentication)
Route::middleware('auth:sanctum')->group(function () {
    // Auth routes
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Product Type management
    Route::post('/product-types', [ProductTypeController::class, 'store']);
    Route::put('/product-types/{id}', [ProductTypeController::class, 'update']);
    Route::delete('/product-types/{id}', [ProductTypeController::class, 'destroy']);

    // Category management
    Route::post('/categories', [CategoryController::class, 'store']);
    Route::put('/categories/{id}', [CategoryController::class, 'update']);
    Route::delete('/categories/{id}', [CategoryController::class, 'destroy']);

    // Color management
    Route::post('/colors', [ColorController::class, 'store']);
    Route::put('/colors/{id}', [ColorController::class, 'update']);
    Route::delete('/colors/{id}', [ColorController::class, 'destroy']);

    // Shape management
    Route::post('/shapes', [ShapeController::class, 'store']);
    Route::put('/shapes/{id}', [ShapeController::class, 'update']);
    Route::delete('/shapes/{id}', [ShapeController::class, 'destroy']);

    // Product management
    Route::post('/products', [ProductController::class, 'store']);
    Route::post('/products/{id}', [ProductController::class, 'update']); // POST for multipart/form-data
    Route::delete('/products/{id}', [ProductController::class, 'destroy']);
});
