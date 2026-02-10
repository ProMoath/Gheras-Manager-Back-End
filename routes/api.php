<?php

// use App\Http\Controllers\Api\Auth\AuthController;
// use App\Http\Controllers\Api\Auth\StatisticsController;
// use App\Http\Controllers\Api\Auth\TaskController;
// use App\Http\Controllers\Api\Auth\TeamController;
// use App\Http\Controllers\Api\Auth\UserController;
namespace App\Http\Controllers\Api\V1;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


// Public routes
Route::group(['prefix' => 'v1', 'namespace' => 'App\Http\Controllers\Api\V1'], function () {
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);
});

// Protected routes
Route::group(['prefix' => 'v1', 'namespace' => 'App\Http\Controllers\Api\V1', 'middleware' => ['auth:sanctum']], function () {
    // Auth
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::post('/auth/refresh', [AuthController::class, 'refresh']);

    // Users
    Route::apiResource('users', UserController::class);
    Route::post('/users/{user}/teams', [UserController::class, 'assignTeam']);
    Route::delete('/users/{user}/teams', [UserController::class, 'removeTeam']);
    Route::patch('/users/{user}/status', [UserController::class, 'toggleStatus']);
});
