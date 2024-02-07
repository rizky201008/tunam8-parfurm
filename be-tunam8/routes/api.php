<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::group(['prefix' => 'account', 'middleware' => 'cors'], function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);
    Route::group(['middleware' => 'auth:sanctum'], function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('user', [AuthController::class, 'user']);
    });
});

Route::group(['middleware' => ['auth:sanctum', 'cors']], function () {
    Route::get('products', [ProductController::class, 'allProducts']);
    Route::get('product/{slug}', [ProductController::class, 'getProduct']);
    Route::get('categories', [CategoryController::class, 'allCategories']);
    Route::get('category/{slug}', [CategoryController::class, 'getCategoryBySlug']);
    Route::middleware(['ability:admin'])->group(function () {
        Route::post('products', [ProductController::class, 'createProduct']);
        Route::put('products', [ProductController::class, 'updateProduct']);
        Route::delete('products', [ProductController::class, 'deleteProduct']);
        Route::post('categories', [CategoryController::class, 'createCategory']);
        Route::put('categories', [CategoryController::class, 'updateCategory']);
        Route::delete('categories', [CategoryController::class, 'deleteCategory']);
    });
});
