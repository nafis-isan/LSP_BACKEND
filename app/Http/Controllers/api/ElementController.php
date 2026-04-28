<?php

namespace App\Http\Controllers\Api;

use App\Models\Element;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;

class ElementController extends Controller
{
    /**
     * Display a listing of all element with pagination.
     */
    public function index()
    {
        try {
            $element = Element::with('unit', 'kriteriaKerjas')->orderBy('id')->paginate(15);

            // Add kriteria count to each element
            $elementData = $element->items();
            foreach ($elementData as $e) {
                $e->jumlah_kriteria = count($e->kriteriaKerjas);
            }

            return response()->json([
                'success' => true,
                'message' => 'Data elemen berhasil diambil',
                'data' => $elementData,
                'pagination' => [
                    'current_page' => $element->currentPage(),
                    'total' => $element->total(),
                    'per_page' => $element->perPage(),
                    'last_page' => $element->lastPage(),
                ]
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data elemen',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display listing of element by unit.
     */
    public function byUnit(string $unitId)
    {
        try {
            $unit = Unit::findOrFail($unitId);
            $elements = Element::where('unit_id', $unitId)->orderBy('id')->get();

            return response()->json([
                'success' => true,
                'message' => 'Data elemen berhasil diambil',
                'unit' => [
                    'id' => $unit->id,
                    'kode_unit' => $unit->kode_unit,
                    'nama_unit' => $unit->nama_unit,
                    'jumlah_elemen' => count($elements)
                ],
                'data' => $elements
            ], Response::HTTP_OK);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Unit tidak ditemukan'
            ], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data elemen',
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
                'unit_id' => 'required|exists:unit,id',
                'judul_elemen' => 'required|string|max:255',
                'kriteria_unjuk_kerja' => 'nullable|string',
                'status' => 'required|in:aktif,nonaktif'
            ]);

            $element = Element::create($validated);
            $element->load('unit');

            return response()->json([
                'success' => true,
                'message' => 'Data elemen berhasil ditambahkan',
                'data' => $element
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
                'message' => 'Gagal menambahkan data elemen',
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
            $element = Element::with('unit')->findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Data elemen berhasil diambil',
                'data' => $element
            ], Response::HTTP_OK);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data elemen tidak ditemukan'
            ], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data elemen',
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
            $element = Element::findOrFail($id);

            $validated = $request->validate([
                'unit_id' => 'sometimes|exists:unit,id',
                'judul_elemen' => 'sometimes|string|max:255',
                'kriteria_unjuk_kerja' => 'nullable|string',
                'status' => 'sometimes|in:aktif,nonaktif'
            ]);

            $element->update($validated);
            $element->load('unit');

            return response()->json([
                'success' => true,
                'message' => 'Data elemen berhasil diperbarui',
                'data' => $element
            ], Response::HTTP_OK);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data elemen tidak ditemukan'
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
                'message' => 'Gagal memperbarui data elemen',
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
            $element = Element::findOrFail($id);
            $judul = $element->judul_elemen;
            $element->delete();

            return response()->json([
                'success' => true,
                'message' => "Data elemen '{$judul}' berhasil dihapus"
            ], Response::HTTP_OK);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data elemen tidak ditemukan'
            ], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus data elemen',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
