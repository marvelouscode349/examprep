<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\QuizController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SubjectController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\PaystackController;
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
    Route::get('/performance/study-plan/status', [PerformanceController::class, 'studyPlanStatus']);
    
    // dashboard card

    //Payments


Route::middleware('auth:sanctum')->group(function () {

    // Validate discount code
    Route::post('/subscription/validate-code', [PaystackController::class, 'validateCode']);

    // Initialize payment
    Route::post('/subscription/initialize', [PaystackController::class, 'initialize']);

    // Verify payment
    Route::post('/subscription/verify', [PaystackController::class, 'verify']);

    // Subscription status
    Route::get('/subscription/status', [PaystackController::class, 'status']);

});

// Webhook (NO AUTH)
Route::post('/webhook/paystack', [PaystackController::class, 'webhook']);

});




