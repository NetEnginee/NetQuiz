<?php
declare(strict_types=1);

namespace App\Repositories;

/**
 * MaterialRepository Contract.
 */
interface MaterialRepositoryInterface
{
    public function getAll(): array;
    public function getById(int $id): ?array;
    public function getByCategory(string $category): array;
    public function create(string $title, string $content, string $category, string $difficulty, ?string $imagePath = null): int;
    public function update(int $id, string $title, string $content, string $category, string $difficulty, ?string $imagePath = null): bool;
    public function delete(int $id): bool;
}
