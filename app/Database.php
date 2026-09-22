<?php

namespace App;

use App\Config;
use Exception;
use PDO;

class Database
{
    private static ?PDO $instance = null;

    public static function connect(): PDO
    {
        if (self::$instance === null) {
            $host = Config::get('DB_HOST', '127.0.0.1');
            $port = Config::get('DB_PORT', '3306');
            $dbName = Config::get('DB_NAME');
            $username = Config::get('DB_USER', 'root');
            $password = Config::get('DB_PASS', '');

            if (!$dbName) {
                throw new Exception("Database name not configured in environment variables.");
            }

            try {
                $dsn = "mysql:host={$host};port={$port};dbname={$dbName};charset=utf8mb4";
                $options = [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ];
                self::$instance = new PDO($dsn, $username, $password, $options);
            } catch (\PDOException $e) {
                throw new Exception("Database Connection Failed: " . $e->getMessage());
            }
        }
        return self::$instance;
    }
}
