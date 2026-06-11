<?php

namespace App\Http\Controllers\Api;

use App\Models\Asesor;
use App\Models\GuruActivityLog;
use App\Models\GuruPenilaian;
use App\Models\JadwalUjikom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GuruDashboardController
{
    /**
     * Get guru dashboard summary
     */
    public function index(Request $request)
    {
        $asesorId = $request->user()->asesor_id ?? null;

        if (!$asesorId) {
            return response()->json([
                'message' => 'Guru tidak ditemukan',
                'data' => null
            ], 404);
        }

        try {
            $asesor = Asesor::find($asesorId);

            // Get jadwal ujikom yang ditugaskan ke guru ini
            $jadwalUjikom = JadwalUjikom::where('asesor_id', $asesorId)
                ->orderBy('tanggal_ujian', 'asc')
                ->get();

            // Get statistik penilaian
            $penilaianStats = GuruPenilaian::where('asesor_id', $asesorId)
                ->select('status', DB::raw('count(*) as total'))
                ->groupBy('status')
                ->get()
                ->keyBy('status');

            // Get recent activity
            $recentActivity = GuruActivityLog::where('asesor_id', $asesorId)
                ->latest()
                ->limit(10)
                ->get();

            return response()->json([
                'message' => 'Dashboard loaded successfully',
                'data' => [
                    'guru' => [
                        'id' => $asesor->id,
                        'nama_lengkap' => $asesor->nama_lengkap,
                        'no_MET' => $asesor->no_MET,
                        'status' => $asesor->status,
                    ],
                    'summary' => [
                        'total_jadwal' => $jadwalUjikom->count(),
                        'jadwal_mendatang' => $jadwalUjikom->where('tanggal_ujian', '>=', now())->count(),
                        'penilaian_menunggu' => $penilaianStats->get('menunggu')?->total ?? 0,
                        'penilaian_dinilai' => $penilaianStats->get('dinilai')?->total ?? 0,
                        'penilaian_selesai' => $penilaianStats->get('selesai')?->total ?? 0,
                    ],
                    'jadwal_ujikom' => $jadwalUjikom->map(function ($j) {
                        return [
                            'id' => $j->id,
                            'tanggal_ujian' => $j->tanggal_ujian,
                            'waktu_mulai' => $j->waktu_mulai,
                            'waktu_selesai' => $j->waktu_selesai,
                            'skema' => [
                                'id' => $j->skema?->id,
                                'nama_skema' => $j->skema?->nama_skema,
                            ],
                            'tuk' => [
                                'id' => $j->tuk?->id,
                                'nama_tuk' => $j->tuk?->nama_tuk,
                            ],
                        ];
                    }),
                    'recent_activity' => $recentActivity->map(function ($activity) {
                        return [
                            'id' => $activity->id,
                            'activity_type' => $activity->activity_type,
                            'description' => $activity->description,
                            'created_at' => $activity->created_at,
                        ];
                    }),
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error loading dashboard',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
