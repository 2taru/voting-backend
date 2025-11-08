<?php

use App\Http\Controllers\Api\AuthController;
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

    // Маршрути для Виборів
    Route::get('/elections', [App\Http\Controllers\Api\ElectionController::class, 'index']);
    Route::post('/elections', [App\Http\Controllers\Api\ElectionController::class, 'store']);
    Route::get('/elections/{id}', [App\Http\Controllers\Api\ElectionController::class, 'show']);
    Route::put('/elections/{id}', [App\Http\Controllers\Api\ElectionController::class, 'update']);
    Route::delete('/elections/{id}', [App\Http\Controllers\Api\ElectionController::class, 'destroy']);

    // Вкладені маршрути (контекст виборів)
    Route::get('/elections/{election_id}/candidates', [App\Http\Controllers\Api\CandidateController::class, 'index']);
    Route::post('/elections/{election_id}/candidates', [App\Http\Controllers\Api\CandidateController::class, 'store']);
    // Маршрути для роботи з конкретним кандидатом
    Route::get('/candidates/{id}', [App\Http\Controllers\Api\CandidateController::class, 'show']);
    Route::put('/candidates/{id}', [App\Http\Controllers\Api\CandidateController::class, 'update']);
    Route::delete('/candidates/{id}', [App\Http\Controllers\Api\CandidateController::class, 'destroy']);

    // --- Маршрути для Голосування ---
    Route::get('/elections/{id}/vote-status', [App\Http\Controllers\Api\VoteController::class, 'status']);
    Route::post('/elections/{id}/vote', [App\Http\Controllers\Api\VoteController::class, 'store']);
});