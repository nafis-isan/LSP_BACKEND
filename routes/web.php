<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'message' => 'LSP Backend API is running.',
    ]);
});

// Fallback supaya redirect bawaan Laravel saat request API tidak terautentikasi
// (tanpa header Accept: application/json) tetap balas JSON, bukan error 500.
Route::get('/login', function () {
    return response()->json(['success' => false, 'message' => 'Unauthenticated.'], 401);
})->name('login');