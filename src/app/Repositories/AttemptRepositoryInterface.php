<?php
declare(strict_types=1);

namespace App\Repositories;

/**
 * AttemptRepository Contract.
 */
interface AttemptRepositoryInterface
{
    public function getPausedAttempt(int $userId, int $quizId): ?array;
    public function savePausedAttempt(int $userId, int $quizId, string $category, array $answers, int $timeLeft): bool;
    public function clearPausedAttempt(int $userId, int $quizId): bool;
    public function getAttemptById(int $id): ?array;
    public function getFinishedAttempt(int $userId, int $quizId): ?array;
    public function recordFinishedAttempt(int $userId, int $quizId, string $category, int $score, array $userAnswers): int;
    public function getUserAttempts(int $userId): array;
    public function getLeaderboard(?string $category = null, int $limit = 10): array;
    public function getUserRank(int $userId, ?string $category = null): array;
}
