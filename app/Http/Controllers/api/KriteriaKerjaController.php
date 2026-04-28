<?php

namespace App\Http\Controllers\Api;

use App\Models\KriteriaKerja;
use App\Models\Element;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;

class KriteriaKerjaController extends Controller
{
    /**
     * Display a listing of all kriteria kerja with pagination.
     */
    public function index()
    {
        try {
            $kriteriaKerja = KriteriaKerja::with('element')->orderBy('id')->paginate(15);

            return response()->json([
                'success' => true,
                'message' => 'Data kriteria kerja berhasil diambil',
                'data' => $kriteriaKerja->items(),
                'pagination' => [
                    'current_page' => $kriteriaKerja->currentPage(),
                    'total' => $kriteriaKerja->total(),
                    'per_page' => $kriteriaKerja->perPage(),
                    'last_page' => $kriteriaKerja->lastPage(),
                ]
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data kriteria kerja',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display listing of kriteria kerja by element.
     */
    public function byElement(string $elementId)
    {
        try {
            $element = Element::findOrFail($elementId);
            $kriteriaKerjas = KriteriaKerja::where('element_id', $elementId)->orderBy('id')->get();

            return response()->json([
                'success' => true,
                'message' => 'Data kriteria kerja berhasil diambil',
                'element' => [
                    'id' => $element->id,
                    'judul_elemen' => $element->judul_elemen,
                    'jumlah_kriteria' => count($kriteriaKerjas)
                ],
                'data' => $kriteriaKerjas
            ], Response::HTTP_OK);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Element tidak ditemukan'
            ], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data kriteria kerja',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'element_id' => 'required|exists:element,id',
                'kode_kriteria' => 'required|string|unique:kriteria_kerja,kode_kriteria',
                'uraian_kriteria' => 'required|string',
                'status' => 'required|in:aktif,nonaktif'
            ]);

            $kriteriaKerja = KriteriaKerja::create($validated);
            $kriteriaKerja->load('element');

            return response()->json([
                'success' => true,
                'message' => 'Data kriteria kerja berhasil ditambahkan',
                'data' => $kriteriaKerja
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
                'message' => 'Gagal menambahkan data kriteria kerja',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $kriteriaKerja = KriteriaKerja::with('element')->findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Data kriteria kerja berhasil diambil',
                'data' => $kriteriaKerja
            ], Response::HTTP_OK);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data kriteria kerja tidak ditemukan'
            ], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data kriteria kerja',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $kriteriaKerja = KriteriaKerja::findOrFail($id);

            $validated = $request->validate([
                'element_id' => 'sometimes|exists:element,id',
                'kode_kriteria' => 'sometimes|string|unique:kriteria_kerja,kode_kriteria,' . $id,
                'uraian_kriteria' => 'sometimes|string',
                'status' => 'sometimes|in:aktif,nonaktif'
            ]);

            $kriteriaKerja->update($validated);
            $kriteriaKerja->load('element');

            return response()->json([
                'success' => true,
                'message' => 'Data kriteria kerja berhasil diperbarui',
                'data' => $kriteriaKerja
            ], Response::HTTP_OK);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data kriteria kerja tidak ditemukan'
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
                'message' => 'Gagal memperbarui data kriteria kerja',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $kriteriaKerja = KriteriaKerja::findOrFail($id);
            $kode = $kriteriaKerja->kode_kriteria;
            $kriteriaKerja->delete();

            return response()->json([
                'success' => true,
                'message' => "Data kriteria kerja '{$kode}' berhasil dihapus"
            ], Response::HTTP_OK);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data kriteria kerja tidak ditemukan'
            ], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus data kriteria kerja',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
