<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        $user = Auth::user();

        if (! $user || ! $user->hasPermission($permission)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => "Anda tidak memiliki izin '{$permission}' untuk tindakan ini.",
                ], 403);
            }

            abort(403, "Akses ditolak: Membutuhkan izin '{$permission}'.");
        }

        return $next($request);
    }
}
