<?php

namespace App\Models;

use App\Database;
use PDO;

abstract class Model
{
    protected static string $table = '';

    public static function all(): array
    {
        $db = Database::connect();
        $stmt = $db->query("SELECT * FROM " . static::$table . " ORDER BY created_at DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function find(string $id): ?array
    {
        $db = Database::connect();
        $stmt = $db->prepare("SELECT * FROM " . static::$table . " WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public static function delete(string $id): bool
    {
        $db = Database::connect();
        $stmt = $db->prepare("DELETE FROM " . static::$table . " WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }
}
