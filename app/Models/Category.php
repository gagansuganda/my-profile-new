<?php

namespace App\Models;

use App\Models\Model;
use App\Database;

class Category extends Model
{
    protected static string $table = 'categories';

    public static function allByType(string $type): array
    {
        $db = Database::connect();
        $stmt = $db->prepare("SELECT * FROM categories WHERE type = :type ORDER BY created_at DESC");
        $stmt->execute([':type' => $type]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public static function create(string $id, string $name, string $slug, ?string $description = null, string $type = 'blog'): bool
    {
        $db = Database::connect();
        $stmt = $db->prepare("INSERT INTO categories (id, name, slug, description, type) VALUES (:id, :name, :slug, :description, :type)");
        return $stmt->execute([
            ':id' => $id,
            ':name' => $name,
            ':slug' => $slug,
            ':description' => $description,
            ':type' => $type
        ]);
    }

    public static function update(string $id, string $name, string $slug, ?string $description = null, string $type = 'blog'): bool
    {
        $db = Database::connect();
        $stmt = $db->prepare("UPDATE categories SET name = :name, slug = :slug, description = :description, type = :type WHERE id = :id");
        return $stmt->execute([
            ':id' => $id,
            ':name' => $name,
            ':slug' => $slug,
            ':description' => $description,
            ':type' => $type
        ]);
    }
}
