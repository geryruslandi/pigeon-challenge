<?php

use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\OrderController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::post('/login')->uses([AuthenticationController::class, 'login'])->name('login');

Route::middleware('auth:sanctum')->group(function() {

    Route::post('/logout')->uses([AuthenticationController::class, 'logout'])->name('logout');

    Route::controller(OrderController::class)
        ->prefix('orders')
        ->name('orders.')
        ->group(function() {

            Route::post('/', 'store')->name('store');
            Route::post('/{orderId}/finish', 'finish')->name('finish');

        });
});
