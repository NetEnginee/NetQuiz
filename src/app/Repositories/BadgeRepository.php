<?php
declare(strict_types=1);

namespace App\Repositories;

use PDO;
use App\Core\Database;

/**
 * BadgeRepository - Badge and Achievement System Data Access Implementation.
 */
class BadgeRepository implements BadgeRepositoryInterface
{
    private PDO $db;

    public function __construct(?Database $database = null)
    {
        $this->db = ($database ?? Database::getInstance())->getConnection();
    }

    /**
     * Get all badges with earned count statistics.
     */
    public function getAll(): array
    {
        $stmt = $this->db->query("SELECT * FROM badges ORDER BY id DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get single badge by ID.
     */
    public function getById(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM badges WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    /**
     * Create new badge.
     */
    public function create(string $title, string $description, string $icon, string $metric, int $targetValue): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO badges (title, description, icon, metric, target_value) 
            VALUES (:title, :description, :icon, :metric, :target_value)
        ");
        $stmt->execute([
            'title' => $title,
            'description' => $description,
            'icon' => $icon,
            'metric' => $metric,
            'target_value' => $targetValue
        ]);

        return (int)$this->db->lastInsertId();
    }

    /**
     * Delete badge by ID.
     */
    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM badges WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    /**
     * Delete multiple badges in bulk.
     */
    public function deleteBulk(array $ids): bool
    {
        if (empty($ids)) {
            return false;
        }

        $ids = array_map('intval', $ids);
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $stmt = $this->db->prepare("DELETE FROM badges WHERE id IN ({$placeholders})");
        return $stmt->execute($ids);
    }

    /**
     * Calculate user achievements and badge unlock statuses dynamically.
     */
    public function calculateUserBadges(int $userId): array
    {
        // 1. Completed finished quizzes
        $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM quiz_attempts WHERE user_id = :uid AND status = 'finished'");
        $stmt->execute(['uid' => $userId]);
        $completedQuizzes = (int)($stmt->fetch(PDO::FETCH_ASSOC)['count'] ?? 0);

        // 2. Total score
        $stmt = $this->db->prepare("SELECT SUM(score) as total FROM quiz_attempts WHERE user_id = :uid AND status = 'finished'");
        $stmt->execute(['uid' => $userId]);
        $totalScore = (int)($stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0);

        // 3. Category count & scores
        $categories = ['Routing' => 0, 'Firewall & NAT' => 0, 'Wireless' => 0, 'Network Management' => 0];
        $categoryScores = ['Routing' => 0, 'Firewall & NAT' => 0, 'Wireless' => 0, 'Network Management' => 0];

        $stmt = $this->db->prepare("SELECT category, COUNT(*) as count, SUM(score) as score_sum FROM quiz_attempts WHERE user_id = :uid AND status = 'finished' GROUP BY category");
        $stmt->execute(['uid' => $userId]);
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $cat = $row['category'];
            if (isset($categories[$cat])) {
                $categories[$cat] = (int)$row['count'];
                $categoryScores[$cat] = (int)($row['score_sum'] ?? 0);
            }
        }

        // 4. Perfect scores
        $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM quiz_attempts WHERE user_id = :uid AND score = 100 AND status = 'finished'");
        $stmt->execute(['uid' => $userId]);
        $perfectScores = (int)($stmt->fetch(PDO::FETCH_ASSOC)['count'] ?? 0);

        // 5. Total available quizzes in database
        $totalAvailableQuizzes = (int)($this->db->query("SELECT COUNT(*) FROM quizzes")->fetchColumn() ?: 1);
        $completionRate = (int)round(($completedQuizzes / max(1, $totalAvailableQuizzes)) * 100);
        $averageScore = (int)round($totalScore / max(1, $completedQuizzes));

        // 6. Recent activities
        $stmtRecent = $this->db->prepare("
            SELECT qa.*, q.title as quiz_title, q.duration 
            FROM quiz_attempts qa 
            LEFT JOIN quizzes q ON qa.quiz_id = q.id 
            WHERE qa.user_id = :uid 
            ORDER BY qa.id DESC 
            LIMIT 5
        ");
        $stmtRecent->execute(['uid' => $userId]);
        $recentActivities = $stmtRecent->fetchAll(PDO::FETCH_ASSOC);

        // Badges calculation
        $badgesDb = $this->getAll();
        $unlocked = [];
        $locked = [];
        $all = [];

        foreach ($badgesDb as $b) {
            $progress = match ($b['metric']) {
                'completed_quizzes' => $completedQuizzes,
                'total_score' => $totalScore,
                'perfect_scores' => $perfectScores,
                'category_routing' => $categories['Routing'] ?? 0,
                'category_firewall' => $categories['Firewall & NAT'] ?? 0,
                'category_wireless' => $categories['Wireless'] ?? 0,
                'category_network' => $categories['Network Management'] ?? 0,
                default => $completedQuizzes
            };

            $target = max(1, (int)$b['target_value']);
            $progVal = min($progress, $target);
            $isUnlocked = $progVal >= $target;

            $badgeData = [
                'id' => (int)$b['id'],
                'title' => $b['title'],
                'description' => $b['description'],
                'icon' => $b['icon'],
                'progress' => $progVal,
                'max' => $target,
                'unlocked' => $isUnlocked,
                'percent' => (int)round(($progVal / $target) * 100)
            ];

            $all[] = $badgeData;
            if ($isUnlocked) {
                $unlocked[] = $badgeData;
            } else {
                $locked[] = $badgeData;
            }
        }

        $nextBadge = null;
        if (!empty($locked)) {
            $tempLocked = $locked;
            usort($tempLocked, fn($a, $b) => $b['percent'] <=> $a['percent']);
            $nextBadge = $tempLocked[0];
        }

        return [
            'all' => $all,
            'unlocked' => $unlocked,
            'locked' => $locked,
            'next_badge' => $nextBadge,
            'stats' => [
                'completed_quizzes' => $completedQuizzes,
                'total_score' => $totalScore,
                'perfect_scores' => $perfectScores,
                'categories' => $categories,
                'category_scores' => $categoryScores,
                'completion_rate' => $completionRate,
                'average_score' => $averageScore,
                'recent_activities' => $recentActivities
            ]
        ];
    }
}
