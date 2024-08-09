<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\QualificationsController;
use App\Http\Controllers\CoursesController;
use App\Http\Controllers\StatesController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\TokenValidationController;


Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    // Route::post('logout', [AuthController::class, 'logout']);
    
    // // Add other routes that require authentication here
    // Route::get('clients', [ClientController::class, 'show']);
    // Route::post('clients', [ClientController::class, 'store']);
    // Route::put('/clients/{id}', [ClientController::class, 'update']);

    // Route::get('courses', [CoursesController::class, 'show']);
    // Route::post('courses', [CoursesController::class, 'store']);

    // Route::get('qualifications', [QualificationsController::class, 'show']);
    // Route::post('qualifications', [QualificationsController::class, 'store']);

    // Route::get('admin', [AdminController::class, 'show']);
    // Route::post('admin', [AdminController::class, 'store']);

    // Route::get('states', [StatesController::class, 'show']);

    Route::post('/validate-token', [TokenValidationController::class, 'validateToken']);
});


Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    
    // Add other routes that require authentication here
    Route::get('clients', [ClientController::class, 'show']);
    Route::post('clients', [ClientController::class, 'store']);
    Route::put('/clients/{client_id}', [ClientController::class, 'update']);

    Route::get('courses', [CoursesController::class, 'show']);
    Route::post('courses', [CoursesController::class, 'store']);

    Route::get('qualifications', [QualificationsController::class, 'show']);
    Route::post('qualifications', [QualificationsController::class, 'store']);

    Route::get('admin', [AdminController::class, 'show']);
    Route::post('admin', [AdminController::class, 'store']);

    Route::get('states', [StatesController::class, 'show']);

});

Route::options('/{any}', function () {
    return response('', 200);
})->where('any', '.*');

Route::middleware('auth:sanctum')->get('/test-token', function (Request $request) {
    return response()->json(['message' => 'Token is valid.']);
});