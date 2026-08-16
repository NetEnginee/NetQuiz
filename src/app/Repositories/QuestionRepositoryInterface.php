<?php
declare(strict_types=1);

namespace App\Repositories;

/**
 * QuestionRepository Contract.
 */
interface QuestionRepositoryInterface
{
    public function getByQuizId(int $quizId): array;
    public function getById(int $id): ?array;
    public function create(
        int $quizId,
        string $question,
        string $optionA,
        string $optionB,
        string $optionC,
        string $optionD,
        string $correct,
        ?string $explanation = null,
        ?string $imagePath = null
    ): int;
    public function createBulk(int $quizId, array $questions): bool;
    public function deleteByQuizId(int $quizId): bool;
    public function delete(int $id): bool;
}
