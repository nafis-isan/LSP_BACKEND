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
use App\Http\Controllers\Api\JadwalUjikomController;
use App\Http\Controllers\Api\PermohonanSertifikasiController;
use App\Http\Controllers\Api\TahunAktifController;
use App\Http\Controllers\Api\KopSuratController;
use App\Http\Controllers\Api\AsesorSkemaController;
use App\Http\Controllers\Api\GuruDashboardController;
use App\Http\Controllers\Api\GuruPenilaianController;
use App\Http\Controllers\Api\GuruJadwalUjikomController;
use App\Http\Controllers\Api\GuruReferensiController;
use App\Http\Controllers\Api\GuruActivityLogController;
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

    // Profile Routes (untuk current authenticated user)
    Route::middleware('auth:sanctum')->prefix('profile')->group(function () {
        Route::get('/', [UserController::class, 'profile']);                    // Get profile
        Route::put('/', [UserController::class, 'updateProfile']);              // Update profile
        Route::put('password', [UserController::class, 'changePassword']);      // Change password
        Route::get('permissions', [UserController::class, 'getPermissions']);   // Get permissions
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

    // Jadwal Ujikom Routes
    Route::prefix('jadwal-ujikom')->group(function () {
        Route::get('/', [JadwalUjikomController::class, 'index']);              // List all with pagination & filters
        Route::post('/', [JadwalUjikomController::class, 'store']);             // Create
        Route::get('metadata', [JadwalUjikomController::class, 'getMetadata']); // Get metadata for form
        Route::get('skema/{skemaId}', [JadwalUjikomController::class, 'bySkema']); // Get by Skema
        Route::get('tuk/{tukId}', [JadwalUjikomController::class, 'byTuk']);    // Get by TUK
        Route::get('{id}/kuota', [JadwalUjikomController::class, 'getAvailableKuota']); // Get available kuota
        Route::get('{id}', [JadwalUjikomController::class, 'show']);            // Show detail
        Route::put('{id}', [JadwalUjikomController::class, 'update']);          // Update
        Route::patch('{id}', [JadwalUjikomController::class, 'update']);        // Update
        Route::delete('{id}', [JadwalUjikomController::class, 'destroy']);      // Delete
    });

    // Permohonan Sertifikasi Routes (APL-01, APL-02)
    Route::prefix('permohonan')->group(function () {
        Route::get('/', [PermohonanSertifikasiController::class, 'index']);                      // List all with filters
        Route::post('/', [PermohonanSertifikasiController::class, 'store']);                     // Create
        Route::get('summary', [PermohonanSertifikasiController::class, 'summary']);              // Get summary/statistics
        Route::get('tipe/{tipeApl}', [PermohonanSertifikasiController::class, 'byTipeApl']);     // Get by tipe APL (APL-01/APL-02)
        Route::get('status/{status}', [PermohonanSertifikasiController::class, 'byStatus']);     // Get by status
        Route::get('asesi/{asesiId}', [PermohonanSertifikasiController::class, 'byAsesi']);      // Get by asesi
        Route::get('{id}', [PermohonanSertifikasiController::class, 'show']);                    // Show detail
        Route::put('{id}', [PermohonanSertifikasiController::class, 'update']);                  // Update
        Route::patch('{id}', [PermohonanSertifikasiController::class, 'update']);                // Update
        Route::delete('{id}', [PermohonanSertifikasiController::class, 'destroy']);              // Delete
    });

    // Tahun Aktif Routes (Pengaturan)
    Route::prefix('tahun-aktif')->group(function () {
        Route::get('/', [TahunAktifController::class, 'index']);                                 // List all
        Route::post('/', [TahunAktifController::class, 'store']);                                // Create
        Route::get('active', [TahunAktifController::class, 'getActive']);                        // Get active tahun aktif
        Route::get('{id}', [TahunAktifController::class, 'show']);                               // Show detail
        Route::put('{id}', [TahunAktifController::class, 'update']);                             // Update
        Route::patch('{id}', [TahunAktifController::class, 'update']);                           // Update
        Route::put('{id}/set-active', [TahunAktifController::class, 'setActive']);               // Set as active
        Route::delete('{id}', [TahunAktifController::class, 'destroy']);                         // Delete
    });

    // Kop Surat Routes (Pengaturan)
    Route::prefix('kop-surat')->group(function () {
        Route::get('/', [KopSuratController::class, 'index']);                                   // List all
        Route::post('/', [KopSuratController::class, 'store']);                                  // Create
        Route::get('config', [KopSuratController::class, 'getConfig']);                          // Get konfigurasi (first record)
        Route::get('{id}', [KopSuratController::class, 'show']);                                 // Show detail
        Route::put('{id}', [KopSuratController::class, 'update']);                               // Update
        Route::patch('{id}', [KopSuratController::class, 'update']);                             // Update
        Route::delete('{id}', [KopSuratController::class, 'destroy']);                           // Delete
    });

    // Asesor Skema Routes (Pengaturan - Penugasan Asesor ke Skema)
    Route::prefix('asesor-skema')->group(function () {
        Route::get('skema/{skemaId}/asesor', [AsesorSkemaController::class, 'getAsesorBySkema']);              // Get asesor by skema
        Route::get('asesor/{asesorId}/skema', [AsesorSkemaController::class, 'getSkemaByAsesor']);            // Get skema by asesor
        Route::post('assign', [AsesorSkemaController::class, 'assignAsesorToSkema']);                         // Assign single asesor
        Route::post('assign-multiple', [AsesorSkemaController::class, 'assignMultipleAsesorToSkema']);        // Assign multiple asesor
        Route::delete('{asesorId}/skema/{skemaId}', [AsesorSkemaController::class, 'removeAsesorFromSkema']); // Remove single asesor
        Route::post('remove-multiple', [AsesorSkemaController::class, 'removeMultipleAsesorFromSkema']);      // Remove multiple asesor
        Route::post('sync', [AsesorSkemaController::class, 'syncAsesorToSkema']);                             // Sync (replace all)
        Route::get('check/{asesorId}/skema/{skemaId}', [AsesorSkemaController::class, 'checkAssignment']);    // Check assignment
    });

    // ===== GURU ROUTES (Halaman Guru) =====
    // Guru Dashboard
    Route::prefix('guru')->group(function () {
        Route::get('dashboard', [GuruDashboardController::class, 'index']);                                    // Dashboard summary

        // Jadwal Ujikom yang ditugaskan ke guru
        Route::prefix('jadwal-ujikom')->group(function () {
            Route::get('/', [GuruJadwalUjikomController::class, 'index']);                                     // List jadwal guru
            Route::get('{id}', [GuruJadwalUjikomController::class, 'show']);                                   // Show jadwal detail
        });

        // Penilaian/Nilai Asesi
        Route::prefix('penilaian')->group(function () {
            Route::get('asesi', [GuruPenilaianController::class, 'getAsesiList']);                            // Get daftar asesi
            Route::get('asesi/{asesiId}', [GuruPenilaianController::class, 'getByAsesi']);                    // Get penilaian by asesi
            Route::post('/', [GuruPenilaianController::class, 'store']);                                      // Input/update nilai
            Route::patch('{id}/complete', [GuruPenilaianController::class, 'complete']);                      // Complete penilaian
        });

        // Referensi Data (Skema, Unit, Element, Kriteria, MUK)
        Route::prefix('referensi')->group(function () {
            Route::get('skema', [GuruReferensiController::class, 'getSkema']);                                // Get daftar skema
            Route::get('skema/{skemaId}/detail', [GuruReferensiController::class, 'getSkemaDetail']);         // Get detail skema
            Route::get('skema/{skemaId}/download', [GuruReferensiController::class, 'downloadSkemaFile']);    // Download file skema
            Route::get('skema/{skemaId}/unit', [GuruReferensiController::class, 'getUnitBySkema']);           // Get unit by skema
            Route::get('unit/{unitId}/element', [GuruReferensiController::class, 'getElementByUnit']);        // Get element by unit
            Route::get('element/{elementId}/kriteria', [GuruReferensiController::class, 'getKriteriaByElement']); // Get kriteria
            Route::get('skema/{skemaId}/muk', [GuruReferensiController::class, 'getMukBySkema']);             // Get MUK by skema
        });
    });

    // ===== ADMIN MONITORING GURU ROUTES =====
    Route::prefix('admin/guru-monitoring')->group(function () {
        Route::get('activity-logs', [GuruActivityLogController::class, 'index']);                             // Get all activity logs
        Route::get('activity-logs/summary', [GuruActivityLogController::class, 'summary']);                   // Get activity summary
        Route::get('activity-logs/statistics', [GuruActivityLogController::class, 'statistics']);             // Get statistics
        Route::get('activity-logs/asesor/{asesorId}', [GuruActivityLogController::class, 'byGuru']);          // Get activity by specific guru
    });

    });

