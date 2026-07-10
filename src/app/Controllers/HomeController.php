<?php
declare(strict_types=1);
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Authorize;
use App\Core\Role;

#[Authorize(Role::USER, Role::ADMIN)]
class HomeController extends Controller
{
    public function index()
    {
        // Redirect admin users to the Admin dashboard
        if ($_SESSION['user']['email'] === 'admin@routerosquiz.academy') {
            header('Location: ' . BASE_URL . '/admin');
            exit;
        }

        // Initialize default statistics
        $completedQuizzes = 0;
        $completionRate = 0;
        $totalScore = 0;
        $averageScore = 0;

        $categories = [
            'Routing' => 0,
            'Firewall & NAT' => 0,
            'Wireless' => 0,
            'Network Management' => 0
        ];

        $categoryScores = [
            'Routing' => 0,
            'Firewall & NAT' => 0,
            'Wireless' => 0,
            'Network Management' => 0
        ];

        try {
            $db = \App\Core\Database::getInstance()->getConnection();
            $userId = $_SESSION['user']['id'];

            // 1. Fetch total completed quizzes count for this user
            $stmt = $db->prepare("SELECT COUNT(*) as count FROM quiz_attempts WHERE user_id = :uid AND status = 'finished'");
            $stmt->execute(['uid' => $userId]);
            $completedQuizzes = (int) ($stmt->fetch(\PDO::FETCH_ASSOC)['count'] ?? 0);

            // 2. Fetch total accumulated score for this user
            $stmt = $db->prepare("SELECT SUM(score) as total FROM quiz_attempts WHERE user_id = :uid");
            $stmt->execute(['uid' => $userId]);
            $totalScore = (int) ($stmt->fetch(\PDO::FETCH_ASSOC)['total'] ?? 0);

            // 3. Calculate average score
            if ($completedQuizzes > 0) {
                $averageScore = (int) round($totalScore / $completedQuizzes);
            }

            // 4. Calculate completion rate (Assuming 10 total system quizzes in the system)
            $totalSystemQuizzes = 10;
            if ($totalSystemQuizzes > 0) {
                $completionRate = (int) round(($completedQuizzes / $totalSystemQuizzes) * 100);
                if ($completionRate > 100) {
                    $completionRate = 100;
                }
            }

            // 5. Fetch count per category
            $stmt = $db->prepare("SELECT category, COUNT(*) as count FROM quiz_attempts WHERE user_id = :uid AND status = 'finished' GROUP BY category");
            $stmt->execute(['uid' => $userId]);
            $categoryCountsResult = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            foreach ($categoryCountsResult as $row) {
                $catName = $row['category'];
                if (array_key_exists($catName, $categories)) {
                    $categories[$catName] = (int) $row['count'];
                }
            }

            // 6. Fetch score per category
            $stmt = $db->prepare("SELECT category, SUM(score) as total_score FROM quiz_attempts WHERE user_id = :uid GROUP BY category");
            $stmt->execute(['uid' => $userId]);
            $categoryScoresResult = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            foreach ($categoryScoresResult as $row) {
                $catName = $row['category'];
                if (array_key_exists($catName, $categoryScores)) {
                    $categoryScores[$catName] = (int) $row['total_score'];
                }
            }

            // 7. Fetch recent activities
            $stmt = $db->prepare("SELECT category, score, created_at, status FROM quiz_attempts WHERE user_id = :uid ORDER BY created_at DESC LIMIT 5");
            $stmt->execute(['uid' => $userId]);
            $recentActivities = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            // 8. Fetch count of perfect scores
            $stmt = $db->prepare("SELECT COUNT(*) as count FROM quiz_attempts WHERE user_id = :uid AND score = 100 AND status = 'finished'");
            $stmt->execute(['uid' => $userId]);
            $perfectScores = (int) ($stmt->fetch(\PDO::FETCH_ASSOC)['count'] ?? 0);

            // 9. Calculate Achievements & Badges
            $stmtBadges = $db->query("SELECT * FROM badges ORDER BY id ASC");
            $badgesDb = $stmtBadges->fetchAll(\PDO::FETCH_ASSOC);

            $badgesDef = [];
            foreach ($badgesDb as $b) {
                $progress = 0;
                switch ($b['metric']) {
                    case 'completed_quizzes':
                        $progress = $completedQuizzes;
                        break;
                    case 'total_score':
                        $progress = $totalScore;
                        break;
                    case 'perfect_scores':
                        $progress = $perfectScores;
                        break;
                    case 'category_routing':
                        $progress = $categories['Routing'] ?? 0;
                        break;
                    case 'category_firewall':
                        $progress = $categories['Firewall & NAT'] ?? 0;
                        break;
                    case 'category_wireless':
                        $progress = $categories['Wireless'] ?? 0;
                        break;
                    case 'category_network':
                        $progress = $categories['Network Management'] ?? 0;
                        break;
                }
                $badgesDef[] = [
                    'id' => $b['id'],
                    'title' => $b['title'],
                    'description' => $b['description'],
                    'icon' => $b['icon'],
                    'progress' => $progress,
                    'max' => (int) $b['target_value']
                ];
            }

            $unlockedBadges = [];
            $lockedAchievements = [];

            foreach ($badgesDef as $b) {
                $progVal = min($b['progress'], $b['max']);
                $isUnlocked = $progVal >= $b['max'];

                $badgeData = [
                    'id' => $b['id'],
                    'title' => $b['title'],
                    'description' => $b['description'],
                    'icon' => $b['icon'],
                    'progress' => $progVal,
                    'max' => $b['max'],
                    'unlocked' => $isUnlocked,
                    'percent' => round(($progVal / $b['max']) * 100)
                ];

                if ($isUnlocked) {
                    $unlockedBadges[] = $badgeData;
                } else {
                    $lockedAchievements[] = $badgeData;
                }
            }

            // Find next badge (locked one with highest progress percentage)
            $nextBadge = null;
            if (!empty($lockedAchievements)) {
                $tempLocked = $lockedAchievements;
                usort($tempLocked, function ($a, $b) {
                    return $b['percent'] <=> $a['percent'];
                });
                $nextBadge = $tempLocked[0];
            }

        } catch (\PDOException $e) {
            // Fallback silently if database tables are temporarily not accessible
            $recentActivities = [];
            $unlockedBadges = [];
            $lockedAchievements = [];
            $nextBadge = null;
        }

        $userStats = [
            'completed_quizzes' => $completedQuizzes,
            'completion_rate' => $completionRate,
            'total_score' => $totalScore,
            'average_score' => $averageScore,
            'categories' => $categories,
            'category_scores' => $categoryScores,
            'recent_activities' => $recentActivities,
            'unlocked_badges' => $unlockedBadges,
            'locked_achievements' => $lockedAchievements,
            'next_badge' => $nextBadge
        ];

        $this->view('home/index', [
            'title' => 'Dashboard | RouterOS Quiz',
            'stats' => $userStats
        ]);
    }

    public function dashboardRedirect()
    {
        if ($_SESSION['user']['email'] === 'admin@routerosquiz.academy') {
            header('Location: ' . BASE_URL . '/admin');
            exit;
        }

        header('Location: ' . BASE_URL . '/');
        exit;
    }
}
