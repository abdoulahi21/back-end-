<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\QuestionCommentController;


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
// routes/api.php
Route::middleware('auth:sanctum')->get('/user/questions', [App\Http\Controllers\QuestionController::class, 'userQuestions']);

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('question-comments', QuestionCommentController::class);
    Route::apiResource('/questions', QuestionController::class)->except(['index', 'show']);
    Route::apiResource('/tags', TagController::class)->except(['index', 'show']);
    Route::delete('/logout', [UserController::class, 'logout']);
});
Route::apiResource('/questions', QuestionController::class);
Route::post('/login', [App\Http\Controllers\UserController::class, 'login']);
Route::get('/users',[UserController::class,'index']);
Route::apiResource('question-comments', QuestionCommentController::class);

Route::apiResource('/questions', QuestionController::class)->only(['index', 'show']);
Route::apiResource('/tags', TagController::class)->only(['index', 'show']);
Route::post('/login', [UserController::class, 'login']);
Route::post('/register', [UserController::class, 'register']);
