<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\TagsController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CallbackController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\ProductImageController;

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

Route::group(['prefix' => 'account'], function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);
    Route::group(['middleware' => 'auth:sanctum'], function () {
        Route::post('logout', [AuthController::class, 'logout']);
    });
});

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::get('products', [ProductController::class, 'allProducts']);
    Route::get('product/{slug}', [ProductController::class, 'getProduct']);
    Route::get('search-products', [ProductController::class, 'searchProductByName']);
    Route::get('personalized-products', [ProductController::class, 'getPersonalizedProducts']);
    Route::get('categories', [CategoryController::class, 'allCategories']);
    Route::get('category/{slug}', [CategoryController::class, 'getCategoryBySlug']);
    Route::get('product-image', [ProductImageController::class, 'getAllProductImages']);
    Route::prefix('transactions')->group(function () {
        Route::post('/', [TransactionController::class, 'createTransaction']);
        Route::get('/', [TransactionController::class, 'getTransactions']);
        Route::get('/{transactionId}', [TransactionController::class, 'getTransaction']);
    });
    Route::get('carts', [CartController::class, 'getCarts']);
    Route::delete('carts', [CartController::class, 'deleteCart']);
    Route::put('carts', [CartController::class, 'updateCartItem']);
    Route::post('carts', [CartController::class, 'addCart']);
    Route::get('tags/{slug}', [TagsController::class, 'showTagProducts']);
    Route::get('tags', [TagsController::class, 'allTags']);
    Route::get('tags/products/{tag}', [TagsController::class, 'getProductsByTag']);
    Route::post('shipping', [TransactionController::class, 'getShippingCost']);
    Route::group(['prefix' => 'user'], function () {
        Route::get('detail', [AuthController::class, 'user']);
        Route::post('personal', [UserController::class, 'personalizeUser']);
        Route::get('personal', [UserController::class, 'getUserPersonal']);
        Route::put('personal', [UserController::class, 'updateUserPersonal']);
    });
    Route::group(['prefix' => 'address'], function () {
        Route::get('/', [AddressController::class, 'getAddresses']);
        Route::get('provinces', [AddressController::class, 'getProvinces']);
        Route::get('cities/{provinceId}', [AddressController::class, 'getCities']);
        Route::post('create', [AddressController::class, 'createAddress']);
        Route::put('update', [AddressController::class, 'updateAddress']);
        Route::delete('delete', [AddressController::class, 'deleteAddress']);
    });
    Route::middleware(['ability:admin'])->group(function () {
        Route::get('/search-transactions', [TransactionController::class, 'searchTransactions']);
        Route::post('tags', [TagsController::class, 'createTag']);
        Route::put('tags', [TagsController::class, 'updateTag']);
        Route::delete('tags', [TagsController::class, 'deleteTag']);
        Route::get('users', [UserController::class, 'getAllUsers']);
        Route::delete('users', [UserController::class, 'deleteUser']);
        Route::post('products', [ProductController::class, 'createProduct']);
        Route::put('products', [ProductController::class, 'updateProduct']);
        Route::delete('products', [ProductController::class, 'deleteProduct']);
        Route::post('categories', [CategoryController::class, 'createCategory']);
        Route::put('categories', [CategoryController::class, 'updateCategory']);
        Route::delete('categories', [CategoryController::class, 'deleteCategory']);
        Route::post('product-image', [ProductImageController::class, 'updateProductImage']);
        Route::put('transactions', [TransactionController::class, 'updateTransaction']);
        Route::get('all-transactions', [TransactionController::class, 'allTransactions']);
        Route::get('dashboard', [DashboardController::class, 'index']);
    });
});

Route::middleware('midtrans')->post('callback', [CallbackController::class, 'callback']);
