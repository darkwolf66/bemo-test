<?php

use App\Http\Controllers\BoardManager;
use App\Http\Controllers\DbDumper;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::middleware('require_token_auth')->group(function (){
    Route::get('/board/{id}', [BoardManager::class, 'getBoard']);
    Route::post('/board/{id}/update-cards', [BoardManager::class, 'updateCards']);
    Route::post('/board/{id}/card/update', [BoardManager::class, 'updateCard']);
    Route::post('/board/{id}/card/new', [BoardManager::class, 'newCard']);
    Route::post('/board/{id}/column/new', [BoardManager::class, 'createColumn']);
    Route::delete('/board/{id}/column/{columnId}', [BoardManager::class, 'deleteColumn']);
    Route::get('/boards', [BoardManager::class, 'myBoards']);

    Route::get('/db-dumper', [DbDumper::class, 'downloadDb']);

    Route::get('/list-cards', [BoardManager::class, 'listCards']);

});
