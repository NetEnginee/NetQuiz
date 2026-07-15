<?php
declare(strict_types=1);

namespace App\Repositories;

use PDO;
use App\Core\Database;

/**
 * MaterialRepository - Handles database operations for Material entity.
 */
class MaterialRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Get all materials.
     */
    public function getAll(): array
    {
        $stmt = $this->db->prepare("SELECT id, title, category, difficulty, image_path, created_at, updated_at FROM materials ORDER BY created_at DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get material by ID.
     */
    public function getById(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM materials WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        $material = $stmt->fetch();
        return $material ?: null;
    }

    /**
     * Get materials by category.
     */
    public function getByCategory(string $category): array
    {
        $stmt = $this->db->prepare("SELECT * FROM materials WHERE category = :category ORDER BY created_at DESC");
        $stmt->execute(['category' => $category]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Create a new learning material.
     */
    public function create(string $title, string $content, string $category, string $difficulty, ?string $image_path = null): int
    {
        $stmt = $this->db->prepare(
            "INSERT INTO materials (title, content, category, difficulty, image_path) 
             VALUES (:title, :content, :category, :difficulty, :image_path)"
        );
        $stmt->execute([
            'title' => $title,
            'content' => $content,
            'category' => $category,
            'difficulty' => $difficulty,
            'image_path' => $image_path
        ]);

        return (int)$this->db->lastInsertId();
    }

    /**
     * Update an existing material.
     */
    public function update(int $id, string $title, string $content, string $category, string $difficulty, ?string $image_path = null): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE materials 
             SET title = :title, content = :content, category = :category, difficulty = :difficulty, image_path = :image_path
             WHERE id = :id"
        );
        return $stmt->execute([
            'title' => $title,
            'content' => $content,
            'category' => $category,
            'difficulty' => $difficulty,
            'image_path' => $image_path,
            'id' => $id
        ]);
    }

    /**
     * Delete a material by ID.
     */
    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM materials WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
}
