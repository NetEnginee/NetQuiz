<?php
declare(strict_types=1);

namespace App\Repositories;

/**
 * QuizRepository Contract.
 */
interface QuizRepositoryInterface
{
    public function getAll(): array;
    public function getById(int $id): ?array;
    public function getWithQuestions(int $id): ?array;
    public function getByCategory(string $category): array;
    public function create(string $title, string $description, string $category, int $duration, string $difficulty, ?string $imagePath = null): int;
    public function createWithQuestions(array $quizData, array $questionsData): int;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
    public function getCategorizedQuizzesWithUserStatus(int $userId, ?string $activeDifficulty = null): array;
}
