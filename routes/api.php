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

// Route pour obtenir les informations de l'utilisateur authentifié
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Route pour obtenir les questions de l'utilisateur authentifié
Route::middleware('auth:sanctum')->get('/user/questions', [QuestionController::class, 'userQuestions']);

// Routes nécessitant une authentification
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('/question-comments', QuestionCommentController::class);
    Route::post('comments/{id}/like', [QuestionCommentController::class, 'like'])->name('comments.like');
    Route::patch('question-comments/{id}/valider', [QuestionCommentController::class, 'valider']);
    Route::apiResource('/questions', QuestionController::class)->except(['index', 'show']);
    Route::apiResource('/tags', TagController::class)->except(['index', 'show']);
    Route::delete('/logout', [UserController::class, 'logout']);
    Route::get('/users', [UserController::class, 'index']);
});

// Routes accessibles sans authentification
Route::apiResource('/questions', QuestionController::class)->only(['index', 'show']);
Route::apiResource('/tags', TagController::class)->only(['index', 'show']);
Route::post('/login', [UserController::class, 'login']);
Route::post('/register', [UserController::class, 'register']);

