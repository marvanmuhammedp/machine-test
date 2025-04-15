<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\ReportController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::middleware(['jwt.auth'])->group(function () {

    Route::get('user', [AuthController::class, 'user']);
    Route::get('projects', [ProjectController::class, 'index']);
    Route::post('projects/store', [ProjectController::class, 'store']);
    Route::put('projects/update/{id}', [ProjectController::class, 'update']);
    Route::delete('projects/delete/{id}', [ProjectController::class, 'destroy']);

    Route::post('projects/tasks/{projectId}', [TaskController::class, 'store']);
    Route::put('tasks/update/{id}', [TaskController::class, 'update']);
    Route::delete('tasks/delete/{id}', [TaskController::class, 'destroy']);
    Route::post('tasks/remark/{id}', [TaskController::class, 'updateStatus']);
    Route::get('projects/report/{id}', [ReportController::class, 'show']);

});