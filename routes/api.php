<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProxyController;
use App\Http\Controllers\UserController;
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

/*
 * Авторизация / Регистрация
 *
 * */
Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login'])
        ->middleware('throttle:login');

    Route::post('/register', [AuthController::class, 'register']);

    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh-token', [AuthController::class, 'refresh']);
});

/*
 * Пользователи
 *
 * */
Route::middleware('auth:api')->group(function () {
    Route::get('/profile', [AuthController::class, 'userProfile']);
    Route::prefix('users')->group(function () {
        Route::put('{id}/change-password', [UserController::class, 'changePassword']);
    });
});

/*
 * Аккаунты
 *
 * */
Route::prefix('fb-accounts')->group(function () {
    Route::delete('delete-bulk', [AccountController::class, 'deleteBulk']);
    Route::put('archive-bulk', [AccountController::class, 'archiveBulk']);
    Route::put('unarchive-bulk', [AccountController::class, 'unArchiveBulk']);
    Route::post('add-tags', [AccountController::class, 'addTags']);
    Route::delete('remove-tags', [AccountController::class, 'removeTags']);
    Route::put('change-proxy', [AccountController::class, 'changeProxy']);
});
Route::apiResource('fb-accounts', AccountController::class);


/*
 * Прокси
 *
 * */
Route::prefix('proxy')->group(function () {
    Route::delete('delete-bulk', [ProxyController::class, 'deleteBulk']);
});
Route::apiResource('proxy', ProxyController::class);
