<?php
declare(strict_types=1);

namespace App\Repositories;

use PDO;
use App\Core\Database;

/**
 * AttemptRepository - Handles Quiz Attempts, Paused States, Scores, and Leaderboard.
 */
class AttemptRepository implements AttemptRepositoryInterface
{
    private PDO $db;
    private Database $database;

    public function __construct(?Database $database = null)
    {
        $this->database = $database ?? Database::getInstance();
        $this->db = $this->database->getConnection();
    }

    /**
     * Get user's active paused quiz attempt.
     */
    public function getPausedAttempt(int $userId, int $quizId): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM quiz_attempts WHERE user_id = :user_id AND quiz_id = :quiz_id AND status = 'paused' LIMIT 1");
        $stmt->execute(['user_id' => $userId, 'quiz_id' => $quizId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    /**
     * Save or update paused quiz attempt with timer and answers.
     */
    public function savePausedAttempt(int $userId, int $quizId, string $category, array $answers, int $timeLeft): bool
    {
        $payload = json_encode([
            'answers' => $answers,
            'time_left' => $timeLeft
        ], JSON_UNESCAPED_UNICODE);

        $pausedAt = date('Y-m-d H:i:s');
        $existing = $this->getPausedAttempt($userId, $quizId);

        if ($existing) {
            $stmt = $this->db->prepare("UPDATE quiz_attempts SET user_answers = :payload, created_at = :paused_at WHERE id = :id");
            return $stmt->execute([
                'payload' => $payload,
                'paused_at' => $pausedAt,
                'id' => $existing['id']
            ]);
        }

        $stmt = $this->db->prepare("
            INSERT INTO quiz_attempts (user_id, quiz_id, category, score, status, user_answers, created_at) 
            VALUES (:user_id, :quiz_id, :category, 0, 'paused', :payload, :paused_at)
        ");
        return $stmt->execute([
            'user_id' => $userId,
            'quiz_id' => $quizId,
            'category' => $category,
            'payload' => $payload,
            'paused_at' => $pausedAt
        ]);
    }

    /**
     * Clear paused attempt for user and quiz.
     */
    public function clearPausedAttempt(int $userId, int $quizId): bool
    {
        $stmt = $this->db->prepare("DELETE FROM quiz_attempts WHERE user_id = :user_id AND quiz_id = :quiz_id AND status = 'paused'");
        return $stmt->execute(['user_id' => $userId, 'quiz_id' => $quizId]);
    }

    /**
     * Get attempt record by ID.
     */
    public function getAttemptById(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM quiz_attempts WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    /**
     * Get finished attempt record for user and quiz.
     */
    public function getFinishedAttempt(int $userId, int $quizId): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM quiz_attempts WHERE user_id = :user_id AND quiz_id = :quiz_id AND status = 'finished' ORDER BY id DESC LIMIT 1");
        $stmt->execute(['user_id' => $userId, 'quiz_id' => $quizId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    /**
     * Record a finished attempt and clear paused attempt atomically.
     */
    public function recordFinishedAttempt(int $userId, int $quizId, string $category, int $score, array $userAnswers): int
    {
        return $this->database->transaction(function (PDO $db) use ($userId, $quizId, $category, $score, $userAnswers) {
            // 1. Clear any paused attempt
            $delStmt = $db->prepare("DELETE FROM quiz_attempts WHERE user_id = :user_id AND quiz_id = :quiz_id AND status = 'paused'");
            $delStmt->execute(['user_id' => $userId, 'quiz_id' => $quizId]);

            // 2. Insert finished attempt
            $jsonAnswers = json_encode($userAnswers, JSON_UNESCAPED_UNICODE);
            $stmt = $db->prepare("
                INSERT INTO quiz_attempts (user_id, quiz_id, category, score, status, user_answers) 
                VALUES (:user_id, :quiz_id, :category, :score, 'finished', :answers)
            ");
            $stmt->execute([
                'user_id' => $userId,
                'quiz_id' => $quizId,
                'category' => $category,
                'score' => $score,
                'answers' => $jsonAnswers
            ]);

            return (int)$db->lastInsertId();
        });
    }

    /**
     * Get all attempts for a user.
     */
    public function getUserAttempts(int $userId): array
    {
        $stmt = $this->db->prepare("SELECT * FROM quiz_attempts WHERE user_id = :user_id ORDER BY id DESC");
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Query leaderboard rankings.
     */
    public function getLeaderboard(?string $category = null, int $limit = 10): array
    {
        $joinConditions = ["u.id = qa.user_id", "qa.status = 'finished'"];
        $params = [];

        $validCategories = ['Routing', 'Firewall & NAT', 'Wireless', 'Network Management'];
        if ($category !== null && in_array($category, $validCategories, true)) {
            $joinConditions[] = "qa.category = :category";
            $params['category'] = $category;
        }

        $joinSql = implode(" AND ", $joinConditions);

        $query = "
            SELECT 
                u.id, 
                u.username, 
                COALESCE(SUM(qa.score), 0) as total_score, 
                COUNT(qa.id) as completed_quizzes 
            FROM users u
            LEFT JOIN quiz_attempts qa ON {$joinSql}
            WHERE LOWER(TRIM(u.email)) != 'admin@routerosquiz.academy'
            GROUP BY u.id, u.username
            ORDER BY total_score DESC, completed_quizzes DESC
        ";

        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        $all = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_slice($all, 0, $limit);
    }

    /**
     * Get single user rank and score in leaderboard.
     */
    public function getUserRank(int $userId, ?string $category = null): array
    {
        $joinConditions = ["u.id = qa.user_id", "qa.status = 'finished'"];
        $params = [];

        $validCategories = ['Routing', 'Firewall & NAT', 'Wireless', 'Network Management'];
        if ($category !== null && in_array($category, $validCategories, true)) {
            $joinConditions[] = "qa.category = :category";
            $params['category'] = $category;
        }

        $joinSql = implode(" AND ", $joinConditions);

        $query = "
            SELECT 
                u.id, 
                u.username, 
                COALESCE(SUM(qa.score), 0) as total_score, 
                COUNT(qa.id) as completed_quizzes 
            FROM users u
            LEFT JOIN quiz_attempts qa ON {$joinSql}
            WHERE LOWER(TRIM(u.email)) != 'admin@routerosquiz.academy'
            GROUP BY u.id, u.username
            ORDER BY total_score DESC, completed_quizzes DESC
        ";

        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        $all = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $rank = 0;
        $stats = null;

        foreach ($all as $index => $row) {
            if ((int)$row['id'] === $userId) {
                $rank = $index + 1;
                $stats = $row;
                break;
            }
        }

        return [
            'rank' => $rank,
            'stats' => $stats
        ];
    }
}
