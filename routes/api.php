<?php

use App\Http\Controllers\Api\AsesiController;
use App\Http\Controllers\Api\AsesorController;
use App\Http\Controllers\Api\SkemaController;
use App\Http\Controllers\Api\UnitController;
use App\Http\Controllers\Api\ElementController;
use App\Http\Controllers\Api\KriteriaKerjaController;
use App\Http\Controllers\Api\TukController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\MukController;
use App\Http\Controllers\Api\DokumenController;
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


    Route::prefix('tuks')->group(function () {
        Route::get('/', [TukController::class, 'index']);
        Route::post('/', [TukController::class, 'store']);
        Route::get('{id}', [TukController::class, 'show']);
        Route::put('{id}', [TukController::class, 'update']);
        Route::patch('{id}', [TukController::class, 'update']);
        Route::delete('{id}', [TukController::class, 'destroy']);
    });

    // Skema Routes
    Route::prefix('skema')->group(function () {
        Route::get('/', [SkemaController::class, 'index']);              // List all
        Route::post('/', [SkemaController::class, 'store']);             // Create
        Route::get('{id}', [SkemaController::class, 'show']);            // Show
        Route::put('{id}', [SkemaController::class, 'update']);          // Update
        Route::patch('{id}', [SkemaController::class, 'update']);        // Update
        Route::delete('{id}', [SkemaController::class, 'destroy']);      // Delete
    });

    // Unit Routes
    Route::prefix('unit')->group(function () {
        Route::get('/', [UnitController::class, 'index']);               // List all
        Route::post('/', [UnitController::class, 'store']);              // Create
        Route::get('skema/{skemaId}', [UnitController::class, 'bySkema']); // Get units by skema
        Route::get('{id}', [UnitController::class, 'show']);             // Show
        Route::put('{id}', [UnitController::class, 'update']);           // Update
        Route::patch('{id}', [UnitController::class, 'update']);         // Update
        Route::delete('{id}', [UnitController::class, 'destroy']);       // Delete
    });

    // Element Routes
    Route::prefix('element')->group(function () {
        Route::get('/', [ElementController::class, 'index']);            // List all
        Route::post('/', [ElementController::class, 'store']);           // Create
        Route::get('unit/{unitId}', [ElementController::class, 'byUnit']); // Get elements by unit
        Route::get('{id}', [ElementController::class, 'show']);          // Show
        Route::put('{id}', [ElementController::class, 'update']);        // Update
        Route::patch('{id}', [ElementController::class, 'update']);      // Update
        Route::delete('{id}', [ElementController::class, 'destroy']);    // Delete
    });

    // Kriteria Kerja Routes
    Route::prefix('kriteria-kerja')->group(function () {
        Route::get('/', [KriteriaKerjaController::class, 'index']);      // List all
        Route::post('/', [KriteriaKerjaController::class, 'store']);     // Create
        Route::get('element/{elementId}', [KriteriaKerjaController::class, 'byElement']); // Get by element
        Route::get('{id}', [KriteriaKerjaController::class, 'show']);    // Show
        Route::put('{id}', [KriteriaKerjaController::class, 'update']);  // Update
        Route::patch('{id}', [KriteriaKerjaController::class, 'update']); // Update
        Route::delete('{id}', [KriteriaKerjaController::class, 'destroy']); // Delete
    });

    // Users Routes
    Route::prefix('users')->group(function () {
        Route::get('/', [UserController::class, 'index']);              // List all
        Route::post('/', [UserController::class, 'store']);             // Create
        Route::get('{id}', [UserController::class, 'show']);            // Show
        Route::put('{id}', [UserController::class, 'update']);          // Update
        Route::patch('{id}', [UserController::class, 'update']);        // Update
        Route::delete('{id}', [UserController::class, 'destroy']);      // Delete
        Route::post('{id}/reset-password', [UserController::class, 'resetPassword']); // Reset Password
    });

    // MUK Routes
    Route::prefix('muks')->group(function () {
        Route::get('/', [MukController::class, 'index']);              // List all with pagination
        Route::post('/', [MukController::class, 'store']);             // Create
        Route::get('skema/{skemaId}', [MukController::class, 'bySkema']); // Get by Skema ID
        Route::get('{id}/detail', [MukController::class, 'detail']);   // Get detail
        Route::get('{id}/print', [MukController::class, 'print']);     // Print single MUK
        Route::get('skema/{skemaId}/print', [MukController::class, 'printBySkema']); // Print by Skema
        Route::get('{id}', [MukController::class, 'show']);            // Show
        Route::put('{id}', [MukController::class, 'update']);          // Update
        Route::patch('{id}', [MukController::class, 'update']);        // Update
        Route::delete('{id}', [MukController::class, 'destroy']);      // Delete
    });

    // Dokumen Routes
    Route::prefix('dokumen')->group(function () {
        Route::get('/', [DokumenController::class, 'index']);          // List all with pagination
        Route::post('/', [DokumenController::class, 'store']);         // Create
        Route::get('muk/{mukId}', [DokumenController::class, 'byMuk']); // Get by MUK ID
        Route::get('{id}', [DokumenController::class, 'show']);        // Show detail
        Route::put('{id}', [DokumenController::class, 'update']);      // Update
        Route::patch('{id}', [DokumenController::class, 'update']);    // Update
        Route::delete('{id}', [DokumenController::class, 'destroy']);  // Delete
    });

    });
    
