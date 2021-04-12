<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\UserController;

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

Route::group(['prefix' => '/user'], function () {

    Route::post('/register', [UserController::class, 'store']);

    Route::group(['middleware' => ['auth:sanctum']], function () {

        Route::get('/me', [UserController::class, 'me']);
    });
});


Route::group(['prefix' => 'auth'], function () {

    Route::post('/login', [AuthController::class, 'store']);

    Route::put('/forgot-password', [AuthController::class, 'update']);

    Route::get('/password-reset-token/{token}', [PasswordResetController::class, 'show']);

    Route::put('/password-reset', [PasswordResetController::class, 'update']);

    Route::group(['middleware' => ['auth:sanctum']], function () {

        Route::delete('/logout', [AuthController::class, 'destroy']);
    });
});
