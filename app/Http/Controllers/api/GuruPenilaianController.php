<?php

namespace App\Http\Controllers\Api;

use App\Models\GuruPenilaian;
use App\Models\JadwalUjikom;
use App\Models\Asesi;
use App\Models\Element;
use App\Models\GuruActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GuruPenilaianController
{
    /**
     * Get daftar asesi yang akan dinilai guru
     */
    public function getAsesiList(Request $request)
    {
        $asesorId = $request->user()->asesor_id ?? null;

        if (!$asesorId) {
            return response()->json([
                'message' => 'Guru tidak ditemukan',
            ], 404);
        }

        try {
            // Log activity
            GuruActivityLog::create([
                'asesor_id' => $asesorId,
                'activity_type' => 'view_asesi_list',
                'description' => 'Lihat daftar asesi',
                'ip_address' => $request->ip(),
                'user_agent' => $request->header('User-Agent'),
            ]);

            $jadwalUjikoms = JadwalUjikom::where('asesor_id', $asesorId)
                ->with(['asesi', 'skema'])
                ->get();

            $asesiList = $jadwalUjikoms->map(function ($jadwal) {
                return [
                    'jadwal_ujikom_id' => $jadwal->id,
                    'asesi_id' => $jadwal->asesi?->id,
                    'nama_asesi' => $jadwal->asesi?->nama_lengkap,
                    'skema' => $jadwal->skema?->nama_skema,
                    'tanggal_ujian' => $jadwal->tanggal_ujian,
                ];
            });

            return response()->json([
                'message' => 'Daftar asesi loaded',
                'data' => $asesiList
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error loading asesi list',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get penilaian untuk asesi tertentu
     */
    public function getByAsesi(Request $request, $asesiId)
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
                'activity_type' => 'view_penilaian',
                'description' => 'Lihat penilaian asesi',
                'ip_address' => $request->ip(),
                'data' => ['asesi_id' => $asesiId],
            ]);

            $penilaian = GuruPenilaian::where('asesor_id', $asesorId)
                ->where('asesi_id', $asesiId)
                ->with(['element', 'jadwalUjikom', 'asesi'])
                ->get();

            return response()->json([
                'message' => 'Penilaian loaded',
                'data' => $penilaian->map(function ($p) {
                    return [
                        'id' => $p->id,
                        'asesi' => $p->asesi?->nama_lengkap,
                        'element' => $p->element?->nama_element,
                        'nilai' => $p->nilai,
                        'status' => $p->status,
                        'komentar' => $p->komentar,
                        'tanggal_penilaian' => $p->tanggal_penilaian,
                    ];
                })
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error loading penilaian',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Simpan/Update penilaian
     */
    public function store(Request $request)
    {
        $asesorId = $request->user()->asesor_id ?? null;

        if (!$asesorId) {
            return response()->json([
                'message' => 'Guru tidak ditemukan',
            ], 404);
        }

        $validated = $request->validate([
            'asesi_id' => 'required|exists:asesi,id',
            'jadwal_ujikom_id' => 'required|exists:jadwal_ujikom,id',
            'element_id' => 'required|exists:element,id',
            'nilai' => 'required|integer|min:0|max:100',
            'komentar' => 'nullable|string',
        ]);

        try {
            $penilaian = GuruPenilaian::updateOrCreate(
                [
                    'asesor_id' => $asesorId,
                    'asesi_id' => $validated['asesi_id'],
                    'jadwal_ujikom_id' => $validated['jadwal_ujikom_id'],
                    'element_id' => $validated['element_id'],
                ],
                [
                    'nilai' => $validated['nilai'],
                    'komentar' => $validated['komentar'],
                    'status' => 'dinilai',
                    'tanggal_penilaian' => now(),
                ]
            );

            GuruActivityLog::create([
                'asesor_id' => $asesorId,
                'activity_type' => 'input_nilai',
                'description' => 'Input nilai untuk asesi',
                'ip_address' => $request->ip(),
                'data' => [
                    'asesi_id' => $validated['asesi_id'],
                    'element_id' => $validated['element_id'],
                    'nilai' => $validated['nilai'],
                ],
            ]);

            return response()->json([
                'message' => 'Penilaian saved successfully',
                'data' => $penilaian
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error saving penilaian',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Selesaikan penilaian
     */
    public function complete(Request $request, $penilaianId)
    {
        $asesorId = $request->user()->asesor_id ?? null;

        if (!$asesorId) {
            return response()->json([
                'message' => 'Guru tidak ditemukan',
            ], 404);
        }

        try {
            $penilaian = GuruPenilaian::where('asesor_id', $asesorId)
                ->findOrFail($penilaianId);

            $penilaian->update([
                'status' => 'selesai',
                'tanggal_penilaian' => now(),
            ]);

            GuruActivityLog::create([
                'asesor_id' => $asesorId,
                'activity_type' => 'complete_penilaian',
                'description' => 'Selesaikan penilaian',
                'ip_address' => $request->ip(),
                'data' => ['penilaian_id' => $penilaianId],
            ]);

            return response()->json([
                'message' => 'Penilaian completed',
                'data' => $penilaian
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error completing penilaian',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
