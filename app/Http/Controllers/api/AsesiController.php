<?php

namespace App\Http\Controllers\Api;

use App\Exports\AsesiExport;
use App\Imports\AsesiImport;
use App\Models\Asesi;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\Controller;

class AsesiController extends Controller
{
    /**
     * Display a listing of all asesi with pagination.
     */
    public function index()
    {
        try {
            $asesi = Asesi::orderBy('id')->paginate(15);
            
            return response()->json([
                'success' => true,
                'message' => 'Data asesi berhasil diambil',
                'data' => $asesi->items(),
                'pagination' => [
                    'current_page' => $asesi->currentPage(),
                    'total' => $asesi->total(),
                    'per_page' => $asesi->perPage(),
                    'last_page' => $asesi->lastPage(),
                ]
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data asesi',
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
                'no_peserta' => 'required|string|unique:asesi,no_peserta',
                'nama' => 'required|string|max:255',
                'kelas' => 'nullable|string|max:100',
                'tahun_aktif' => 'nullable|integer|min:2000|max:2099',
                'nama_pengguna' => 'nullable|string|max:255',
                'foto' => 'nullable|string',
            ]);

            $asesi = Asesi::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Data asesi berhasil ditambahkan',
                'data' => $asesi
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
                'message' => 'Gagal menambahkan data asesi',
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
            $asesi = Asesi::findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Data asesi berhasil diambil',
                'data' => $asesi
            ], Response::HTTP_OK);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data asesi tidak ditemukan'
            ], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data asesi',
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
            $asesi = Asesi::findOrFail($id);

            $validated = $request->validate([
                'no_peserta' => 'sometimes|string|unique:asesi,no_peserta,' . $id,
                'nama' => 'sometimes|string|max:255',
                'kelas' => 'nullable|string|max:100',
                'tahun_aktif' => 'nullable|integer|min:2000|max:2099',
                'nama_pengguna' => 'nullable|string|max:255',
                'foto' => 'nullable|string',
            ]);

            $asesi->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Data asesi berhasil diperbarui',
                'data' => $asesi
            ], Response::HTTP_OK);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data asesi tidak ditemukan'
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
                'message' => 'Gagal memperbarui data asesi',
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
            $asesi = Asesi::findOrFail($id);
            $nama = $asesi->nama;
            $asesi->delete();

            return response()->json([
                'success' => true,
                'message' => "Data asesi '{$nama}' berhasil dihapus"
            ], Response::HTTP_OK);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data asesi tidak ditemukan'
            ], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus data asesi',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Export Asesi data to Excel.
     */
    public function export()
    {
        try {
            return Excel::download(new AsesiExport, 'asesi_' . date('Y-m-d_H-i-s') . '.xlsx');
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengekspor data asesi',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Import Asesi data from Excel.
     */
    public function import(Request $request)
    {
        try {
            $request->validate([
                'file' => 'required|file|mimes:xlsx,xls,csv',
            ]);

            Excel::import(new AsesiImport, $request->file('file'));

            return response()->json([
                'success' => true,
                'message' => 'Data asesi berhasil diimpor'
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
                'message' => 'Gagal mengimpor data asesi',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
