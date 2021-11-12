<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\FbAccountController;
use App\Http\Controllers\Api\V1\ProxyController;
use App\Http\Controllers\Api\V1\UserController;
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


Route::middleware('auth:api')->group(function () {
    /*
     * Пользователи
     *
     * */
    Route::get('/profile', [AuthController::class, 'userProfile']);

    Route::prefix('users')->group(function () {
        Route::put('{id}/change-password', [UserController::class, 'changePassword']);
        Route::delete('delete-bulk', [UserController::class, 'deleteBulk']);
        Route::put('add-tags', [UserController::class, 'addTags']);
        Route::delete('remove-tags', [UserController::class, 'removeTags']);
    });
    Route::apiResource('users', UserController::class);

    /*
     * Аккаунты
     *
     * */
    Route::prefix('fb-accounts')->group(function () {
        Route::delete('delete-bulk', [FbAccountController::class, 'deleteBulk']);
        Route::put('archive-bulk', [FbAccountController::class, 'archiveBulk']);
        Route::put('unarchive-bulk', [FbAccountController::class, 'unArchiveBulk']);
        Route::post('add-tags', [FbAccountController::class, 'addTags']);
        Route::delete('remove-tags', [FbAccountController::class, 'removeTags']);
        Route::put('change-proxy', [FbAccountController::class, 'changeProxy']);
        Route::put('add-permissions', [FbAccountController::class, 'addPermissions']);
        Route::delete('remove-permissions', [FbAccountController::class, 'removePermissions']);
    });
    Route::apiResource('fb-accounts', FbAccountController::class);


    /*
     * Прокси
     *
     * */
    Route::prefix('proxy')->group(function () {
        Route::delete('delete-bulk', [ProxyController::class, 'deleteBulk']);
        Route::post('import', [ProxyController::class, 'import']);
        Route::get('{proxy}/check', [ProxyController::class, 'check']);
        Route::put('add-permissions', [ProxyController::class, 'addPermissions']);
        Route::delete('remove-permissions', [ProxyController::class, 'removePermissions']);
    });
    Route::apiResource('proxy', ProxyController::class);
});


Route::get('test', function () {
    \Illuminate\Support\Facades\DB::enableQueryLog();
    $builder = \App\Models\User::query()->with('teamleads', 'subordinates');
    return $builder->find('5340e952-e62f-4e56-a504-412d55387960');
//    dd(\Illuminate\Support\Facades\DB::getQueryLog());
});
