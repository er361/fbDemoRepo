<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\FbAccountController;
use App\Http\Controllers\Api\V1\ProxyController;
use App\Http\Controllers\Api\V1\UserController;
use App\Libraries\FbFetchBase;
use App\Models\FbAccount;
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

    Route::post('logout', [AuthController::class, 'logout'])
        ->name('logout');

    Route::post('/register', [AuthController::class, 'register']);

    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh-token', [AuthController::class, 'refresh']);
});


Route::middleware('auth:api')->group(function () {
    /*
     * Пользователи
     *
     * */
//    Route::get('/profile', [AuthController::class, 'userProfile']);

    Route::prefix('users')->group(function () {
        Route::put('{id}/change-password', [UserController::class, 'changePassword']);

        Route::delete('delete-bulk', [UserController::class, 'deleteBulk']);
        Route::put('restore-bulk', [UserController::class, 'restoreBulk']);

        Route::put('add-tags', [UserController::class, 'addTags']);
        Route::delete('remove-tags', [UserController::class, 'removeTags']);

        Route::get('tags', [UserController::class, 'tags']);
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
        Route::put('{fb_account}/save-notes', [FbAccountController::class, 'saveNotes']);
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
    $account = FbAccount::withoutGlobalScopes()->firstWhere('access_token', 'like', 'EAABs%');
    $fbFetchBase = new FbFetchBase($account);
    $fbFetchBase->process();
//    DB::enableQueryLog();
//    return \App\Models\FbAdAccount::with('insights')->first();
//    dd(DB::getQueryLog());
    return $account->load(
        'pages',
        'adAccounts',
        'adAccounts.insights',
        'adAccounts.apps',
        'adAccounts.campaigns',
        'adAccounts.adsets',
        'adAccounts.ads',
    );
//    DB::enableQueryLog();
//    $collection = \App\Models\FbAdAccount::with('insights',)->get();
//    dd(DB::getQueryLog());
//    return $collection;
});
