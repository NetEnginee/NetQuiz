<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Authorize;
use App\Core\Role;
use App\Core\Request;
use App\Core\Response;
use App\Core\Security;
use App\Repositories\QuizRepositoryInterface;
use App\Repositories\MaterialRepositoryInterface;
use App\Repositories\BadgeRepositoryInterface;

#[Authorize(Role::USER, Role::ADMIN)]
class HomeController extends Controller
{
    public function __construct(
        private QuizRepositoryInterface $quizRepo,
        private MaterialRepositoryInterface $materialRepo,
        private BadgeRepositoryInterface $badgeRepo,
        private Request $request
    ) {}

    /**
     * Display Student Home Dashboard.
     */
    public function index(): Response
    {
        $userId = (int)($_SESSION['user']['id'] ?? 0);

        // Fetch quizzes with user completion flags
        $categorized = $this->quizRepo->getCategorizedQuizzesWithUserStatus($userId);

        // Fetch recent learning materials
        $materials = array_slice($this->materialRepo->getAll(), 0, 4);

        // Fetch dynamic badge calculations
        $badgesData = $this->badgeRepo->calculateUserBadges($userId);

        return $this->view('home/index', [
            'title' => 'Dashboard | NetQuiz',
            'categorized' => $categorized,
            'materials' => $materials,
            'badges' => $badgesData['all'],
            'unlockedBadges' => $badgesData['unlocked'],
            'stats' => $badgesData['stats']
        ]);
    }

    /**
     * Compatibility route redirecting to home.
     */
    public function dashboardRedirect(): Response
    {
        $email = $_SESSION['user']['email'] ?? '';
        if (Security::isAdminEmail($email)) {
            return $this->redirect(BASE_URL . '/admin');
        }

        return $this->redirect(BASE_URL . '/');
    }
}
