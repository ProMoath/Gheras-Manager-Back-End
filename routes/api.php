<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\StatisticsController;
use App\Http\Controllers\Api\TaskController;
use App\Http\Controllers\Api\TeamController;
use App\Http\Controllers\Api\UserController;
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
Route::post('auth/register', [AuthController::class, 'register']);
Route::post('auth/login', [AuthController::class, 'login']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    //Auth
    Route::post('auth/logout', [AuthController::class, 'logout']);
    Route::post('auth/refresh', [AuthController::class, 'refresh']);

    // Users
    Route::apiResource('users', UserController::class);
    Route::post('users/{user}/teams/{team}', [UserController::class, 'assignTeam']);

    // Teams
    Route::apiResource('teams', TeamController::class,);
    Route::get('/teams/{team}/members', [TaskController::class, 'members']);
    Route::get('/teams/{team}/tasks/', [TaskController::class, 'teamTasks']);

    // Tasks
/*    Route::apiResource('tasks', TaskController::class);
    Route::get('users/{user}/tasks', [TaskController::class, 'userTasks']);

    // Statistics
    Route::get('statistics',[StatisticsController::class,'index']);
    Route::get('teams/{team}/statistics', [StatisticsController::class, 'teamStats']);
    Route::get('users/{user}/statistics', [StatisticsController::class, 'userStats']);
*/

}
);


