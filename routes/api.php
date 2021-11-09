<?php

use App\Http\Controllers\FbController;
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
    Route::delete('delete-bulk', [FbController::class, 'deleteBulk']);
    Route::put('archive-bulk', [FbController::class, 'archiveBulk']);
    Route::put('unarchive-bulk', [FbController::class, 'unArchiveBulk']);
    Route::post('add-tags', [FbController::class, 'addTags']);
    Route::delete('remove-tags', [FbController::class, 'removeTags']);
    Route::put('change-proxy', [FbController::class, 'changeProxy']);
});
Route::apiResource('fb-accounts', FbController::class);


/*
 * Прокси
 *
 * */
Route::prefix('proxy')->group(function () {
    Route::delete('delete-bulk', [ProxyController::class, 'deleteBulk']);
    Route::post('import', [ProxyController::class, 'import']);
    Route::get('{proxy}/check', [ProxyController::class, 'check']);
});
Route::apiResource('proxy', ProxyController::class);
