<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Middleware\IsAdmin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// --- Публічні маршрути ---
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// --- Захищені маршрути (вимагають Bearer token) ---
Route::middleware('auth:sanctum')->group(function () {
    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    Route::post('/user/wallet', [App\Http\Controllers\Api\UserController::class, 'updateWallet']);

    // Маршрути для Виборів
    Route::get('/elections', [App\Http\Controllers\Api\ElectionController::class, 'index']);
    Route::get('/elections/{id}', [App\Http\Controllers\Api\ElectionController::class, 'show']);

    // Вкладені маршрути (контекст виборів)
    Route::get('/elections/{election_id}/candidates', [App\Http\Controllers\Api\CandidateController::class, 'index']);
    // Маршрути для роботи з конкретним кандидатом
    Route::get('/candidates/{id}', [App\Http\Controllers\Api\CandidateController::class, 'show']);
    Route::put('/candidates/{id}', [App\Http\Controllers\Api\CandidateController::class, 'update']);
    Route::delete('/candidates/{id}', [App\Http\Controllers\Api\CandidateController::class, 'destroy']);

    // --- Маршрути для Голосування ---
    Route::get('/elections/{id}/vote-status', [App\Http\Controllers\Api\VoteController::class, 'status']);
    Route::post('/elections/{id}/vote', [App\Http\Controllers\Api\VoteController::class, 'store']);
    Route::get('/my-votes', [App\Http\Controllers\Api\VoteController::class, 'history']);
});

Route::middleware(['auth:sanctum', IsAdmin::class])->group(function () {
    // Створення та редагування виборів
    Route::post('/elections', [App\Http\Controllers\Api\ElectionController::class, 'store']);
    Route::put('/elections/{id}', [App\Http\Controllers\Api\ElectionController::class, 'update']);
    Route::delete('/elections/{id}', [App\Http\Controllers\Api\ElectionController::class, 'destroy']);

    // Додавання кандидатів
    Route::post('/elections/{election_id}/candidates', [App\Http\Controllers\Api\CandidateController::class, 'store']);

    // Статистика
    Route::get('/admin/stats', [App\Http\Controllers\Api\DashboardController::class, 'stats']);

    Route::get('/users/search', [App\Http\Controllers\Api\UserController::class, 'search']);
});