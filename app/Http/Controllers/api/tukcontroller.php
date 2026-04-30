<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tuk;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class TukController extends Controller
{
    /**
     * Display a listing of all tuk with pagination.
     */
    public function index()
    {
        try {
            $tuk = Tuk::orderBy('id')->paginate(15);
            
            return response()->json([
                'success' => true,
                'message' => 'Data TUK berhasil diambil',
                'data' => $tuk->items(),
                'pagination' => [
                    'current_page' => $tuk->currentPage(),
                    'total' => $tuk->total(),
                    'per_page' => $tuk->perPage(),
                    'last_page' => $tuk->lastPage(),
                ]
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data TUK',
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
                'foto' => 'nullable|image|max:2048',
                'nama_tuk' => 'required|string|max:255',
                'jenis_tuk' => 'required|in:sewaktu,mandiri,tempat kerja',
                'deskripsi' => 'nullable|string',
            ]);

            if ($request->hasFile('foto')) {
                $validated['foto'] = $request->file('foto')->store('tuk', 'public');
            }

            $tuk = Tuk::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Data TUK berhasil ditambahkan',
                'data' => $tuk
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
                'message' => 'Gagal menambahkan data TUK',
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
            $tuk = Tuk::findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Data TUK berhasil diambil',
                'data' => $tuk
            ], Response::HTTP_OK);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data TUK tidak ditemukan'
            ], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data TUK',
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
            $tuk = Tuk::findOrFail($id);

            $validated = $request->validate([
                'foto' => 'nullable|image|max:2048',
                'nama_tuk' => 'sometimes|string|max:255',
                'jenis_tuk' => 'sometimes|in:sewaktu,mandiri,tempat kerja',
                'deskripsi' => 'nullable|string',
            ]);

            if ($request->hasFile('foto')) {
                if ($tuk->foto) {
                    Storage::disk('public')->delete($tuk->foto);
                }
                $validated['foto'] = $request->file('foto')->store('tuk', 'public');
            }

            $tuk->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Data TUK berhasil diperbarui',
                'data' => $tuk
            ], Response::HTTP_OK);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data TUK tidak ditemukan'
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
                'message' => 'Gagal memperbarui data TUK',
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
            $tuk = Tuk::findOrFail($id);
            $nama = $tuk->nama_tuk;

            if ($tuk->foto) {
                Storage::disk('public')->delete($tuk->foto);
            }

            $tuk->delete();

            return response()->json([
                'success' => true,
                'message' => "Data TUK '{$nama}' berhasil dihapus"
            ], Response::HTTP_OK);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data TUK tidak ditemukan'
            ], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus data TUK',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}