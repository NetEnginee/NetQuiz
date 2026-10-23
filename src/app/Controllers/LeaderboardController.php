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
    private const CATEGORY_DISPLAY_NAMES = [
        'all' => 'Semua Topik',
        'Routing' => 'Routing',
        'Firewall & NAT' => 'Firewall & NAT',
        'Wireless' => 'Wireless',
        'Network Management' => 'Network Management'
    ];

    public function __construct(
        private AttemptRepositoryInterface $attemptRepo,
        private Request $request
    ) {}

    /**
     * Display Leaderboard Rankings and User Personal Rank.
     * Supports both standard SSR View and Live AJAX/JSON responses.
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

        $activeCategoryLabel = self::CATEGORY_DISPLAY_NAMES[$activeCategory] ?? 'Semua Topik';

        // Check if AJAX / JSON request
        $isAjax = $this->request->isAjax() ||
                  str_contains($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json') ||
                  $this->request->query('ajax') === '1' ||
                  $this->request->query('json') === '1';

        if ($isAjax) {
            return $this->jsonResponse([
                'status' => 'success',
                'activeCategory' => $activeCategory,
                'activeCategoryLabel' => $activeCategoryLabel,
                'leaderboard' => $leaderboard,
                'currentUserId' => $userId,
                'currentUserRank' => $userRankData['rank'],
                'currentUserStats' => $userRankData['stats']
            ]);
        }

        return $this->view('leaderboard/index', [
            'title' => 'Leaderboard | NetQuiz',
            'leaderboard' => $leaderboard,
            'currentUserRank' => $userRankData['rank'],
            'currentUserStats' => $userRankData['stats'],
            'activeCategory' => $activeCategory
        ]);
    }
}
