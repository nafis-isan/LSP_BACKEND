<?php

namespace App\Http\Controllers\Api;

use App\Models\Unit;
use App\Models\Skema;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;

class UnitController extends Controller
{
    /**
     * Display a listing of all unit with pagination.
     */
    public function index()
    {
        try {
            $unit = Unit::with('skema', 'elements')->orderBy('id')->paginate(15);

            // Add element count to each unit
            $unitData = $unit->items();
            foreach ($unitData as $u) {
                $u->jumlah_elemen = count($u->elements);
            }

            return response()->json([
                'success' => true,
                'message' => 'Data unit berhasil diambil',
                'data' => $unitData,
                'pagination' => [
                    'current_page' => $unit->currentPage(),
                    'total' => $unit->total(),
                    'per_page' => $unit->perPage(),
                    'last_page' => $unit->lastPage(),
                ]
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data unit',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display listing of unit by skema.
     */
    public function bySkema(string $skemaId)
    {
        try {
            $skema = Skema::findOrFail($skemaId);
            $units = Unit::where('skema_id', $skemaId)->with('elements')->orderBy('id')->get();

            // Add element count to each unit
            foreach ($units as $u) {
                $u->jumlah_elemen = count($u->elements);
            }

            return response()->json([
                'success' => true,
                'message' => 'Data unit berhasil diambil',
                'skema' => [
                    'id' => $skema->id,
                    'kode_skema' => $skema->kode_skema,
                    'nama_skema' => $skema->nama_skema,
                    'jumlah_unit' => count($units)
                ],
                'data' => $units
            ], Response::HTTP_OK);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Skema tidak ditemukan'
            ], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data unit',
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
                'skema_id' => 'required|exists:skema,id',
                'kode_unit' => 'required|string|unique:unit,kode_unit',
                'nama_unit' => 'required|string|max:255',
                'jenis_standar' => 'required|in:SKKNI,standar khusus,standar internasional',
                'jumlah_elemen' => 'nullable|integer|min:0',
                'deskripsi' => 'nullable|string',
                'status' => 'required|in:aktif,nonaktif'
            ]);

            $unit = Unit::create($validated);
            $unit->load('skema');

            return response()->json([
                'success' => true,
                'message' => 'Data unit berhasil ditambahkan',
                'data' => $unit
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
                'message' => 'Gagal menambahkan data unit',
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
            $unit = Unit::with('skema', 'elements')->findOrFail($id);
            $unit->jumlah_elemen = count($unit->elements);

            return response()->json([
                'success' => true,
                'message' => 'Data unit berhasil diambil',
                'data' => $unit
            ], Response::HTTP_OK);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data unit tidak ditemukan'
            ], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data unit',
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
            $unit = Unit::findOrFail($id);

            $validated = $request->validate([
                'skema_id' => 'sometimes|exists:skema,id',
                'kode_unit' => 'sometimes|string|unique:unit,kode_unit,' . $id,
                'nama_unit' => 'sometimes|string|max:255',
                'jenis_standar' => 'sometimes|in:SKKNI,standar khusus,standar internasional',
                'jumlah_elemen' => 'nullable|integer|min:0',
                'deskripsi' => 'nullable|string',
                'status' => 'sometimes|in:aktif,nonaktif'
            ]);

            $unit->update($validated);
            $unit->load('skema');

            return response()->json([
                'success' => true,
                'message' => 'Data unit berhasil diperbarui',
                'data' => $unit
            ], Response::HTTP_OK);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data unit tidak ditemukan'
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
                'message' => 'Gagal memperbarui data unit',
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
            $unit = Unit::findOrFail($id);
            $nama = $unit->nama_unit;
            $unit->delete();

            return response()->json([
                'success' => true,
                'message' => "Data unit '{$nama}' berhasil dihapus"
            ], Response::HTTP_OK);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data unit tidak ditemukan'
            ], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus data unit',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
