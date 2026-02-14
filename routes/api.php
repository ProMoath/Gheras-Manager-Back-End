<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\UserController;
use App\Http\Controllers\Api\V1\TeamController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // Public routes
    Route::prefix('auth')->group(function () {
        Route::post('register', [AuthController::class, 'register']);
        Route::post('login',    [AuthController::class, 'login']);
    });

    // Protected routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::prefix('auth')->group(function () {
            Route::post('logout',  [AuthController::class, 'logout']);
            Route::post('refresh', [AuthController::class, 'refresh']);
        });
        Route::apiResource('users', UserController::class);
        Route::prefix('users/{user}')->group(function () {
            Route::post(  'teams',  [UserController::class, 'assignTeam']);
            Route::delete('teams',  [UserController::class, 'removeTeam']);
            Route::patch( 'status', [UserController::class, 'toggleStatus']);
            Route::get(  'profile', [UserController::class, 'getProfile']);
        });
    // Teams
    Route::apiResource('teams', TeamController::class);
    Route::get('/teams/{team}/members', [TeamController::class, 'members']);
    });
});
