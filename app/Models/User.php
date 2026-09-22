<?php

namespace App\Models;

use App\Models\Model;
use App\Database;
use PDO;

class User extends Model
{
    protected static string $table = 'users';

    public static function findByEmail(string $email): ?array
    {
        $db = Database::connect();
        $stmt = $db->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
        $stmt->execute([':email' => $email]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public static function create(string $id, string $name, string $email, string $password): bool
    {
        $db = Database::connect();
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $db->prepare("INSERT INTO users (id, name, email, password_hash) VALUES (:id, :name, :email, :password_hash)");
        return $stmt->execute([
            ':id' => $id,
            ':name' => $name,
            ':email' => $email,
            ':password_hash' => $hash
        ]);
    }

    public static function updateProfile(string $id, string $name, string $phone, ?string $password = null): bool
    {
        $db = Database::connect();

        // We'll also store phone in users table if needed, let me make sure users table has phone column, or we can add it, or save dynamically
        // Let's add phone directly into users model and schema.sql
        if ($password) {
            $hash = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $db->prepare("UPDATE users SET name = :name, phone = :phone, password_hash = :hash WHERE id = :id");
            return $stmt->execute([
                ':id' => $id,
                ':name' => $name,
                ':phone' => $phone,
                ':hash' => $hash
            ]);
        } else {
            $stmt = $db->prepare("UPDATE users SET name = :name, phone = :phone WHERE id = :id");
            return $stmt->execute([
                ':id' => $id,
                ':name' => $name,
                ':phone' => $phone
            ]);
        }
    }
}
