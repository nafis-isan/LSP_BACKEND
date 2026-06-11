<?php

namespace App\Http\Controllers\Api;

use App\Models\Skema;
use App\Models\Unit;
use App\Models\Element;
use App\Models\KriteriaKerja;
use App\Models\Muk;
use App\Models\GuruActivityLog;
use Illuminate\Http\Request;

class GuruReferensiController
{
    /**
     * Get daftar skema untuk guru
     */
    public function getSkema(Request $request)
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
                'activity_type' => 'view_referensi_skema',
                'description' => 'Lihat referensi data skema',
                'ip_address' => $request->ip(),
            ]);

            // Get skema yang ditugaskan ke guru with units count
            $skema = Skema::whereHas('asesor', function ($query) use ($asesorId) {
                $query->where('asesor.id', $asesorId);
            })
            ->withCount('units')
            ->paginate($request->get('per_page', 20));

            return response()->json([
                'message' => 'Skema list loaded',
                'data' => $skema->map(function ($s) {
                    return [
                        'id' => $s->id,
                        'nomor_skema' => $s->kode_skema,
                        'judul_skema' => $s->nama_skema,
                        'jenis_skema' => $s->jenis_skema,
                        'jenjang' => $s->jenjang,
                        'file_skema' => $s->file_skema, // URL untuk download
                        'jumlah_unit' => $s->units_count, // Actual count dari units
                        'deskripsi' => $s->deskripsi,
                        'status' => $s->status,
                        'actions' => [
                            'view' => [
                                'label' => 'Lihat Detail',
                                'method' => 'GET',
                                'endpoint' => '/api/guru/referensi/skema/' . $s->id . '/detail'
                            ],
                            'download' => $s->file_skema ? [
                                'label' => 'Download File',
                                'method' => 'GET',
                                'endpoint' => '/api/guru/referensi/skema/' . $s->id . '/download'
                            ] : null,
                            'view_unit' => [
                                'label' => 'Lihat Unit Kompetensi',
                                'method' => 'GET',
                                'endpoint' => '/api/guru/referensi/skema/' . $s->id . '/unit'
                            ],
                            'view_muk' => [
                                'label' => 'Lihat MUK',
                                'method' => 'GET',
                                'endpoint' => '/api/guru/referensi/skema/' . $s->id . '/muk'
                            ]
                        ]
                    ];
                }),
                'pagination' => [
                    'total' => $skema->total(),
                    'per_page' => $skema->perPage(),
                    'current_page' => $skema->currentPage(),
                    'last_page' => $skema->lastPage(),
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error loading skema',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get unit kompetensi berdasarkan skema
     */
    public function getUnitBySkema(Request $request, $skemaId)
    {
        try {
            GuruActivityLog::create([
                'asesor_id' => $request->user()->asesor_id,
                'activity_type' => 'view_unit_kompetensi',
                'description' => 'Lihat unit kompetensi',
                'ip_address' => $request->ip(),
                'data' => ['skema_id' => $skemaId],
            ]);

            $units = Unit::where('skema_id', $skemaId)
                ->with('element')
                ->paginate($request->get('per_page', 20));

            return response()->json([
                'message' => 'Unit kompetensi loaded',
                'data' => $units->map(function ($u) {
                    return [
                        'id' => $u->id,
                        'nama_unit' => $u->nama_unit,
                        'deskripsi' => $u->deskripsi,
                        'element_count' => $u->element->count(),
                    ];
                }),
                'pagination' => [
                    'total' => $units->total(),
                    'per_page' => $units->perPage(),
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error loading unit',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get element berdasarkan unit
     */
    public function getElementByUnit(Request $request, $unitId)
    {
        try {
            GuruActivityLog::create([
                'asesor_id' => $request->user()->asesor_id,
                'activity_type' => 'view_element',
                'description' => 'Lihat element kompetensi',
                'ip_address' => $request->ip(),
                'data' => ['unit_id' => $unitId],
            ]);

            $elements = Element::where('unit_id', $unitId)
                ->with('kriteriaKerja')
                ->paginate($request->get('per_page', 20));

            return response()->json([
                'message' => 'Element loaded',
                'data' => $elements->map(function ($e) {
                    return [
                        'id' => $e->id,
                        'nama_element' => $e->nama_element,
                        'deskripsi' => $e->deskripsi,
                        'kriteria_kerja_count' => $e->kriteriaKerja->count(),
                    ];
                }),
                'pagination' => [
                    'total' => $elements->total(),
                    'per_page' => $elements->perPage(),
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error loading element',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get kriteria kerja berdasarkan element
     */
    public function getKriteriaByElement(Request $request, $elementId)
    {
        try {
            GuruActivityLog::create([
                'asesor_id' => $request->user()->asesor_id,
                'activity_type' => 'view_kriteria_kerja',
                'description' => 'Lihat kriteria kerja',
                'ip_address' => $request->ip(),
                'data' => ['element_id' => $elementId],
            ]);

            $kriteria = KriteriaKerja::where('element_id', $elementId)
                ->paginate($request->get('per_page', 50));

            return response()->json([
                'message' => 'Kriteria kerja loaded',
                'data' => $kriteria->map(function ($k) {
                    return [
                        'id' => $k->id,
                        'kriteria_kerja' => $k->kriteria_kerja,
                        'deskripsi' => $k->deskripsi,
                    ];
                }),
                'pagination' => [
                    'total' => $kriteria->total(),
                    'per_page' => $kriteria->perPage(),
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error loading kriteria kerja',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get MUK (Materi Uji Kompetensi) berdasarkan skema
     */
    public function getMukBySkema(Request $request, $skemaId)
    {
        try {
            GuruActivityLog::create([
                'asesor_id' => $request->user()->asesor_id,
                'activity_type' => 'view_muk',
                'description' => 'Lihat MUK',
                'ip_address' => $request->ip(),
                'data' => ['skema_id' => $skemaId],
            ]);

            $muk = Muk::where('skema_id', $skemaId)
                ->with('dokumen')
                ->paginate($request->get('per_page', 20));

            return response()->json([
                'message' => 'MUK loaded',
                'data' => $muk->map(function ($m) {
                    return [
                        'id' => $m->id,
                        'judul' => $m->judul,
                        'deskripsi' => $m->deskripsi,
                        'dokumen_count' => $m->dokumen->count(),
                    ];
                }),
                'pagination' => [
                    'total' => $muk->total(),
                    'per_page' => $muk->perPage(),
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error loading MUK',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get detail skema
     */
    public function getSkemaDetail(Request $request, $skemaId)
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
                'activity_type' => 'view_skema_detail',
                'description' => 'Lihat detail skema',
                'ip_address' => $request->ip(),
                'data' => ['skema_id' => $skemaId],
            ]);

            $skema = Skema::whereHas('asesor', function ($query) use ($asesorId) {
                $query->where('asesor.id', $asesorId);
            })
            ->withCount('units')
            ->findOrFail($skemaId);

            return response()->json([
                'message' => 'Skema detail loaded',
                'data' => [
                    'id' => $skema->id,
                    'nomor_skema' => $skema->kode_skema,
                    'judul_skema' => $skema->nama_skema,
                    'jenis_skema' => $skema->jenis_skema,
                    'jenjang' => $skema->jenjang,
                    'file_skema' => $skema->file_skema,
                    'jumlah_unit' => $skema->units_count,
                    'deskripsi' => $skema->deskripsi,
                    'status' => $skema->status,
                    'created_at' => $skema->created_at,
                    'updated_at' => $skema->updated_at,
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error loading skema detail',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Download file skema
     */
    public function downloadSkemaFile(Request $request, $skemaId)
    {
        $asesorId = $request->user()->asesor_id ?? null;

        if (!$asesorId) {
            return response()->json([
                'message' => 'Guru tidak ditemukan',
            ], 404);
        }

        try {
            $skema = Skema::whereHas('asesor', function ($query) use ($asesorId) {
                $query->where('asesor.id', $asesorId);
            })->findOrFail($skemaId);

            if (!$skema->file_skema) {
                return response()->json([
                    'message' => 'File skema tidak tersedia',
                ], 404);
            }

            GuruActivityLog::create([
                'asesor_id' => $asesorId,
                'activity_type' => 'download_skema',
                'description' => 'Download file skema',
                'ip_address' => $request->ip(),
                'data' => ['skema_id' => $skemaId, 'file' => $skema->file_skema],
            ]);

            // Assuming file_skema is a full path or URL
            // If it's a path relative to storage, use Storage::download()
            $filePath = storage_path('app/' . $skema->file_skema);

            if (!file_exists($filePath)) {
                return response()->json([
                    'message' => 'File tidak ditemukan di server',
                ], 404);
            }

            return response()->download($filePath, basename($skema->file_skema));
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error downloading file',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
