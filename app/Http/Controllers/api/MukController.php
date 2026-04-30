<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Muk;
use App\Models\Skema;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class MukController extends Controller
{
    /**
     * Display a listing of MUK for a specific skema with pagination.
     */
    public function index(Request $request)
    {
        try {
            $query = Muk::query();

            // Filter berdasarkan skema_id jika diberikan
            if ($request->skema_id) {
                $query->where('skema_id', $request->skema_id);
            }

            $muks = $query->with('skema')->orderBy('id')->paginate(10);

            return response()->json([
                'success' => true,
                'message' => 'Data MUK berhasil diambil',
                'data' => $muks->items(),
                'pagination' => [
                    'current_page' => $muks->currentPage(),
                    'total' => $muks->total(),
                    'per_page' => $muks->perPage(),
                    'last_page' => $muks->lastPage(),
                ]
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data MUK: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get MUK by specific skema ID with No column.
     */
    public function bySkema($skemaId)
    {
        try {
            // Verify skema exists
            $skema = Skema::findOrFail($skemaId);

            $muks = Muk::where('skema_id', $skemaId)
                ->orderBy('id')
                ->get()
                ->map(function ($muk, $index) {
                    return [
                        'no' => $index + 1,
                        'id' => $muk->id,
                        'kode_dokumen' => $muk->kode_dokumen,
                        'nama_dokumen' => $muk->nama_dokumen,
                        'keterangan' => $muk->keterangan,
                    ];
                });

            return response()->json([
                'success' => true,
                'message' => 'Data MUK untuk skema ' . $skema->nama_skema . ' berhasil diambil',
                'skema' => [
                    'id' => $skema->id,
                    'kode_skema' => $skema->kode_skema,
                    'nama_skema' => $skema->nama_skema,
                ],
                'data' => $muks
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data MUK: ' . $e->getMessage()
            ], Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Store a newly created MUK in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'skema_id' => 'required|exists:skema,id',
                'kode_dokumen' => 'required|unique:muk,kode_dokumen',
                'nama_dokumen' => 'required|string|max:255',
                'keterangan' => 'nullable|string',
            ]);

            $muk = Muk::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'MUK berhasil ditambahkan',
                'data' => $muk->load('skema')
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
                'message' => 'Gagal menambahkan MUK: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified MUK.
     */
    public function show($id)
    {
        try {
            $muk = Muk::with('skema')->findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Data MUK berhasil diambil',
                'data' => $muk
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data MUK tidak ditemukan'
            ], Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Update the specified MUK in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $muk = Muk::findOrFail($id);

            $validated = $request->validate([
                'skema_id' => 'sometimes|required|exists:skema,id',
                'kode_dokumen' => 'sometimes|required|unique:muk,kode_dokumen,' . $id,
                'nama_dokumen' => 'sometimes|required|string|max:255',
                'keterangan' => 'nullable|string',
            ]);

            $muk->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'MUK berhasil diperbarui',
                'data' => $muk->load('skema')
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
                'message' => 'Gagal memperbarui MUK: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified MUK from storage.
     */
    public function destroy($id)
    {
        try {
            $muk = Muk::findOrFail($id);
            $muk->delete();

            return response()->json([
                'success' => true,
                'message' => 'MUK berhasil dihapus'
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus MUK: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get detail MUK with full information.
     */
    public function detail($id)
    {
        try {
            $muk = Muk::with('skema')->findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Detail MUK berhasil diambil',
                'data' => [
                    'id' => $muk->id,
                    'skema' => [
                        'id' => $muk->skema->id,
                        'kode_skema' => $muk->skema->kode_skema,
                        'nama_skema' => $muk->skema->nama_skema,
                        'jenjang' => $muk->skema->jenjang,
                    ],
                    'kode_dokumen' => $muk->kode_dokumen,
                    'nama_dokumen' => $muk->nama_dokumen,
                    'keterangan' => $muk->keterangan,
                    'created_at' => $muk->created_at,
                    'updated_at' => $muk->updated_at,
                ]
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Detail MUK tidak ditemukan'
            ], Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Print MUK data in a printable format.
     */
    public function print($id)
    {
        try {
            $muk = Muk::with('skema')->findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Data MUK siap dicetak',
                'print_data' => [
                    'title' => 'DETAIL MUK (Modul Uji Kompetensi)',
                    'skema_info' => [
                        'kode' => $muk->skema->kode_skema,
                        'nama' => $muk->skema->nama_skema,
                        'jenjang' => $muk->skema->jenjang,
                    ],
                    'dokumen_info' => [
                        'no_urut' => $muk->id,
                        'kode_dokumen' => $muk->kode_dokumen,
                        'nama_dokumen' => $muk->nama_dokumen,
                        'keterangan' => $muk->keterangan,
                    ],
                    'timestamp' => now()->format('d-m-Y H:i:s')
                ]
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data untuk print: ' . $e->getMessage()
            ], Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Print MUK data by Skema ID in a printable format.
     */
    public function printBySkema($skemaId)
    {
        try {
            $skema = Skema::findOrFail($skemaId);

            $muks = Muk::where('skema_id', $skemaId)
                ->orderBy('id')
                ->get()
                ->map(function ($muk, $index) {
                    return [
                        'no' => $index + 1,
                        'kode_dokumen' => $muk->kode_dokumen,
                        'nama_dokumen' => $muk->nama_dokumen,
                        'keterangan' => $muk->keterangan,
                    ];
                });

            return response()->json([
                'success' => true,
                'message' => 'Data MUK siap dicetak',
                'print_data' => [
                    'title' => 'DATA MUK (Modul Uji Kompetensi)',
                    'skema_info' => [
                        'kode_skema' => $skema->kode_skema,
                        'nama_skema' => $skema->nama_skema,
                        'jenjang' => $skema->jenjang,
                    ],
                    'table_headers' => ['No', 'Kode Dokumen', 'Nama Dokumen', 'Keterangan'],
                    'table_data' => $muks,
                    'total_records' => count($muks),
                    'timestamp' => now()->format('d-m-Y H:i:s')
                ]
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data untuk print: ' . $e->getMessage()
            ], Response::HTTP_NOT_FOUND);
        }
    }
}
