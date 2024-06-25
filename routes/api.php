<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
/*
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::apiResource('/questions', QuestionController::class);
Route::apiResource('/tags', TagController::class);
Route::post('/login', [App\Http\Controllers\UserController::class, 'login']);
Route::post('/register', [App\Http\Controllers\UserController::class, 'register']);
Route::delete('/logout',[\App\Http\Controllers\UserController::class,'logout']);*/
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('/questions', QuestionController::class)->except(['index', 'show']);
    Route::apiResource('/tags', TagController::class)->except(['index', 'show']);
    Route::delete('/logout', [UserController::class, 'logout']);

});
Route::post('/login', [App\Http\Controllers\UserController::class, 'login']);
