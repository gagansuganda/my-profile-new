<?php

namespace App\Helpers;

use App\Database;

class Slug
{
    public static function make(string $string): string
    {
        $string = preg_replace('~[^\pL\d]+~u', '-', $string);
        $string = iconv('utf-8', 'us-ascii//TRANSLIT', $string);
        $string = preg_replace('~[^-\w]+~', '', $string);
        $string = trim($string, '-');
        $string = preg_replace('~-+~', '-', $string);
        $string = strtolower($string);

        if (empty($string)) {
            return 'n-a';
        }
        return $string;
    }

    public static function unique(string $table, string $string, ?string $excludeId = null): string
    {
        $slug = self::make($string);
        $originalSlug = $slug;
        $counter = 1;

        $db = Database::connect();
        while (true) {
            $sql = "SELECT COUNT(*) FROM {$table} WHERE slug = :slug";
            if ($excludeId) {
                $sql .= " AND id != :id";
            }
            $stmt = $db->prepare($sql);
            $params = [':slug' => $slug];
            if ($excludeId) {
                $params[':id'] = $excludeId;
            }
            $stmt->execute($params);
            $count = $stmt->fetchColumn();

            if ($count == 0) {
                break;
            }

            $counter++;
            $slug = "{$originalSlug}-{$counter}";
        }

        return $slug;
    }
}
