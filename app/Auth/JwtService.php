<?php

namespace App\Auth;

use App\Config;
use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JwtService
{
    private static function getSecret(): string
    {
        $secret = Config::get('JWT_SECRET');
        if (!$secret) {
            throw new Exception("JWT_SECRET is not configured in .env file.");
        }
        return $secret;
    }

    public static function encode(array $payload, int $expiry = 86400): string
    {
        $payload['iss'] = Config::get('APP_URL', 'http://localhost:8000');
        $payload['iat'] = time();
        $payload['exp'] = time() + $expiry;

        return JWT::encode($payload, self::getSecret(), 'HS256');
    }

    public static function decode(string $token): ?array
    {
        try {
            $decoded = JWT::decode($token, new Key(self::getSecret(), 'HS256'));
            return (array) $decoded;
        } catch (Exception $e) {
            return null;
        }
    }
}
