<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\TeamController;
use App\Http\Controllers\Api\V1\UserController;
//use App\Http\Controllers\Api\V1\StatisticsController;
use App\Http\Controllers\Api\V1\TaskController;
//use App\Http\Controllers\Api\V1\TeamController;
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
            Route::post( 'teams',   [UserController::class, 'assignTeam']);
            Route::delete('teams',  [UserController::class, 'removeTeam']);
            Route::patch( 'status', [UserController::class, 'toggleStatus']);
            Route::get(   'profile',[UserController::class, 'getProfile']);
            Route::get(   'tasks',  [TaskController::class, 'userTasks']);
        });
    Route::apiResource('teams', TeamController::class);
        Route::prefix('teams/{team}')->group(function () {
            Route::get('members', [TeamController::class, 'members']);
            Route::get('tasks',   [TaskController::class, 'teamTasks']);
        });
        Route::apiResource('tasks', TaskController::class);
        Route::prefix('tasks/{task}')->group(function () {
            Route::patch( 'status', [TaskController::class, 'updateStatus']);
            Route::patch(  'assign', [TaskController::class, 'assignToUser']);
            Route::delete( 'unassign', [TaskController::class, 'removeFromUser']);
            Route::patch(  'team', [TaskController::class, 'assignToTeam']);
        });
    });
});
