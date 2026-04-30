<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Dokumen;
use App\Models\Muk;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class DokumenController extends Controller
{
    /**
     * Display a listing of dokumen for a specific MUK.
     */
    public function index(Request $request)
    {
        try {
            $query = Dokumen::query();

            // Filter berdasarkan muk_id jika diberikan
            if ($request->muk_id) {
                $query->where('muk_id', $request->muk_id);
            }

            $dokumen = $query->with('muk')->orderBy('id')->paginate(10);

            return response()->json([
                'success' => true,
                'message' => 'Data dokumen berhasil diambil',
                'data' => $dokumen->items(),
                'pagination' => [
                    'current_page' => $dokumen->currentPage(),
                    'total' => $dokumen->total(),
                    'per_page' => $dokumen->perPage(),
                    'last_page' => $dokumen->lastPage(),
                ]
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data dokumen: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get dokumen by specific MUK ID with No column.
     */
    public function byMuk($mukId)
    {
        try {
            // Verify muk exists
            $muk = Muk::with('skema')->findOrFail($mukId);

            $dokumen = Dokumen::where('muk_id', $mukId)
                ->orderBy('id')
                ->get()
                ->map(function ($dok, $index) {
                    return [
                        'no' => $index + 1,
                        'id' => $dok->id,
                        'jenis_bukti' => $dok->jenis_bukti,
                        'persyaratan' => $dok->persyaratan,
                        'ukuran_file' => $dok->ukuran_file,
                        'tipe_file' => $dok->tipe_file,
                    ];
                });

            return response()->json([
                'success' => true,
                'message' => 'Data dokumen untuk MUK ' . $muk->nama_dokumen . ' berhasil diambil',
                'muk' => [
                    'id' => $muk->id,
                    'kode_dokumen' => $muk->kode_dokumen,
                    'nama_dokumen' => $muk->nama_dokumen,
                    'skema' => [
                        'id' => $muk->skema->id,
                        'kode_skema' => $muk->skema->kode_skema,
                        'nama_skema' => $muk->skema->nama_skema,
                    ]
                ],
                'data' => $dokumen
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data dokumen: ' . $e->getMessage()
            ], Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Store a newly created dokumen in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'muk_id' => 'required|exists:muk,id',
                'jenis_bukti' => 'required|string|max:255',
                'persyaratan' => 'required|string',
                'ukuran_file' => 'nullable|integer|min:0',
                'tipe_file' => 'nullable|string|max:50',
            ]);

            $dokumen = Dokumen::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Dokumen berhasil ditambahkan',
                'data' => $dokumen->load('muk')
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
                'message' => 'Gagal menambahkan dokumen: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified dokumen.
     */
    public function show($id)
    {
        try {
            $dokumen = Dokumen::with('muk.skema')->findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Data dokumen berhasil diambil',
                'data' => [
                    'id' => $dokumen->id,
                    'muk' => [
                        'id' => $dokumen->muk->id,
                        'kode_dokumen' => $dokumen->muk->kode_dokumen,
                        'nama_dokumen' => $dokumen->muk->nama_dokumen,
                        'skema' => [
                            'id' => $dokumen->muk->skema->id,
                            'kode_skema' => $dokumen->muk->skema->kode_skema,
                            'nama_skema' => $dokumen->muk->skema->nama_skema,
                        ]
                    ],
                    'jenis_bukti' => $dokumen->jenis_bukti,
                    'persyaratan' => $dokumen->persyaratan,
                    'ukuran_file' => $dokumen->ukuran_file,
                    'tipe_file' => $dokumen->tipe_file,
                    'created_at' => $dokumen->created_at,
                    'updated_at' => $dokumen->updated_at,
                ]
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data dokumen tidak ditemukan'
            ], Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Update the specified dokumen in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $dokumen = Dokumen::findOrFail($id);

            $validated = $request->validate([
                'muk_id' => 'sometimes|required|exists:muk,id',
                'jenis_bukti' => 'sometimes|required|string|max:255',
                'persyaratan' => 'sometimes|required|string',
                'ukuran_file' => 'nullable|integer|min:0',
                'tipe_file' => 'nullable|string|max:50',
            ]);

            $dokumen->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Dokumen berhasil diperbarui',
                'data' => $dokumen->load('muk')
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
                'message' => 'Gagal memperbarui dokumen: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified dokumen from storage.
     */
    public function destroy($id)
    {
        try {
            $dokumen = Dokumen::findOrFail($id);
            $dokumen->delete();

            return response()->json([
                'success' => true,
                'message' => 'Dokumen berhasil dihapus'
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus dokumen: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
