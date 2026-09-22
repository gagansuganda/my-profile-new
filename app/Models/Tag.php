<?php

namespace App\Models;

use App\Models\Model;
use App\Database;

class Tag extends Model
{
    protected static string $table = 'tags';

    public static function allByType(string $type): array
    {
        $db = Database::connect();
        $stmt = $db->prepare("SELECT * FROM tags WHERE type = :type ORDER BY created_at DESC");
        $stmt->execute([':type' => $type]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public static function create(string $id, string $name, string $slug, string $type = 'blog'): bool
    {
        $db = Database::connect();
        $stmt = $db->prepare("INSERT INTO tags (id, name, slug, type) VALUES (:id, :name, :slug, :type)");
        return $stmt->execute([
            ':id' => $id,
            ':name' => $name,
            ':slug' => $slug,
            ':type' => $type
        ]);
    }

    public static function update(string $id, string $name, string $slug, string $type = 'blog'): bool
    {
        $db = Database::connect();
        $stmt = $db->prepare("UPDATE tags SET name = :name, slug = :slug, type = :type WHERE id = :id");
        return $stmt->execute([
            ':id' => $id,
            ':name' => $name,
            ':slug' => $slug,
            ':type' => $type
        ]);
    }
}
