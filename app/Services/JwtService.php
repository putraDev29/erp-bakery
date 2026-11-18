<?php

namespace App\Services;

use App\Models\User;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Support\Facades\Config;
use RuntimeException;
use Throwable;

class JwtService
{
    protected string $secret;
    protected string $algo = 'HS256';
    protected int $ttl = 43200; // 12 hours

    public function __construct()
    {
        $this->secret = (string) (Config::get('app.jwt_secret') ?: env('JWT_SECRET', 'default-bakery-erp-secret-key-32-chars-min!'));
    }

    /**
     * Generate signed JWT for a user.
     */
    public function generateToken(User $user, ?int $branchId = null): string
    {
        $now = time();
        $payload = [
            'iss' => Config::get('app.url'),
            'sub' => $user->id,
            'company_id' => $user->company_id,
            'branch_id' => $branchId ?: optional($user->getDefaultBranch())->id,
            'email' => $user->email,
            'roles' => $user->roles->pluck('name')->toArray(),
            'iat' => $now,
            'nbf' => $now,
            'exp' => $now + $this->ttl,
        ];

        return JWT::encode($payload, $this->secret, $this->algo);
    }

    /**
     * Decode and validate token payload.
     */
    public function decodeToken(string $token): array
    {
        try {
            $decoded = JWT::decode($token, new Key($this->secret, $this->algo));
            return (array) $decoded;
        } catch (Throwable $e) {
            throw new RuntimeException("Token JWT tidak valid atau telah kedaluwarsa: " . $e->getMessage());
        }
    }
}
