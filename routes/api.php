<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TextbookController;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
Route::prefix('user')->group(function () {
    Route::post('/',[UserController::class,'list']);
    Route::post('/get',[UserController::class,'get']);
    Route::post('/create',[UserController::class,'create']);
    Route::post('/update',[UserController::class,'update']);
    Route::post('/delete',[UserController::class,'delete']);
});
Route::prefix('textbook')->group(function () {
    Route::post('/',[TextbookController::class,'list']);
    Route::post('/get',[TextbookController::class,'get']);
    Route::post('/create',[TextbookController::class,'create']);
    Route::post('/update',[TextbookController::class,'update']);
    Route::post('/delete',[TextbookController::class,'delete']);
});
