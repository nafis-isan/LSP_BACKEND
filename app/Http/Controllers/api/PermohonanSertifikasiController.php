<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PermohonanSertifikasi;
use App\Models\Asesi;
use App\Models\JadwalUjikom;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

class PermohonanSertifikasiController extends Controller
{
    /**
     * Display a listing of all permohonan with pagination and filters
     */
    public function index(Request $request)
    {
        try {
            $perPage = $request->query('per_page', 15);
            $status = $request->query('status');
            $tipeApl = $request->query('tipe_apl');
            $asesiId = $request->query('asesi_id');
            $jadwalId = $request->query('jadwal_ujikom_id');

            $query = PermohonanSertifikasi::with(['asesi', 'jadwalUjikom']);

            if ($status) {
                $query->where('status', $status);
            }

            if ($tipeApl) {
                $query->where('tipe_apl', $tipeApl);
            }

            if ($asesiId) {
                $query->where('asesi_id', $asesiId);
            }

            if ($jadwalId) {
                $query->where('jadwal_ujikom_id', $jadwalId);
            }

            $permohonan = $query->orderBy('tanggal_permohonan', 'desc')->paginate($perPage);

            return response()->json([
                'success' => true,
                'message' => 'Data permohonan berhasil diambil',
                'data' => $permohonan->items(),
                'pagination' => [
                    'current_page' => $permohonan->currentPage(),
                    'total' => $permohonan->total(),
                    'per_page' => $permohonan->perPage(),
                    'last_page' => $permohonan->lastPage(),
                ]
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data permohonan',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Store a newly created permohonan
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'asesi_id' => 'required|exists:asesi,id',
                'jadwal_ujikom_id' => 'required|exists:jadwal_ujikom,id',
                'tipe_apl' => 'required|in:APL-01,APL-02',
                'tanggal_permohonan' => 'required|date',
                'catatan' => 'nullable|string',
                'dokumen_path' => 'nullable|string',
            ]);

            // Generate nomor permohonan unik
            $noPermohonan = $this->generateNoPermohonan($validated['tipe_apl']);

            $validated['no_permohonan'] = $noPermohonan;
            $validated['status'] = 'menunggu';

            $permohonan = PermohonanSertifikasi::create($validated);
            $permohonan->load(['asesi', 'jadwalUjikom']);

            return response()->json([
                'success' => true,
                'message' => 'Permohonan berhasil dibuat',
                'data' => $permohonan
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
                'message' => 'Gagal membuat permohonan',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified permohonan
     */
    public function show($id)
    {
        try {
            $permohonan = PermohonanSertifikasi::with(['asesi', 'jadwalUjikom'])->findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Data permohonan berhasil diambil',
                'data' => $permohonan
            ], Response::HTTP_OK);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data permohonan tidak ditemukan'
            ], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data permohonan',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update the specified permohonan
     */
    public function update(Request $request, $id)
    {
        try {
            $permohonan = PermohonanSertifikasi::findOrFail($id);

            $validated = $request->validate([
                'status' => 'nullable|in:menunggu,diterima,ditolak',
                'catatan' => 'nullable|string',
                'dokumen_path' => 'nullable|string',
            ]);

            $permohonan->update($validated);
            $permohonan->load(['asesi', 'jadwalUjikom']);

            return response()->json([
                'success' => true,
                'message' => 'Permohonan berhasil diperbarui',
                'data' => $permohonan
            ], Response::HTTP_OK);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data permohonan tidak ditemukan'
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
                'message' => 'Gagal memperbarui permohonan',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Delete the specified permohonan
     */
    public function destroy($id)
    {
        try {
            $permohonan = PermohonanSertifikasi::findOrFail($id);
            $permohonan->delete();

            return response()->json([
                'success' => true,
                'message' => 'Permohonan berhasil dihapus'
            ], Response::HTTP_OK);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data permohonan tidak ditemukan'
            ], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus permohonan',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get permohonan by tipe APL (APL-01 or APL-02)
     */
    public function byTipeApl($tipeApl)
    {
        try {
            if (!in_array($tipeApl, ['APL-01', 'APL-02'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tipe APL tidak valid. Gunakan APL-01 atau APL-02'
                ], Response::HTTP_BAD_REQUEST);
            }

            $permohonan = PermohonanSertifikasi::where('tipe_apl', $tipeApl)
                ->with(['asesi', 'jadwalUjikom'])
                ->orderBy('tanggal_permohonan', 'desc')
                ->paginate(15);

            return response()->json([
                'success' => true,
                'message' => 'Data permohonan ' . $tipeApl . ' berhasil diambil',
                'data' => $permohonan->items(),
                'pagination' => [
                    'current_page' => $permohonan->currentPage(),
                    'total' => $permohonan->total(),
                    'per_page' => $permohonan->perPage(),
                    'last_page' => $permohonan->lastPage(),
                ]
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data permohonan',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get permohonan by status
     */
    public function byStatus($status)
    {
        try {
            if (!in_array($status, ['menunggu', 'diterima', 'ditolak'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Status tidak valid. Gunakan: menunggu, diterima, atau ditolak'
                ], Response::HTTP_BAD_REQUEST);
            }

            $permohonan = PermohonanSertifikasi::where('status', $status)
                ->with(['asesi', 'jadwalUjikom'])
                ->orderBy('tanggal_permohonan', 'desc')
                ->paginate(15);

            return response()->json([
                'success' => true,
                'message' => 'Data permohonan dengan status ' . $status . ' berhasil diambil',
                'data' => $permohonan->items(),
                'pagination' => [
                    'current_page' => $permohonan->currentPage(),
                    'total' => $permohonan->total(),
                    'per_page' => $permohonan->perPage(),
                    'last_page' => $permohonan->lastPage(),
                ]
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data permohonan',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get permohonan by asesi
     */
    public function byAsesi($asesiId)
    {
        try {
            $asesi = Asesi::findOrFail($asesiId);

            $permohonan = PermohonanSertifikasi::where('asesi_id', $asesiId)
                ->with(['asesi', 'jadwalUjikom'])
                ->orderBy('tanggal_permohonan', 'desc')
                ->paginate(15);

            return response()->json([
                'success' => true,
                'message' => 'Data permohonan asesi ' . $asesi->nama . ' berhasil diambil',
                'data' => $permohonan->items(),
                'pagination' => [
                    'current_page' => $permohonan->currentPage(),
                    'total' => $permohonan->total(),
                    'per_page' => $permohonan->perPage(),
                    'last_page' => $permohonan->lastPage(),
                ]
            ], Response::HTTP_OK);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data asesi tidak ditemukan'
            ], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data permohonan',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get permohonan statistics/summary
     */
    public function summary()
    {
        try {
            $total = PermohonanSertifikasi::count();
            $menunggu = PermohonanSertifikasi::where('status', 'menunggu')->count();
            $diterima = PermohonanSertifikasi::where('status', 'diterima')->count();
            $ditolak = PermohonanSertifikasi::where('status', 'ditolak')->count();
            $apl01 = PermohonanSertifikasi::where('tipe_apl', 'APL-01')->count();
            $apl02 = PermohonanSertifikasi::where('tipe_apl', 'APL-02')->count();

            return response()->json([
                'success' => true,
                'message' => 'Ringkasan permohonan berhasil diambil',
                'data' => [
                    'total' => $total,
                    'status' => [
                        'menunggu' => $menunggu,
                        'diterima' => $diterima,
                        'ditolak' => $ditolak,
                    ],
                    'tipe_apl' => [
                        'APL-01' => $apl01,
                        'APL-02' => $apl02,
                    ]
                ]
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil ringkasan permohonan',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Generate nomor permohonan unik
     */
    private function generateNoPermohonan($tipeApl): string
    {
        $year = date('Y');
        $month = date('m');

        // Format: APL-01/YYYY/MM/000001
        $lastPermohonan = PermohonanSertifikasi::where('tipe_apl', $tipeApl)
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->orderBy('id', 'desc')
            ->first();

        $sequence = 1;
        if ($lastPermohonan) {
            $lastNo = $lastPermohonan->no_permohonan;
            $parts = explode('/', $lastNo);
            $sequence = intval(end($parts)) + 1;
        }

        return $tipeApl . '/' . $year . '/' . $month . '/' . str_pad($sequence, 6, '0', STR_PAD_LEFT);
    }
}
