<?php
declare(strict_types=1);
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Authorize;
use App\Core\Role;
use PDO;
use PDOException;

#[Authorize(Role::USER)]
class LeaderboardController extends Controller
{
    public function index()
    {
        // 1. Parse active category filter from query parameters
        $activeCategory = $_GET['category'] ?? 'all';
        $validCategories = ['Routing', 'Firewall & NAT', 'Wireless', 'Network Management'];

        // 2. Build the JOIN conditions dynamically based on active category
        $joinConditions = ["u.id = qa.user_id", "qa.status = 'finished'"];
        $params = [];

        if (in_array($activeCategory, $validCategories)) {
            $joinConditions[] = "qa.category = :category";
            $params['category'] = $activeCategory;
        }

        $joinSql = implode(" AND ", $joinConditions);

        $leaderboard = [];
        $currentUserRank = 0;
        $currentUserStats = null;
        $userId = $_SESSION['user']['id'];

        try {
            $db = Database::getInstance()->getConnection();

            // Query complete rankings across all users to calculate ranks
            $query = "
                SELECT 
                    u.id, 
                    u.username, 
                    COALESCE(SUM(qa.score), 0) as total_score, 
                    COUNT(qa.id) as completed_quizzes 
                FROM users u
                LEFT JOIN quiz_attempts qa ON {$joinSql}
                WHERE u.email != 'admin@routerosquiz.academy'
                GROUP BY u.id, u.username
                ORDER BY total_score DESC, completed_quizzes DESC
            ";

            $stmt = $db->prepare($query);
            $stmt->execute($params);
            $leaderboard = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // 3. Find current user's position in the full list
            foreach ($leaderboard as $index => $row) {
                if ($row['id'] == $userId) {
                    $currentUserRank = $index + 1;
                    $currentUserStats = $row;
                    break;
                }
            }

        } catch (PDOException $e) {
            // Fallback
        }

        // 4. Slice the rankings to only show top 10 users on the main list
        $topUsers = array_slice($leaderboard, 0, 10);

        $this->view('leaderboard/index', [
            'title' => 'Leaderboard | RouterOS Quiz',
            'leaderboard' => $topUsers,
            'currentUserRank' => $currentUserRank,
            'currentUserStats' => $currentUserStats,
            'activeCategory' => $activeCategory
        ]);
    }
}
