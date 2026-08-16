<?php
declare(strict_types=1);

namespace App\Repositories;

/**
 * BadgeRepository Contract.
 */
interface BadgeRepositoryInterface
{
    public function getAll(): array;
    public function getById(int $id): ?array;
    public function create(string $title, string $description, string $icon, string $metric, int $targetValue): int;
    public function delete(int $id): bool;
    public function deleteBulk(array $ids): bool;
    public function calculateUserBadges(int $userId): array;
}
