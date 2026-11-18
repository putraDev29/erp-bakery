<?php

namespace App\Http\Middleware;

use App\Models\User;
use App\Services\JwtService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class JwtAuthMiddleware
{
    public function __construct(
        protected JwtService $jwtService
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();

        if (! $token) {
            return response()->json([
                'success' => false,
                'message' => 'Token otentikasi Bearer tidak disertakan.',
            ], 401);
        }

        try {
            $payload = $this->jwtService->decodeToken($token);
            $user = User::find($payload['sub']);

            if (! $user || ! $user->is_active) {
                return response()->json([
                    'success' => false,
                    'message' => 'Akun pengguna tidak ditemukan atau telah dinonaktifkan.',
                ], 401);
            }

            Auth::setUser($user);
            $request->attributes->set('jwt_payload', $payload);

            return $next($request);
        } catch (Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Otentikasi gagal: ' . $e->getMessage(),
            ], 401);
        }
    }
}
