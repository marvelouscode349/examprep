<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\QuizController;
use App\Http\Controllers\Api\SubjectController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\PaystackController;
use App\Http\Controllers\Api\PerformanceController;
use App\Http\Controllers\Api\TopicController;
use Illuminate\Support\Facades\Route;

// PUBLIC ROUTES
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login',    [AuthController::class, 'login']);
});

// AUTH REQUIRED
Route::middleware('auth:sanctum')->group(function () {

    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/me',      [AuthController::class, 'me']);

    // FREE ROUTES
    Route::get('/user/subjects', [SubjectController::class, 'index']);
    Route::get('/subjects/{subjectId}/topics', [TopicController::class, 'index']);
    Route::get('/topics/{topicId}/notes',      [TopicController::class, 'notes']);
    Route::get('/dashboard', [DashboardController::class, 'index']);
    
    // Payments (FREE)
    Route::post('/subscription/validate-code', [PaystackController::class, 'validateCode']);
    Route::post('/subscription/initialize',    [PaystackController::class, 'initialize']);
    Route::post('/subscription/verify',        [PaystackController::class, 'verify']);
    Route::get('/subscription/status',         [PaystackController::class, 'status']);

    // ✅ PREMIUM‑PROTECTED ROUTES
    Route::middleware('premium')->group(function () {
        // Quiz
        Route::post('/quiz/start',                 [QuizController::class, 'start']);
        Route::post('/quiz/submit',                [QuizController::class, 'submit']);
        Route::post('/quiz/session/{id}/finish',   [QuizController::class, 'finish']);
        Route::get('/quiz/session/{id}/review',    [QuizController::class, 'review']);

        // AI explanations
        Route::get('/quiz/explanation/{questionId}',        [QuizController::class, 'getExplanation']);
        Route::get('/quiz/explanation/{questionId}/stream', [QuizController::class, 'streamExplanation']);

        // Performance
        Route::get('/performance', [PerformanceController::class, 'index']);

        // Study plan
        Route::get('/performance/study-plan', [PerformanceController::class, 'studyPlan']);
        Route::get('/performance/study-plan/status', [PerformanceController::class, 'studyPlanStatus']);
    });
});

// PAYSTACK WEBHOOK — PUBLIC
Route::post('/webhook/paystack', [PaystackController::class, 'webhook']);
Route::middleware(['auth:sanctum', 'premium'])->get('/test-premium', function () {
    return 'PREMIUM OK';
});