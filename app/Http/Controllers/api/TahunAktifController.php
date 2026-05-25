<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TahunAktif;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TahunAktifController extends Controller
{
    /**
     * Display a listing of all tahun aktif
     */
    public function index()
    {
        try {
            $tahunAktif = TahunAktif::orderBy('tahun', 'desc')->get();

            return response()->json([
                'success' => true,
                'message' => 'Data tahun aktif berhasil diambil',
                'data' => $tahunAktif
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data tahun aktif',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Store a newly created tahun aktif
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'tahun' => 'required|string|unique:tahun_aktif',
                'semester' => 'required|string|in:Ganjil,Genap',
                'is_active' => 'nullable|boolean',
            ]);

            $tahunAktif = TahunAktif::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Tahun aktif berhasil ditambahkan',
                'data' => $tahunAktif
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
                'message' => 'Gagal menambahkan tahun aktif',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified tahun aktif
     */
    public function show($id)
    {
        try {
            $tahunAktif = TahunAktif::findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Data tahun aktif berhasil diambil',
                'data' => $tahunAktif
            ], Response::HTTP_OK);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data tahun aktif tidak ditemukan'
            ], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data tahun aktif',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update the specified tahun aktif
     */
    public function update(Request $request, $id)
    {
        try {
            $tahunAktif = TahunAktif::findOrFail($id);

            $validated = $request->validate([
                'tahun' => 'nullable|string|unique:tahun_aktif,tahun,' . $id,
                'semester' => 'nullable|string|in:Ganjil,Genap',
                'is_active' => 'nullable|boolean',
            ]);

            // Remove null values
            $validated = array_filter($validated, fn($value) => $value !== null);

            $tahunAktif->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Tahun aktif berhasil diperbarui',
                'data' => $tahunAktif
            ], Response::HTTP_OK);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data tahun aktif tidak ditemukan'
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
                'message' => 'Gagal memperbarui tahun aktif',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Delete the specified tahun aktif
     */
    public function destroy($id)
    {
        try {
            $tahunAktif = TahunAktif::findOrFail($id);
            $tahunAktif->delete();

            return response()->json([
                'success' => true,
                'message' => 'Tahun aktif berhasil dihapus'
            ], Response::HTTP_OK);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data tahun aktif tidak ditemukan'
            ], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus tahun aktif',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get active tahun aktif
     */
    public function getActive()
    {
        try {
            $tahunAktif = TahunAktif::where('is_active', true)->first();

            if (!$tahunAktif) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada tahun aktif yang dipilih'
                ], Response::HTTP_NOT_FOUND);
            }

            return response()->json([
                'success' => true,
                'message' => 'Tahun aktif berhasil diambil',
                'data' => $tahunAktif
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil tahun aktif',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Set tahun aktif as active (set others to inactive)
     */
    public function setActive($id)
    {
        try {
            $tahunAktif = TahunAktif::findOrFail($id);

            // Set all to inactive
            TahunAktif::query()->update(['is_active' => false]);

            // Set selected to active
            $tahunAktif->update(['is_active' => true]);

            return response()->json([
                'success' => true,
                'message' => 'Tahun aktif berhasil diaktifkan',
                'data' => $tahunAktif
            ], Response::HTTP_OK);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data tahun aktif tidak ditemukan'
            ], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengaktifkan tahun aktif',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
