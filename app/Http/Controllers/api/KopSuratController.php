<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\KopSurat;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class KopSuratController extends Controller
{
    /**
     * Display a listing of all kop surat
     */
    public function index()
    {
        try {
            $kopSurat = KopSurat::all();

            return response()->json([
                'success' => true,
                'message' => 'Data kop surat berhasil diambil',
                'data' => $kopSurat
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data kop surat',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Store a newly created kop surat
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'nama_lembaga' => 'required|string|max:255',
                'alamat' => 'required|string',
                'no_telepon' => 'required|string|max:20',
                'email' => 'required|email|max:255',
                'website' => 'nullable|string|max:255',
                'logo' => 'nullable|string',
            ]);

            $kopSurat = KopSurat::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Kop surat berhasil ditambahkan',
                'data' => $kopSurat
            ], Response::HTTP_CREATED);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan kop surat',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified kop surat
     */
    public function show($id)
    {
        try {
            $kopSurat = KopSurat::findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Data kop surat berhasil diambil',
                'data' => $kopSurat
            ], Response::HTTP_OK);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data kop surat tidak ditemukan'
            ], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data kop surat',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update the specified kop surat
     */
    public function update(Request $request, $id)
    {
        try {
            $kopSurat = KopSurat::findOrFail($id);

            $validated = $request->validate([
                'nama_lembaga' => 'nullable|string|max:255',
                'alamat' => 'nullable|string',
                'no_telepon' => 'nullable|string|max:20',
                'email' => 'nullable|email|max:255',
                'website' => 'nullable|string|max:255',
                'logo' => 'nullable|string',
            ]);

            // Remove null values
            $validated = array_filter($validated, fn($value) => $value !== null);

            $kopSurat->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Kop surat berhasil diperbarui',
                'data' => $kopSurat
            ], Response::HTTP_OK);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data kop surat tidak ditemukan'
            ], Response::HTTP_NOT_FOUND);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui kop surat',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Delete the specified kop surat
     */
    public function destroy($id)
    {
        try {
            $kopSurat = KopSurat::findOrFail($id);
            $kopSurat->delete();

            return response()->json([
                'success' => true,
                'message' => 'Kop surat berhasil dihapus'
            ], Response::HTTP_OK);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data kop surat tidak ditemukan'
            ], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus kop surat',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get kop surat configuration (usually the first/main one)
     */
    public function getConfig()
    {
        try {
            $kopSurat = KopSurat::first();

            if (!$kopSurat) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kop surat belum dikonfigurasi'
                ], Response::HTTP_NOT_FOUND);
            }

            return response()->json([
                'success' => true,
                'message' => 'Konfigurasi kop surat berhasil diambil',
                'data' => $kopSurat
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil konfigurasi kop surat',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
