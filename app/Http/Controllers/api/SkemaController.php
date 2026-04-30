<?php

namespace App\Http\Controllers\Api;

use App\Models\Skema;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;

class SkemaController extends Controller
{
    /**
     * Display a listing of all skema with pagination.
     */
    public function index()
    {
        try {
            $skema = Skema::with('units')->orderBy('id')->paginate(10);

            // Add unit count to each skema
            $skemaData = $skema->items();
            foreach ($skemaData as $s) {
                $s->jumlah_unit = count($s->units);
            }

            return response()->json([
                'success' => true,
                'message' => 'Data skema berhasil diambil',
                'data' => $skemaData,
                'pagination' => [
                    'current_page' => $skema->currentPage(),
                    'total' => $skema->total(),
                    'per_page' => $skema->perPage(),
                    'last_page' => $skema->lastPage(),
                ]
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data skema',
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
                'kode_skema' => 'required|string|unique:skema,kode_skema',
                'nama_skema' => 'required|string|max:255',
                'jenjang' => 'required|string|max:100',
                'jenis_skema' => 'required|in:KKNI,okupasi,klaster',
                'file_skema' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
                'jumlah_unit' => 'nullable|integer|min:0',
                'deskripsi' => 'nullable|string',
                'status' => 'required|in:aktif,nonaktif'
            ]);

            // Handle file upload
            if ($request->hasFile('file_skema')) {
                $file = $request->file('file_skema');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $file->storeAs('skema', $fileName, 'public');
                $validated['file_skema'] = $fileName;
            }

            $skema = Skema::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Data skema berhasil ditambahkan',
                'data' => $skema
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
                'message' => 'Gagal menambahkan data skema',
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
            $skema = Skema::with('units')->findOrFail($id);
            $skema->jumlah_unit = count($skema->units);

            return response()->json([
                'success' => true,
                'message' => 'Data skema berhasil diambil',
                'data' => $skema
            ], Response::HTTP_OK);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data skema tidak ditemukan'
            ], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data skema',
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
            $skema = Skema::findOrFail($id);

            $validated = $request->validate([
                'kode_skema' => 'sometimes|string|unique:skema,kode_skema,' . $id,
                'nama_skema' => 'sometimes|string|max:255',
                'jenjang' => 'sometimes|string|max:100',
                'jenis_skema' => 'sometimes|in:KKNI,okupasi,klaster',
                'file_skema' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
                'jumlah_unit' => 'nullable|integer|min:0',
                'deskripsi' => 'nullable|string',
                'status' => 'sometimes|in:aktif,nonaktif'
            ]);

            // Handle file upload
            if ($request->hasFile('file_skema')) {
                // Delete old file if exists
                if ($skema->file_skema && file_exists(storage_path('app/public/skema/' . $skema->file_skema))) {
                    unlink(storage_path('app/public/skema/' . $skema->file_skema));
                }

                $file = $request->file('file_skema');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $file->storeAs('skema', $fileName, 'public');
                $validated['file_skema'] = $fileName;
            }

            $skema->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Data skema berhasil diperbarui',
                'data' => $skema
            ], Response::HTTP_OK);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data skema tidak ditemukan'
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
                'message' => 'Gagal memperbarui data skema',
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
            $skema = Skema::findOrFail($id);
            $nama = $skema->nama_skema;

            // Delete file if exists
            if ($skema->file_skema && file_exists(storage_path('app/public/skema/' . $skema->file_skema))) {
                unlink(storage_path('app/public/skema/' . $skema->file_skema));
            }

            $skema->delete();

            return response()->json([
                'success' => true,
                'message' => "Data skema '{$nama}' berhasil dihapus"
            ], Response::HTTP_OK);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data skema tidak ditemukan'
            ], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus data skema',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
