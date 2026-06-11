<?php

namespace App\Http\Controllers\Api;

use App\Models\JadwalUjikom;
use App\Models\GuruActivityLog;
use Illuminate\Http\Request;

class GuruJadwalUjikomController
{
    /**
     * Get jadwal ujikom untuk guru
     */
    public function index(Request $request)
    {
        $asesorId = $request->user()->asesor_id ?? null;

        if (!$asesorId) {
            return response()->json([
                'message' => 'Guru tidak ditemukan',
            ], 404);
        }

        try {
            GuruActivityLog::create([
                'asesor_id' => $asesorId,
                'activity_type' => 'view_jadwal_ujikom',
                'description' => 'Lihat jadwal ujikom',
                'ip_address' => $request->ip(),
            ]);

            $query = JadwalUjikom::where('asesor_id', $asesorId)
                ->with(['skema', 'tuk', 'asesi']);

            // Filter by status
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            // Filter by skema
            if ($request->filled('skema_id')) {
                $query->where('skema_id', $request->skema_id);
            }

            // Filter by tuk
            if ($request->filled('tuk_id')) {
                $query->where('tuk_id', $request->tuk_id);
            }

            $jadwal = $query->orderBy('tanggal_ujian', 'asc')
                ->paginate($request->get('per_page', 20));

            return response()->json([
                'message' => 'Jadwal ujikom loaded',
                'data' => $jadwal->map(function ($j) {
                    return [
                        'id' => $j->id,
                        'tanggal_ujian' => $j->tanggal_ujian,
                        'waktu_mulai' => $j->waktu_mulai,
                        'waktu_selesai' => $j->waktu_selesai,
                        'kuota' => $j->kuota,
                        'peserta_terdaftar' => $j->peserta_terdaftar,
                        'status' => $j->status,
                        'skema' => [
                            'id' => $j->skema?->id,
                            'nama_skema' => $j->skema?->nama_skema,
                        ],
                        'tuk' => [
                            'id' => $j->tuk?->id,
                            'nama_tuk' => $j->tuk?->nama_tuk,
                            'lokasi' => $j->tuk?->lokasi,
                        ],
                        'asesi_count' => $j->asesi?->count() ?? 0,
                    ];
                }),
                'pagination' => [
                    'total' => $jadwal->total(),
                    'per_page' => $jadwal->perPage(),
                    'current_page' => $jadwal->currentPage(),
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error loading jadwal',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get detail jadwal ujikom
     */
    public function show(Request $request, $jadwalId)
    {
        $asesorId = $request->user()->asesor_id ?? null;

        if (!$asesorId) {
            return response()->json([
                'message' => 'Guru tidak ditemukan',
            ], 404);
        }

        try {
            GuruActivityLog::create([
                'asesor_id' => $asesorId,
                'activity_type' => 'view_jadwal_detail',
                'description' => 'Lihat detail jadwal ujikom',
                'ip_address' => $request->ip(),
                'data' => ['jadwal_ujikom_id' => $jadwalId],
            ]);

            $jadwal = JadwalUjikom::where('asesor_id', $asesorId)
                ->with(['skema', 'tuk', 'asesi'])
                ->findOrFail($jadwalId);

            return response()->json([
                'message' => 'Jadwal detail loaded',
                'data' => [
                    'id' => $jadwal->id,
                    'tanggal_ujian' => $jadwal->tanggal_ujian,
                    'waktu_mulai' => $jadwal->waktu_mulai,
                    'waktu_selesai' => $jadwal->waktu_selesai,
                    'kuota' => $jadwal->kuota,
                    'peserta_terdaftar' => $jadwal->peserta_terdaftar,
                    'status' => $jadwal->status,
                    'catatan' => $jadwal->catatan,
                    'skema' => $jadwal->skema,
                    'tuk' => $jadwal->tuk,
                    'asesi' => $jadwal->asesi->map(function ($a) {
                        return [
                            'id' => $a->id,
                            'nama_lengkap' => $a->nama_lengkap,
                            'nomor_peserta' => $a->nomor_peserta,
                        ];
                    }),
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error loading jadwal detail',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
