<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProxyController;
use App\Http\Controllers\UserController;
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

Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login'])
        ->middleware('throttle:login');

    Route::post('/register', [AuthController::class, 'register']);

    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh-token', [AuthController::class, 'refresh']);
});


Route::middleware('auth:api')->group(function () {
    Route::prefix('users')->group(function () {
        Route::put('{id}/change-password', [UserController::class, 'changePassword']);
    });

    Route::prefix('fbAccounts')->group(function () {
        Route::delete('delete-bulk', [AccountController::class, 'deleteBulk']);
        Route::put('archive-bulk', [AccountController::class, 'archiveBulk']);
        Route::put('unarchive-bulk', [AccountController::class, 'unArchiveBulk']);
    });
    Route::apiResource('fbAccounts', AccountController::class);

    Route::prefix('proxy')->group(function () {
        Route::delete('delete-bulk', [ProxyController::class, 'deleteBulk']);
    });
    Route::apiResource('proxy', ProxyController::class);
});



