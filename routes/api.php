<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Auth\StatisticsController;
use App\Http\Controllers\Api\Auth\TaskController;
use App\Http\Controllers\Api\Auth\TeamController;
use App\Http\Controllers\Api\Auth\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

// Public routes
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::post('/auth/refresh', [AuthController::class, 'refresh']);

    // Users
    Route::apiResource('users', UserController::class);
    Route::post('/users/create', [UserController::class,'store']);
    Route::post('/users/{user}/teams', [UserController::class, 'assignTeam']);
    Route::delete('/users]{user}',[UserController::class,'destroy']);
    Route::delete('/users/{user}/teams', [UserController::class, 'removeTeam']);
    Route::patch('/users/{user}/update', [UserController::class, 'update']);
    Route::patch('/users/{user}/status', [UserController::class, 'toggleStatus']);

    // Teams
    Route::apiResource('teams', TeamController::class);
    Route::post('/teams/create', [TeamController::class, 'store']);
    Route::delete('/teams/{team}/', [TeamController::class, 'destroy']);
    Route::patch('/teams/{team}/update', [TeamController::class, 'update']);
    Route::get('/teams/{team}/members', [TeamController::class, 'members']);

    // Tasks
    Route::apiResource('tasks', TaskController::class);
    Route::get('/users/{user}/tasks', [TaskController::class, 'userTasks']);
    Route::patch('/tasks/{task}/status', [TaskController::class, 'updateStatus']);
    Route::get('/teams/{team}/tasks', [TaskController::class, 'teamTasks']);
    Route::post('/tasks/{task}/assign', [TaskController::class, 'assignToUser']);

    // Statistics
    Route::get('/statistics', [StatisticsController::class, 'index']);
    Route::get('/teams/{team}/statistics', [StatisticsController::class, 'teamStats']);
    Route::get('/users/{user}/statistics', [StatisticsController::class, 'userStats']);

});
