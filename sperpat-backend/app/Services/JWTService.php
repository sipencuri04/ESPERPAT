<?php

namespace App\Services;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JWTService
{
    private string $secretKey;
    private int $expireTime;

    public function __construct()
    {
        $this->secretKey  = env('JWT_SECRET_KEY', 'default_secret_key');
        $this->expireTime = (int) env('JWT_EXPIRE_TIME', 3600);
    }

    public function generateToken(array $userData): string
    {
        $issuedAt = time();
        $expire   = $issuedAt + $this->expireTime;

        $payload = [
            'iss'  => base_url(),
            'iat'  => $issuedAt,
            'exp'  => $expire,
            'data' => [
                'id'    => $userData['id'],
                'email' => $userData['email'],
                'role'  => $userData['role'],
                'name'  => $userData['name'],
            ],
        ];

        return JWT::encode($payload, $this->secretKey, 'HS256');
    }

    public function validateToken(string $token): ?object
    {
        try {
            $decoded = JWT::decode($token, new Key($this->secretKey, 'HS256'));
            return $decoded->data;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function getTokenFromHeader(): ?string
    {
        $request = service('request');
        $authHeader = $request->getHeaderLine('Authorization');

        if (empty($authHeader)) {
            return null;
        }

        if (preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            return $matches[1];
        }

        return null;
    }
}
