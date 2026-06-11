<?php

namespace App\Http\Controllers\Api;

use App\Models\GuruActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GuruActivityLogController
{
    /**
     * Get activity log untuk monitoring admin
     */
    public function index(Request $request)
    {
        try {
            $query = GuruActivityLog::query();

            // Filter by asesor_id
            if ($request->filled('asesor_id')) {
                $query->where('asesor_id', $request->asesor_id);
            }

            // Filter by activity type
            if ($request->filled('activity_type')) {
                $query->where('activity_type', $request->activity_type);
            }

            // Filter by date range
            if ($request->filled('date_from')) {
                $query->whereDate('created_at', '>=', $request->date_from);
            }
            if ($request->filled('date_to')) {
                $query->whereDate('created_at', '<=', $request->date_to);
            }

            $logs = $query->with('asesor')
                ->latest()
                ->paginate($request->get('per_page', 50));

            return response()->json([
                'message' => 'Activity logs retrieved',
                'data' => $logs->map(function ($log) {
                    return [
                        'id' => $log->id,
                        'asesor' => [
                            'id' => $log->asesor?->id,
                            'nama_lengkap' => $log->asesor?->nama_lengkap,
                            'no_MET' => $log->asesor?->no_MET,
                        ],
                        'activity_type' => $log->activity_type,
                        'description' => $log->description,
                        'ip_address' => $log->ip_address,
                        'data' => $log->data,
                        'created_at' => $log->created_at,
                    ];
                }),
                'pagination' => [
                    'total' => $logs->total(),
                    'count' => $logs->count(),
                    'per_page' => $logs->perPage(),
                    'current_page' => $logs->currentPage(),
                    'last_page' => $logs->lastPage(),
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error retrieving activity logs',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get activity summary by guru
     */
    public function summary(Request $request)
    {
        try {
            $dateFrom = $request->get('date_from', now()->subDays(30));
            $dateTo = $request->get('date_to', now());

            $summary = GuruActivityLog::whereBetween('created_at', [$dateFrom, $dateTo])
                ->select(
                    'asesor_id',
                    'activity_type',
                    DB::raw('count(*) as total')
                )
                ->with('asesor')
                ->groupBy('asesor_id', 'activity_type')
                ->get();

            // Group by asesor
            $bySummary = $summary->groupBy('asesor_id')->map(function ($activities) {
                $asesor = $activities->first()->asesor;
                return [
                    'asesor' => [
                        'id' => $asesor->id,
                        'nama_lengkap' => $asesor->nama_lengkap,
                    ],
                    'activities' => $activities->keyBy('activity_type')->map->total,
                    'total' => $activities->sum('total'),
                ];
            });

            return response()->json([
                'message' => 'Activity summary retrieved',
                'period' => [
                    'from' => $dateFrom,
                    'to' => $dateTo,
                ],
                'data' => $bySummary->values()
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error retrieving summary',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get activity statistics
     */
    public function statistics(Request $request)
    {
        try {
            $stats = GuruActivityLog::select('activity_type', DB::raw('count(*) as total'))
                ->groupBy('activity_type')
                ->get()
                ->keyBy('activity_type');

            // Get active gurus (gurus yang login dalam 24 jam terakhir)
            $activeGurus = GuruActivityLog::where('activity_type', 'login')
                ->where('created_at', '>=', now()->subDay())
                ->distinct('asesor_id')
                ->count('asesor_id');

            return response()->json([
                'message' => 'Statistics retrieved',
                'data' => [
                    'activity_breakdown' => $stats,
                    'active_gurus' => $activeGurus,
                    'total_activities' => GuruActivityLog::count(),
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error retrieving statistics',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get activity by specific guru
     */
    public function byGuru(Request $request, $asesorId)
    {
        try {
            $logs = GuruActivityLog::where('asesor_id', $asesorId)
                ->with('asesor')
                ->latest()
                ->paginate($request->get('per_page', 50));

            return response()->json([
                'message' => 'Guru activity logs retrieved',
                'data' => $logs->map(function ($log) {
                    return [
                        'id' => $log->id,
                        'activity_type' => $log->activity_type,
                        'description' => $log->description,
                        'ip_address' => $log->ip_address,
                        'data' => $log->data,
                        'created_at' => $log->created_at,
                    ];
                }),
                'pagination' => [
                    'total' => $logs->total(),
                    'per_page' => $logs->perPage(),
                    'current_page' => $logs->currentPage(),
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error retrieving activity logs',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
