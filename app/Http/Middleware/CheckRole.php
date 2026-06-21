<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated.'], 401);
        }

        if ($user->status !== 'aktif') {
            return response()->json(['success' => false, 'message' => 'Akun Anda tidak aktif.'], 403);
        }

        foreach ($roles as $role) {
            if ($user->level === $role) {
                return $next($request);
            }
        }

        return response()->json(['success' => false, 'message' => 'Unauthorized action.'], 403);
    }
}