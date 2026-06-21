<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Asesor;
use App\Models\Skema;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class AsesorSkemaController extends Controller
{
    /**
     * Display all asesor-skema assignments in a single query
     * (lebih cepat daripada memanggil getSkemaByAsesor per asesor satu-satu).
     */
    public function index()
    {
        try {
            $data = DB::table('asesor_skema')
                ->join('asesor', 'asesor.id', '=', 'asesor_skema.asesor_id')
                ->join('skema', 'skema.id', '=', 'asesor_skema.skema_id')
                ->select(
                    'asesor.id as asesor_id',
                    'asesor.nama_lengkap as asesor_nama',
                    'skema.id as skema_id',
                    'skema.nama_skema as skema_nama'
                )
                ->orderBy('asesor.nama_lengkap')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Data asesor skema berhasil diambil',
                'data' => $data,
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data asesor skema',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display all asesor for a specific skema
     */
    public function getAsesorBySkema($skemaId)
    {
        try {
            $skema = Skema::findOrFail($skemaId);
            $asesor = $skema->asesor()->paginate(15);

            return response()->json([
                'success' => true,
                'message' => 'Data asesor untuk skema ' . $skema->nama_skema . ' berhasil diambil',
                'data' => $asesor->items(),
                'pagination' => [
                    'current_page' => $asesor->currentPage(),
                    'total' => $asesor->total(),
                    'per_page' => $asesor->perPage(),
                    'last_page' => $asesor->lastPage(),
                ]
            ], Response::HTTP_OK);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data skema tidak ditemukan'
            ], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data asesor',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display all skema for a specific asesor
     */
    public function getSkemaByAsesor($asesorId)
    {
        try {
            $asesor = Asesor::findOrFail($asesorId);
            $skema = $asesor->skemas()->paginate(15);

            return response()->json([
                'success' => true,
                'message' => 'Data skema untuk asesor ' . $asesor->nama_lengkap . ' berhasil diambil',
                'data' => $skema->items(),
                'pagination' => [
                    'current_page' => $skema->currentPage(),
                    'total' => $skema->total(),
                    'per_page' => $skema->perPage(),
                    'last_page' => $skema->lastPage(),
                ]
            ], Response::HTTP_OK);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data asesor tidak ditemukan'
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
     * Assign asesor to skema
     */
    public function assignAsesorToSkema(Request $request)
    {
        try {
            $validated = $request->validate([
                'asesor_id' => 'required|exists:asesor,id',
                'skema_id' => 'required|exists:skema,id',
            ]);

            $asesor = Asesor::findOrFail($validated['asesor_id']);
            $skema = Skema::findOrFail($validated['skema_id']);

            // Check if already assigned
            if ($asesor->skemas()->where('skema_id', $validated['skema_id'])->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Asesor sudah terdaftar untuk skema ini'
                ], Response::HTTP_CONFLICT);
            }

            $asesor->skemas()->attach($validated['skema_id']);

            return response()->json([
                'success' => true,
                'message' => 'Asesor berhasil ditambahkan ke skema',
                'data' => [
                    'asesor' => $asesor,
                    'skema' => $skema
                ]
            ], Response::HTTP_CREATED);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data asesor atau skema tidak ditemukan'
            ], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan asesor ke skema',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Assign multiple asesor to one skema
     */
    public function assignMultipleAsesorToSkema(Request $request)
    {
        try {
            $validated = $request->validate([
                'skema_id' => 'required|exists:skema,id',
                'asesor_ids' => 'required|array',
                'asesor_ids.*' => 'exists:asesor,id',
            ]);

            $skema = Skema::findOrFail($validated['skema_id']);
            $attachedCount = 0;
            $errors = [];

            foreach ($validated['asesor_ids'] as $asesorId) {
                try {
                    if (!$skema->asesor()->where('asesor_id', $asesorId)->exists()) {
                        $skema->asesor()->attach($asesorId);
                        $attachedCount++;
                    }
                } catch (\Exception $e) {
                    $errors[] = 'Asesor ID ' . $asesorId . ': ' . $e->getMessage();
                }
            }

            return response()->json([
                'success' => true,
                'message' => $attachedCount . ' asesor berhasil ditambahkan ke skema',
                'data' => [
                    'skema_id' => $validated['skema_id'],
                    'attached_count' => $attachedCount,
                    'errors' => $errors
                ]
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
                'message' => 'Gagal menambahkan asesor ke skema',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove asesor from skema
     */
    public function removeAsesorFromSkema($asesorId, $skemaId)
    {
        try {
            $asesor = Asesor::findOrFail($asesorId);
            $skema = Skema::findOrFail($skemaId);

            if (!$asesor->skemas()->where('skema_id', $skemaId)->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Asesor tidak terdaftar untuk skema ini'
                ], Response::HTTP_NOT_FOUND);
            }

            $asesor->skemas()->detach($skemaId);

            return response()->json([
                'success' => true,
                'message' => 'Asesor berhasil dihapus dari skema'
            ], Response::HTTP_OK);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data asesor atau skema tidak ditemukan'
            ], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus asesor dari skema',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove multiple asesor from one skema
     */
    public function removeMultipleAsesorFromSkema(Request $request)
    {
        try {
            $validated = $request->validate([
                'skema_id' => 'required|exists:skema,id',
                'asesor_ids' => 'required|array',
                'asesor_ids.*' => 'exists:asesor,id',
            ]);

            $skema = Skema::findOrFail($validated['skema_id']);
            $detachedCount = 0;

            foreach ($validated['asesor_ids'] as $asesorId) {
                if ($skema->asesor()->where('asesor_id', $asesorId)->exists()) {
                    $skema->asesor()->detach($asesorId);
                    $detachedCount++;
                }
            }

            return response()->json([
                'success' => true,
                'message' => $detachedCount . ' asesor berhasil dihapus dari skema',
                'data' => [
                    'skema_id' => $validated['skema_id'],
                    'detached_count' => $detachedCount
                ]
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
                'message' => 'Gagal menghapus asesor dari skema',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Sync asesor to skema (replace all existing with new ones)
     */
    public function syncAsesorToSkema(Request $request)
    {
        try {
            $validated = $request->validate([
                'skema_id' => 'required|exists:skema,id',
                'asesor_ids' => 'required|array',
                'asesor_ids.*' => 'exists:asesor,id',
            ]);

            $skema = Skema::findOrFail($validated['skema_id']);
            $skema->asesor()->sync($validated['asesor_ids']);

            return response()->json([
                'success' => true,
                'message' => 'Asesor untuk skema berhasil disinkronisasi',
                'data' => [
                    'skema_id' => $validated['skema_id'],
                    'asesor_count' => count($validated['asesor_ids'])
                ]
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
                'message' => 'Gagal mensinkronisasi asesor',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Check if asesor is assigned to skema
     */
    public function checkAssignment($asesorId, $skemaId)
    {
        try {
            $asesor = Asesor::findOrFail($asesorId);
            $isAssigned = $asesor->skemas()->where('skema_id', $skemaId)->exists();

            return response()->json([
                'success' => true,
                'message' => 'Pengecekan berhasil',
                'data' => [
                    'asesor_id' => $asesorId,
                    'skema_id' => $skemaId,
                    'is_assigned' => $isAssigned
                ]
            ], Response::HTTP_OK);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data asesor atau skema tidak ditemukan'
            ], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memeriksa penugasan',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
