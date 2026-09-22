<?php

namespace App;

class Config
{
    private static array $config = [];

    public static function init(): void
    {
        $envPath = dirname(__DIR__) . '/.env';
        if (file_exists($envPath)) {
            $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                if (strpos(trim($line), '#') === 0) {
                    continue;
                }
                $parts = explode('=', $line, 2);
                if (count($parts) === 2) {
                    $key = trim($parts[0]);
                    $val = trim($parts[1]);
                    // Strip quotes if any
                    if (preg_match('/^"([^"]*)"$/', $val, $matches) || preg_match("/^'([^']*)'$/", $val, $matches)) {
                        $val = $matches[1];
                    }
                    self::$config[$key] = $val;
                    $_ENV[$key] = $val;
                    putenv("$key=$val");
                }
            }
        }
    }

    public static function get(string $key, $default = null)
    {
        if (empty(self::$config)) {
            self::init();
        }
        return self::$config[$key] ?? $_ENV[$key] ?? getenv($key) ?: $default;
    }
}
