<?php

use App\Http\Controllers\Api\Auth\AuthController;
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
    Route::post('/users/{user}/teams', [UserController::class, 'assignTeam']);
    Route::delete('/users/{user}/teams', [UserController::class, 'removeTeam']);
    Route::patch('/users/{user}/status', [UserController::class, 'toggleStatus']);
    Route::get('/users/{user}/profile', [UserController::class, 'getProfile']);

    // Teams
    Route::apiResource('teams', TeamController::class);
    Route::get('/teams/{team}/members', [TeamController::class, 'members']);

});
