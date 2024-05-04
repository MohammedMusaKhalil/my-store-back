<?php

use App\Http\Controllers\API\OrdersController;
use App\Http\Controllers\Api\ProductsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!

sanctum - passport
|
*/

// not auth requests
Route::middleware('guest')->group(function () {
    // Register
    Route::post('register', [RegisteredUserController::class, 'store']);
    
    // Login
    Route::put('login', [AuthenticatedSessionController::class, 'store']);
    
    // Get Products
    Route::get('products', [ProductsController::class, 'GetProducts']);
    
    // Get Categories
    
    Route::get('categories', [ProductsController::class, 'GetCategories']);
    Route::get('statistics', [ProductsController::class, 'statistics']);
});

// auth requests
Route::middleware('auth:sanctum')->group(function () {
    
    //add User
    Route::post('registeruser', [RegisteredUserController::class, 'createUser']);
    Route::post('create', [RegisteredUserController::class, 'createUser']);
     //delete user
    Route::delete('users/{id}', [UserController::class, 'destroy']);
    //update user
    Route::put('editUsers/{id}',[UserController::class,'edite']);
    //show users (admin)
    Route::get('users',[UserController::class,'show']);
    Route::get('user', function (Request $request) {
        return $request->user();
    });

    //add catigore
    Route::post('add-categories', [ProductsController::class, 'createCategories']);
    //delete catigory
    Route::delete('deletecategory/{id}', [ProductsController::class, 'deleteCategory']);
    //update catigory
    Route::put('editcategory/{id}',[ProductsController::class,'updateCategory']);

    //Create orders
    Route::post('addorders', [OrdersController::class, 'store']);
    //show order (admin)
    Route::get('ordersadmin', [OrdersController::class, 'indexAdmin']);
    //show order (user)
    Route::get('ordersuser', [OrdersController::class, 'indexUser']);
    //update order
    Route::put('updateorders/{id}', [OrdersController::class, 'update']);
    //delete order
    Route::delete('deleteOrder/{id}', [OrdersController::class, 'deleteOrder']);

    //delete product
    Route::delete('products/{id}', [ProductsController::class, 'destroy']);
    //add product
    Route::post('add-product', [ProductsController::class, 'store']);
    //update product
    Route::put('updateproduct/{id}', [ProductsController::class, 'update']);
    
    //  Logout User
    Route::put('logout', [AuthenticatedSessionController::class, 'logout']);
});
