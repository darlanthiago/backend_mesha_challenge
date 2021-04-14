<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\ServiceController;
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


    Route::group(['middleware' => ['auth:sanctum']], function () {

        Route::get('/me', [UserController::class, 'me']);

        Route::group(['middleware' => ['sanctum.abilities:admin']], function () {

            Route::post('/register', [UserController::class, 'store']);

            Route::get('/', [UserController::class, 'index']);
        });


        Route::get('/{id}', [UserController::class, 'show']);
        Route::put('/{id}', [UserController::class, 'update']);
        Route::delete('/{id}', [UserController::class, 'destroy']);
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

Route::group(['prefix' => 'service'], function () {

    Route::group(['middleware' => ['auth:sanctum']], function () {

        Route::group(['middleware' => ['sanctum.abilities:admin']], function () {

            Route::post('/', [ServiceController::class, 'store']);
            Route::get('/', [ServiceController::class, 'index']);
        });
    });
});
