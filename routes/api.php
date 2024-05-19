<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\CategoryConstroller;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('login', [LoginController::class, 'login']);
Route::post('register', [LoginController::class, 'register']);

Route::middleware('auth.admin')->group(function() {
    Route::get('categories', [CategoryConstroller::class, 'showCategory']);
    Route::post('categories', [CategoryConstroller::class, 'addCategory']);
    Route::put('categories/{id}', [CategoryConstroller::class, 'updateCategory']);
    Route::delete('categories/{id}', [CategoryConstroller::class, 'deleteCategory']);
});
Route::middleware('auth.jwt')->group(function() {
    Route::get('products', [ProductController::class, 'showProduct']);
    Route::post('products', [ProductController::class, 'addProduct']);
    Route::put('products/{id}', [ProductController::class, 'updateProduct']);
    Route::delete('products/{id}', [ProductController::class, 'deleteProduct']);
});
Route::middleware('web')->group(function() {
Route::get('oauth/register', [LoginController::class, 'redirectGoogle']);
Route::get('oauth/callback', [LoginController::class, 'callbackGoogle']);
});