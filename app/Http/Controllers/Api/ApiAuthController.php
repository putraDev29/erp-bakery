<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\JwtService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ApiAuthController extends Controller
{
    public function __construct(
        protected JwtService $jwtService
    ) {}

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'branch_id' => 'nullable|integer',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Kredensial login tidak valid.',
            ], 401);
        }

        if (! $user->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Akun pengguna dinonaktifkan.',
            ], 403);
        }

        $branchId = $request->branch_id;
        if ($branchId && ! $user->canAccessBranch((int) $branchId)) {
            return response()->json([
                'success' => false,
                'message' => 'Pengguna tidak memiliki akses ke cabang ini.',
            ], 403);
        }

        $token = $this->jwtService->generateToken($user, $branchId);

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil.',
            'token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => 43200,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'roles' => $user->roles->pluck('name'),
                'branches' => $user->branches->map(fn ($b) => ['id' => $b->id, 'name' => $b->name, 'code' => $b->code]),
            ],
        ]);
    }

    public function me(Request $request)
    {
        $payload = $request->attributes->get('jwt_payload');
        $user = $request->user();

        return response()->json([
            'success' => true,
            'user' => $user,
            'token_context' => $payload,
        ]);
    }

    public function logout(Request $request)
    {
        return response()->json([
            'success' => true,
            'message' => 'Token JWT telah dicabut dari sesi klien.',
        ]);
    }
}
