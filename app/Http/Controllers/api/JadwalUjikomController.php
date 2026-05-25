<?php

namespace App\Http\Controllers\Api;

use App\Models\JadwalUjikom;
use App\Models\Skema;
use App\Models\Tuk;
use App\Models\TahunAktif;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;

class JadwalUjikomController extends Controller
{
    /**
     * Display a listing of all jadwal ujikom with pagination.
     */
    public function index(Request $request)
    {
        try {
            $perPage = $request->query('per_page', 15);
            $status = $request->query('status');
            $skemaId = $request->query('skema_id');
            $tukId = $request->query('tuk_id');
            $tahunAktifId = $request->query('tahun_aktif_id');

            $query = JadwalUjikom::with(['skema', 'tuk', 'tahunAktif']);

            if ($status) {
                $query->where('status', $status);
            }

            if ($skemaId) {
                $query->where('skema_id', $skemaId);
            }

            if ($tukId) {
                $query->where('tuk_id', $tukId);
            }

            if ($tahunAktifId) {
                $query->where('tahun_aktif_id', $tahunAktifId);
            }

            $jadwals = $query->orderBy('tanggal_mulai', 'asc')->paginate($perPage);

            return response()->json([
                'success' => true,
                'message' => 'Data jadwal ujikom berhasil diambil',
                'data' => $jadwals->items(),
                'pagination' => [
                    'current_page' => $jadwals->currentPage(),
                    'total' => $jadwals->total(),
                    'per_page' => $jadwals->perPage(),
                    'last_page' => $jadwals->lastPage(),
                ]
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data jadwal ujikom',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Store a newly created jadwal ujikom in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'skema_id' => 'required|exists:skema,id',
                'tuk_id' => 'required|exists:tuks,id',
                'tahun_aktif_id' => 'required|exists:tahun_aktif,id',
                'tanggal_mulai' => 'required|date',
                'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
                'waktu_mulai' => 'required|date_format:H:i:s',
                'waktu_selesai' => 'required|date_format:H:i:s',
                'kuota' => 'required|integer|min:1',
                'status' => 'required|in:dibuka,ditutup,selesai,dibatalkan',
                'keterangan' => 'nullable|string'
            ]);

            $jadwalUjikom = JadwalUjikom::create($validated);
            $jadwalUjikom->load(['skema', 'tuk', 'tahunAktif']);

            return response()->json([
                'success' => true,
                'message' => 'Jadwal ujikom berhasil ditambahkan',
                'data' => $jadwalUjikom
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
                'message' => 'Gagal menambahkan jadwal ujikom',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified jadwal ujikom.
     */
    public function show($id)
    {
        try {
            $jadwalUjikom = JadwalUjikom::with(['skema', 'tuk', 'tahunAktif', 'permohonan.asesi'])->findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Data jadwal ujikom berhasil diambil',
                'data' => $jadwalUjikom
            ], Response::HTTP_OK);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Jadwal ujikom tidak ditemukan'
            ], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data jadwal ujikom',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update the specified jadwal ujikom in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $jadwalUjikom = JadwalUjikom::findOrFail($id);

            $validated = $request->validate([
                'skema_id' => 'sometimes|required|exists:skema,id',
                'tuk_id' => 'sometimes|required|exists:tuks,id',
                'tahun_aktif_id' => 'sometimes|required|exists:tahun_aktif,id',
                'tanggal_mulai' => 'sometimes|required|date',
                'tanggal_selesai' => 'sometimes|required|date|after_or_equal:tanggal_mulai',
                'waktu_mulai' => 'sometimes|required|date_format:H:i:s',
                'waktu_selesai' => 'sometimes|required|date_format:H:i:s',
                'kuota' => 'sometimes|required|integer|min:1',
                'status' => 'sometimes|required|in:dibuka,ditutup,selesai,dibatalkan',
                'keterangan' => 'nullable|string'
            ]);

            $jadwalUjikom->update($validated);
            $jadwalUjikom->load(['skema', 'tuk', 'tahunAktif']);

            return response()->json([
                'success' => true,
                'message' => 'Jadwal ujikom berhasil diperbarui',
                'data' => $jadwalUjikom
            ], Response::HTTP_OK);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Jadwal ujikom tidak ditemukan'
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
                'message' => 'Gagal memperbarui jadwal ujikom',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified jadwal ujikom from storage.
     */
    public function destroy($id)
    {
        try {
            $jadwalUjikom = JadwalUjikom::findOrFail($id);
            $jadwalUjikom->delete();

            return response()->json([
                'success' => true,
                'message' => 'Jadwal ujikom berhasil dihapus'
            ], Response::HTTP_OK);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Jadwal ujikom tidak ditemukan'
            ], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus jadwal ujikom',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get jadwal ujikom by skema.
     */
    public function bySkema($skemaId)
    {
        try {
            $jadwals = JadwalUjikom::with(['skema', 'tuk', 'tahunAktif'])
                ->where('skema_id', $skemaId)
                ->where('status', 'dibuka')
                ->orderBy('tanggal_mulai', 'asc')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Data jadwal ujikom berdasarkan skema berhasil diambil',
                'data' => $jadwals
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data jadwal ujikom',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get jadwal ujikom by TUK.
     */
    public function byTuk($tukId)
    {
        try {
            $jadwals = JadwalUjikom::with(['skema', 'tuk', 'tahunAktif'])
                ->where('tuk_id', $tukId)
                ->where('status', 'dibuka')
                ->orderBy('tanggal_mulai', 'asc')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Data jadwal ujikom berdasarkan TUK berhasil diambil',
                'data' => $jadwals
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data jadwal ujikom',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get available kuota for jadwal ujikom.
     */
    public function getAvailableKuota($id)
    {
        try {
            $jadwalUjikom = JadwalUjikom::findOrFail($id);
            $registeredCount = $jadwalUjikom->permohonan()->count();
            $availableKuota = $jadwalUjikom->kuota - $registeredCount;

            return response()->json([
                'success' => true,
                'message' => 'Data kuota berhasil diambil',
                'data' => [
                    'id' => $jadwalUjikom->id,
                    'kuota' => $jadwalUjikom->kuota,
                    'registered' => $registeredCount,
                    'available_kuota' => $availableKuota
                ]
            ], Response::HTTP_OK);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Jadwal ujikom tidak ditemukan'
            ], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data kuota',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get metadata for creating/editing jadwal ujikom.
     */
    public function getMetadata()
    {
        try {
            $skemas = Skema::where('status', 'aktif')->select('id', 'nama_skema')->get();
            $tuks = Tuk::where('status', 'aktif')->select('id', 'nama_tuk')->get();
            $tahunAktif = TahunAktif::where('is_active', true)->select('id', 'tahun')->first();

            return response()->json([
                'success' => true,
                'message' => 'Metadata berhasil diambil',
                'data' => [
                    'skemas' => $skemas,
                    'tuks' => $tuks,
                    'tahun_aktif' => $tahunAktif,
                    'status_options' => ['dibuka', 'ditutup', 'selesai', 'dibatalkan']
                ]
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil metadata',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
