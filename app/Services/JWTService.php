<?php

namespace App\Services;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Support\Facades\Config;

class JWTService
{
    private $secretKey;
    private $algorithm;
    private $tokenExpiration;
    private $refreshExpiration;

    public function __construct()
    {
        $this->secretKey = Config::get('app.key');
        $this->algorithm = 'HS256';
        $this->tokenExpiration = 60 * 24; // 24 hours in minutes
        $this->refreshExpiration = 60 * 24 * 7; // 7 days in minutes
    }

    /**
     * Generate JWT token for user
     */
    public function generateToken($user): array
    {
        $issuedAt = time();
        $expire = $issuedAt + ($this->tokenExpiration * 60);
        $refreshExpire = $issuedAt + ($this->refreshExpiration * 60);

        $payload = [
            'iat' => $issuedAt,
            'exp' => $expire,
            'refresh_exp' => $refreshExpire,
            'user_id' => $user->id_user,
            'username' => $user->username,
            'email' => $user->email,
        ];

        $token = JWT::encode($payload, $this->secretKey, $this->algorithm);

        return [
            'token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => $this->tokenExpiration * 60,
            'refresh_expires_in' => $this->refreshExpiration * 60,
        ];
    }

    /**
     * Validate JWT token
     */
    public function validateToken($token): ?object
    {
        try {
            $decoded = JWT::decode($token, new Key($this->secretKey, $this->algorithm));
            return $decoded;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Get user from token
     */
    public function getUserFromToken($token): ?array
    {
        $decoded = $this->validateToken($token);
        if (!$decoded) {
            return null;
        }

        return [
            'user_id' => $decoded->user_id,
            'username' => $decoded->username,
            'email' => $decoded->email,
        ];
    }

    /**
     * Refresh token
     */
    public function refreshToken($token): ?array
    {
        $decoded = $this->validateToken($token);
        if (!$decoded) {
            return null;
        }

        // Check if refresh token is still valid
        if (time() > $decoded->refresh_exp) {
            return null;
        }

        // Generate new token
        $user = \App\Models\User::find($decoded->user_id);
        if (!$user) {
            return null;
        }

        return $this->generateToken($user);
    }
}
