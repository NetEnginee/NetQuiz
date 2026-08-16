<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Authorize;
use App\Core\Role;
use App\Core\Request;
use App\Core\Response;
use App\Repositories\AttemptRepositoryInterface;

#[Authorize(Role::USER, Role::ADMIN)]
class LeaderboardController extends Controller
{
    public function __construct(
        private AttemptRepositoryInterface $attemptRepo,
        private Request $request
    ) {}

    /**
     * Display Leaderboard Rankings and User Personal Rank.
     */
    public function index(): Response
    {
        $activeCategory = (string)$this->request->query('category', 'all');
        $categoryFilter = ($activeCategory !== 'all') ? $activeCategory : null;

        $userId = (int)($_SESSION['user']['id'] ?? 0);

        // Fetch top 10 rankings
        $leaderboard = $this->attemptRepo->getLeaderboard($categoryFilter, 10);

        // Fetch current user position and statistics
        $userRankData = $this->attemptRepo->getUserRank($userId, $categoryFilter);

        return $this->view('leaderboard/index', [
            'title' => 'Leaderboard | NetQuiz',
            'leaderboard' => $leaderboard,
            'currentUserRank' => $userRankData['rank'],
            'currentUserStats' => $userRankData['stats'],
            'activeCategory' => $activeCategory
        ]);
    }
}
