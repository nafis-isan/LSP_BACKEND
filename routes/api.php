<?php

use App\Http\Controllers\Api\AsesiController;
use App\Http\Controllers\Api\AsesorController;
use Illuminate\Support\Facades\Route;           

Route::middleware('api')->group(function () {
    // Asesi Routes
    Route::prefix('asesi')->group(function () {
        Route::get('/', [AsesiController::class, 'index']);              // List all
        Route::post('/', [AsesiController::class, 'store']);             // Create
        Route::get('{id}', [AsesiController::class, 'show']);            // Show
        Route::put('{id}', [AsesiController::class, 'update']);          // Update
        Route::delete('{id}', [AsesiController::class, 'destroy']);      // Delete
        Route::get('export', [AsesiController::class, 'exportData']);    // Export
    });
    
    // Asesor Routes
    Route::prefix('asesor')->group(function () {
        Route::get('/', [AsesorController::class, 'index']);              // List all
        Route::post('/', [AsesorController::class, 'store']);             // Create
        Route::get('{id}', [AsesorController::class, 'show']);            // Show
        Route::put('{id}', [AsesorController::class, 'update']);          // Update
        Route::delete('{id}', [AsesorController::class, 'destroy']);      // Delete
        Route::get('export', [AsesorController::class, 'exportData']);    // Export
    });
});
