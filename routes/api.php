<?php

use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\ProfileController;
use App\Http\Controllers\Api\CartsController;
use App\Http\Controllers\Api\CheckoutController;
use App\Http\Controllers\Api\ProductsController;
use App\Http\Controllers\Api\WishlistsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

//login
Route::post('/login', [LoginController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    //profile
    Route::get('/profile', [ProfileController::class, 'profile']);

    //products
    Route::get('/products', [ProductsController::class, 'products']);
    Route::get('/product/{id}', [ProductsController::class, 'read']);
    Route::post('/product/create', [ProductsController::class, 'create']);
    Route::post('/product/update/{id}', [ProductsController::class, 'update']);
    Route::post('/product/delete/{id}', [ProductsController::class, 'delete']);

    //carts
    Route::get('/cart', [CartsController::class, 'getCart']);
    Route::post('/cart/emptyCart', [CartsController::class, 'emptyCart']);
    Route::post('/cart/addProductToCart', [CartsController::class, 'addProductToCart']);
    Route::post('/cart/clearProductFromCart', [CartsController::class, 'clearProductFromCart']);
    Route::post('/cart/updateProductInCart', [CartsController::class, 'updateProductInCart']);
    Route::post('/cart/removeProductFromCart', [CartsController::class, 'removeProductFromCart']);

    //Wishlist
    Route::get('wishlist', [WishlistsController::class, 'index']);
    Route::post('/Wishlist/addProductToWishlist', [WishlistsController::class, 'addProductToWishlist']);
    Route::post('/Wishlist/removeProductFromWishlist', [WishlistsController::class, 'removeProductFromWishlist']);

    //checkout To Create Orders
    Route::post('/checkout', [CheckoutController::class, 'checkout']);
    Route::post('/cancelOrder/{order}', [CheckoutController::class, 'cancelOrder']);
});
