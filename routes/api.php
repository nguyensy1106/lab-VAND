<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\StoreController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['middleware' => 'api', 'prefix' => 'auth'], function() {
    Route::post('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/login', [AuthController::class, 'login'])->name('login');
});
Route::group(['middleware' => 'auth.jwt', 'prefix' => 'auth'], function() {
    Route::get('/profile', [AuthController::class, 'profile']);
});
Route::group(['middleware' => 'auth.jwt'], function() {
    Route::get('stores/get-list', [StoreController::class, 'getList']);
    Route::apiResource('stores', StoreController::class)->except(['index', 'create', 'edit']);
});
Route::group(['middleware' => 'auth.jwt'], function() {
    Route::get('products/get-list', [ProductController::class, 'getList']);
    Route::apiResource('products', ProductController::class)->except(['index', 'create', 'edit']);
});
