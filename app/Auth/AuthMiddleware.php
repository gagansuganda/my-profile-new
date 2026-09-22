<?php

namespace App\Auth;

use App\Auth\JwtService;

class AuthMiddleware
{
    public static function check(): ?array
    {
        $token = null;

        // Try to get token from Cookie first (security pattern)
        if (isset($_COOKIE['admin_token'])) {
            $token = $_COOKIE['admin_token'];
        }

        // Dropback: Try to get from Authorization Bearer header
        if (!$token && isset($_SERVER['HTTP_AUTHORIZATION'])) {
            $parts = explode(' ', $_SERVER['HTTP_AUTHORIZATION']);
            if (count($parts) === 2 && strcasecmp($parts[0], 'Bearer') === 0) {
                $token = $parts[1];
            }
        }

        if (!$token) {
            return null;
        }

        return JwtService::decode($token);
    }

    public static function enforceAPI(): array
    {
        $user = self::check();
        if (!$user) {
            apiResponse(401, false, "Unauthorized. Silakan login terlebih dahulu.");
        }
        return $user;
    }

    public static function enforceUI(): void
    {
        $user = self::check();
        if (!$user) {
            header('Location: /admin/login.php');
            exit;
        }
    }
}
