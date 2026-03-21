<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\QuizController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SubjectController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\PerformanceController;
use App\Http\Controllers\Api\TopicController;


// Public auth routes — no token needed
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login',    [AuthController::class, 'login']);
});

// Protected routes — token required
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/me',      [AuthController::class, 'me']);

    // Quiz
    Route::post('/quiz/start',                    [QuizController::class, 'start']);
    Route::post('/quiz/submit',                   [QuizController::class, 'submit']);
    Route::post('/quiz/session/{id}/finish',      [QuizController::class, 'finish']);
    Route::get('/quiz/session/{id}/review',       [QuizController::class, 'review']);
     Route::get('/user/subjects', [SubjectController::class, 'index']);
     Route::get('/quiz/explanation/{questionId}', [QuizController::class, 'getExplanation']);
     Route::get('/quiz/explanation/{questionId}/stream', [QuizController::class, 'streamExplanation']);

     //dashboard
     Route::get('/dashboard', [DashboardController::class, 'index']);

     //performance
     Route::get('/performance', [PerformanceController::class, 'index']);


// Topics
Route::get('/subjects/{subjectId}/topics',  [TopicController::class, 'index']);
Route::get('/topics/{topicId}/notes',       [TopicController::class, 'notes']);

//study plan
Route::get('/performance/study-plan', [PerformanceController::class, 'studyPlan']);

// generate or return cached
    Route::get('/performance/study-plan/status', [PerformanceController::class, 'studyPlanStatus']); // dashboard card

});




