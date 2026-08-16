<?php
declare(strict_types=1);

namespace App\Repositories;

use PDO;
use App\Core\Database;

/**
 * MaterialRepository - Article and Learning Material Data Access.
 */
class MaterialRepository implements MaterialRepositoryInterface
{
    private PDO $db;

    public function __construct(?Database $database = null)
    {
        $this->db = ($database ?? Database::getInstance())->getConnection();
    }

    /**
     * Fetch all learning materials ordered by newest.
     */
    public function getAll(): array
    {
        $stmt = $this->db->query("SELECT * FROM materials ORDER BY id DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get learning material by ID.
     */
    public function getById(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM materials WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    /**
     * Get learning materials in the same category.
     */
    public function getByCategory(string $category): array
    {
        $stmt = $this->db->prepare("SELECT * FROM materials WHERE category = :category ORDER BY id DESC");
        $stmt->execute(['category' => $category]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Create new learning material.
     */
    public function create(
        string $title,
        string $content,
        string $category,
        string $difficulty,
        ?string $imagePath = null
    ): int {
        $stmt = $this->db->prepare("
            INSERT INTO materials (title, content, category, difficulty, image_path) 
            VALUES (:title, :content, :category, :difficulty, :image_path)
        ");
        $stmt->execute([
            'title' => $title,
            'content' => $content,
            'category' => $category,
            'difficulty' => $difficulty,
            'image_path' => $imagePath
        ]);

        return (int)$this->db->lastInsertId();
    }

    /**
     * Update existing learning material.
     */
    public function update(
        int $id,
        string $title,
        string $content,
        string $category,
        string $difficulty,
        ?string $imagePath = null
    ): bool {
        $sql = "UPDATE materials SET title = :title, content = :content, category = :category, difficulty = :difficulty";
        $params = [
            'id' => $id,
            'title' => $title,
            'content' => $content,
            'category' => $category,
            'difficulty' => $difficulty
        ];

        if ($imagePath !== null) {
            $sql .= ", image_path = :image_path";
            $params['image_path'] = $imagePath;
        }

        $sql .= " WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    /**
     * Delete learning material.
     */
    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM materials WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
}
