<?php

namespace App\Http\Controllers\Api;

use App\Exports\AsesorExport;
use App\Imports\AsesorImport;
use App\Models\Asesor;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\Controller;

class AsesorController extends Controller
{
    /**
     * Display a listing of all asesor with pagination.
     */
    public function index()
    {
        try {
            $asesor = Asesor::orderBy('id')->paginate(15);
            
            return response()->json([
                'success' => true,
                'message' => 'Data asesor berhasil diambil',
                'data' => $asesor->items(),
                'pagination' => [
                    'current_page' => $asesor->currentPage(),
                    'total' => $asesor->total(),
                    'per_page' => $asesor->perPage(),
                    'last_page' => $asesor->lastPage(),
                ]
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data asesor',
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
                'nama_lengkap' => 'required|string|max:255',
                'no_MET' => 'required|string|unique:asesor,no_MET|max:100',
                'akun' => 'required|string|max:255',
                'foto' => 'nullable|string',
                'status' => 'nullable|in:aktif,nonaktif',
            ]);

            $asesor = Asesor::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Data asesor berhasil ditambahkan',
                'data' => $asesor
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
                'message' => 'Gagal menambahkan data asesor',
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
            $asesor = Asesor::findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Data asesor berhasil diambil',
                'data' => $asesor
            ], Response::HTTP_OK);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data asesor tidak ditemukan'
            ], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data asesor',
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
            $asesor = Asesor::findOrFail($id);

            $validated = $request->validate([
                'nama_lengkap' => 'sometimes|string|max:255',
                'no_MET' => 'sometimes|string|unique:asesor,no_MET,' . $id . '|max:100',
                'akun' => 'sometimes|string|max:255',
                'foto' => 'nullable|string',
                'status' => 'nullable|in:aktif,nonaktif',
            ]);

            $asesor->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Data asesor berhasil diperbarui',
                'data' => $asesor
            ], Response::HTTP_OK);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data asesor tidak ditemukan'
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
                'message' => 'Gagal memperbarui data asesor',
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
            $asesor = Asesor::findOrFail($id);
            $nama = $asesor->nama_lengkap;
            $asesor->delete();

            return response()->json([
                'success' => true,
                'message' => "Data asesor '{$nama}' berhasil dihapus"
            ], Response::HTTP_OK);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data asesor tidak ditemukan'
            ], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus data asesor',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Export Asesor data to Excel.
     */
    public function export()
    {
        try {
            return Excel::download(new AsesorExport, 'asesor_' . date('Y-m-d_H-i-s') . '.xlsx');
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengekspor data asesor',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Import Asesor data from Excel.
     */
    public function import(Request $request)
    {
        try {
            $request->validate([
                'file' => 'required|file|mimes:xlsx,xls,csv',
            ]);

            Excel::import(new AsesorImport, $request->file('file'));

            return response()->json([
                'success' => true,
                'message' => 'Data asesor berhasil diimpor'
            ], Response::HTTP_OK);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengimpor data asesor',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
