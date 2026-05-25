<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        // filter berdasarkan level
        if ($request->level) {
            $query->where('level', $request->level);
        }

        $users = $query->orderBy('id')->paginate(5);

        return response()->json([
            'success' => true,
            'data' => $users
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_lengkap' => 'required',
            'username' => 'required|unique:users',
            'level' => 'required|in:administrator,asesor,asesi,validator',
            'password' => 'required|min:6',
        ]);

        $data['password'] = bcrypt($data['password']);

        $user = User::create($data);

        return response()->json([
            'success' => true,
            'message' => 'User berhasil ditambahkan',
            'data' => $user
        ], 201);
    }

    public function show(User $user)
    {
        return response()->json([
            'success' => true,
            'data' => $user
        ]);
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'nama_lengkap' => 'required',
            'username' => 'required|unique:users,username,' . $user->id,
            'level' => 'required|in:administrator,asesor,asesi,validator',
            'password' => 'nullable|min:6',
        ]);

        if (!empty($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);

        return response()->json([
            'success' => true,
            'message' => 'User berhasil diupdate',
            'data' => $user
        ]);
    }

    public function destroy(User $user)
    {
        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'User berhasil dihapus'
        ]);
    }

    public function resetPassword(Request $request, User $user)
    {
        $validated = $request->validate([
            'password' => 'required|string|min:8|confirmed'
        ]);

        $user->update([
            'password' => Hash::make($validated['password'])
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Password berhasil direset'
        ]);
    }

    /**
     * Get current authenticated user's profile
     */
    public function profile(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User tidak terautentikasi'
            ], 401);
        }

        return response()->json([
            'success' => true,
            'data' => $user
        ]);
    }

    /**
     * Update current authenticated user's profile
     */
    public function updateProfile(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User tidak terautentikasi'
            ], 401);
        }

        $data = $request->validate([
            'nama_lengkap' => 'nullable|string',
            'username' => 'nullable|string|unique:users,username,' . $user->id,
            'email' => 'nullable|email',
        ]);

        // Remove null values
        $data = array_filter($data, fn($value) => $value !== null);

        $user->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Profile berhasil diperbarui',
            'data' => $user
        ]);
    }

    /**
     * Change password for current authenticated user
     */
    public function changePassword(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User tidak terautentikasi'
            ], 401);
        }

        $validated = $request->validate([
            'password_lama' => 'required|string',
            'password_baru' => 'required|string|min:6|confirmed',
        ]);

        // Check if old password is correct
        if (!Hash::check($validated['password_lama'], $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Password lama tidak sesuai'
            ], 422);
        }

        $user->update([
            'password' => Hash::make($validated['password_baru'])
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Password berhasil diubah'
        ]);
    }

    /**
     * Get current user's permissions/role
     */
    public function getPermissions(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User tidak terautentikasi'
            ], 401);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $user->id,
                'nama_lengkap' => $user->nama_lengkap,
                'username' => $user->username,
                'level' => $user->level,
                'permissions' => $this->getPermissionsByLevel($user->level),
                'is_admin' => $user->level === 'administrator',
                'is_asesor' => $user->level === 'asesor',
                'is_asesi' => $user->level === 'asesi',
                'is_validator' => $user->level === 'validator',
            ]
        ]);
    }

    /**
     * Helper: Get permissions based on user level
     */
    private function getPermissionsByLevel(string $level): array
    {
        $permissions = [
            'administrator' => [
                'manage_users',
                'manage_asesi',
                'manage_asesor',
                'manage_skema',
                'manage_unit',
                'manage_element',
                'manage_kriteria',
                'view_reports',
                'manage_settings',
            ],
            'asesor' => [
                'view_asesi',
                'manage_asesi',
                'view_jadwal_ujikom',
                'input_nilai',
                'view_reports',
            ],
            'asesi' => [
                'view_profile',
                'view_jadwal_ujikom',
                'view_skema',
                'submit_dokumen',
            ],
            'validator' => [
                'validate_asesi',
                'view_reports',
                'manage_validasi',
            ],
        ];

        return $permissions[$level] ?? [];
    }
}
