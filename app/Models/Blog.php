<?php

namespace App\Models;

use App\Models\Model;
use App\Database;
use Exception;
use PDO;

class Blog extends Model
{
    protected static string $table = 'blogs';

    public static function allWithRelations(): array
    {
        $db = Database::connect();
        $sql = "SELECT b.*, c.name as category_name 
                FROM blogs b 
                LEFT JOIN categories c ON b.category_id = c.id 
                ORDER BY b.created_at DESC";
        $stmt = $db->query($sql);
        $blogs = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Fetch tags for each blog
        foreach ($blogs as &$blog) {
            $blog['tags'] = self::getTags($blog['id']);
        }

        return $blogs;
    }

    public static function findWithRelations(string $id): ?array
    {
        $blog = self::find($id);
        if ($blog) {
            $blog['tags'] = self::getTags($id);
        }
        return $blog;
    }

    public static function getTags(string $blogId): array
    {
        $db = Database::connect();
        $sql = "SELECT t.* FROM tags t 
                JOIN blog_tags bt ON t.id = bt.tag_id 
                WHERE bt.blog_id = :blog_id";
        $stmt = $db->prepare($sql);
        $stmt->execute([':blog_id' => $blogId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function create(array $data, array $tagIds = []): bool
    {
        $db = Database::connect();
        try {
            $db->beginTransaction();

            $stmt = $db->prepare("INSERT INTO blogs (id, title, slug, excerpt, content, featured_image, category_id, status, published_at) 
                                  VALUES (:id, :title, :slug, :excerpt, :content, :featured_image, :category_id, :status, :published_at)");

            $stmt->execute([
                ':id' => $data['id'],
                ':title' => $data['title'],
                ':slug' => $data['slug'],
                ':excerpt' => $data['excerpt'] ?? null,
                ':content' => $data['content'] ?? null,
                ':featured_image' => $data['featured_image'] ?? null,
                ':category_id' => $data['category_id'] ?: null,
                ':status' => $data['status'] ?? 'draft',
                ':published_at' => ($data['status'] === 'published') ? date('Y-m-d H:i:s') : null,
            ]);

            // Sync Tags
            if (!empty($tagIds)) {
                $stmtTag = $db->prepare("INSERT INTO blog_tags (blog_id, tag_id) VALUES (:blog_id, :tag_id)");
                foreach ($tagIds as $tagId) {
                    $stmtTag->execute([
                        ':blog_id' => $data['id'],
                        ':tag_id' => $tagId
                    ]);
                }
            }

            $db->commit();
            return true;
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    public static function update(string $id, array $data, array $tagIds = []): bool
    {
        $db = Database::connect();
        try {
            $db->beginTransaction();

            $existing = self::find($id);
            if (!$existing) {
                throw new Exception("Blog ID tidak ditemukan.");
            }

            $publishedAt = $existing['published_at'];
            if (($data['status'] ?? 'draft') === 'published' && !$publishedAt) {
                $publishedAt = date('Y-m-d H:i:s');
            } elseif (($data['status'] ?? 'draft') === 'draft') {
                $publishedAt = null;
            }

            $imageSql = "";
            $params = [
                ':id' => $id,
                ':title' => $data['title'],
                ':slug' => $data['slug'],
                ':excerpt' => $data['excerpt'] ?? null,
                ':content' => $data['content'] ?? null,
                ':category_id' => $data['category_id'] ?: null,
                ':status' => $data['status'] ?? 'draft',
                ':published_at' => $publishedAt
            ];

            if (isset($data['featured_image'])) {
                $imageSql = ", featured_image = :featured_image";
                $params[':featured_image'] = $data['featured_image'];
            }

            $stmt = $db->prepare("UPDATE blogs 
                                  SET title = :title, slug = :slug, excerpt = :excerpt, content = :content, category_id = :category_id, status = :status, published_at = :published_at {$imageSql} 
                                  WHERE id = :id");
            $stmt->execute($params);

            // Delete old tags relation
            $stmtDel = $db->prepare("DELETE FROM blog_tags WHERE blog_id = :blog_id");
            $stmtDel->execute([':blog_id' => $id]);

            // Sync new tags
            if (!empty($tagIds)) {
                $stmtTag = $db->prepare("INSERT INTO blog_tags (blog_id, tag_id) VALUES (:blog_id, :tag_id)");
                foreach ($tagIds as $tagId) {
                    $stmtTag->execute([
                        ':blog_id' => $id,
                        ':tag_id' => $tagId
                    ]);
                }
            }

            $db->commit();
            return true;
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }
}
