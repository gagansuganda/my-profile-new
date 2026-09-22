<?php

namespace App\Models;

use App\Models\Model;
use App\Database;
use Exception;
use PDO;

class Project extends Model
{
    protected static string $table = 'projects';

    public static function allWithRelations(): array
    {
        $db = Database::connect();
        $sql = "SELECT p.*, c.name as category_name 
                FROM projects p 
                LEFT JOIN categories c ON p.category_id = c.id 
                ORDER BY p.created_at DESC";
        $stmt = $db->query($sql);
        $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Fetch tags for each project
        foreach ($projects as &$project) {
            $project['tags'] = self::getTags($project['id']);
        }

        return $projects;
    }

    public static function findWithRelations(string $id): ?array
    {
        $project = self::find($id);
        if ($project) {
            $project['tags'] = self::getTags($id);
        }
        return $project;
    }

    public static function getTags(string $projectId): array
    {
        $db = Database::connect();
        $sql = "SELECT t.* FROM tags t 
                JOIN project_tags pt ON t.id = pt.tag_id 
                WHERE pt.project_id = :project_id";
        $stmt = $db->prepare($sql);
        $stmt->execute([':project_id' => $projectId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function create(array $data, array $tagIds = []): bool
    {
        $db = Database::connect();
        try {
            $db->beginTransaction();

            $stmt = $db->prepare("INSERT INTO projects (id, title, slug, description, image, project_url, category_id, status) 
                                  VALUES (:id, :title, :slug, :description, :image, :project_url, :category_id, :status)");

            $stmt->execute([
                ':id' => $data['id'],
                ':title' => $data['title'],
                ':slug' => $data['slug'],
                ':description' => $data['description'] ?? null,
                ':image' => $data['image'] ?? null,
                ':project_url' => $data['project_url'] ?? null,
                ':category_id' => $data['category_id'] ?: null,
                ':status' => $data['status'] ?? 'draft',
            ]);

            // Sync tags
            if (!empty($tagIds)) {
                $stmtTag = $db->prepare("INSERT INTO project_tags (project_id, tag_id) VALUES (:project_id, :tag_id)");
                foreach ($tagIds as $tagId) {
                    $stmtTag->execute([
                        ':project_id' => $data['id'],
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

            $imageSql = "";
            $params = [
                ':id' => $id,
                ':title' => $data['title'],
                ':slug' => $data['slug'],
                ':description' => $data['description'] ?? null,
                ':project_url' => $data['project_url'] ?? null,
                ':category_id' => $data['category_id'] ?: null,
                ':status' => $data['status'] ?? 'draft'
            ];

            if (isset($data['image'])) {
                $imageSql = ", image = :image";
                $params[':image'] = $data['image'];
            }

            $stmt = $db->prepare("UPDATE projects 
                                  SET title = :title, slug = :slug, description = :description, project_url = :project_url, category_id = :category_id, status = :status {$imageSql} 
                                  WHERE id = :id");
            $stmt->execute($params);

            // Delete old tags
            $stmtDel = $db->prepare("DELETE FROM project_tags WHERE project_id = :project_id");
            $stmtDel->execute([':project_id' => $id]);

            // Sync new tags
            if (!empty($tagIds)) {
                $stmtTag = $db->prepare("INSERT INTO project_tags (project_id, tag_id) VALUES (:project_id, :tag_id)");
                foreach ($tagIds as $tagId) {
                    $stmtTag->execute([
                        ':project_id' => $id,
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
