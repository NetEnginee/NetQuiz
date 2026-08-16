<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Authorize;
use App\Core\Role;
use App\Core\Request;
use App\Core\Response;
use App\Repositories\UserRepositoryInterface;

#[Authorize(Role::USER, Role::ADMIN)]
class SettingsController extends Controller
{
    public function __construct(
        private UserRepositoryInterface $userRepo,
        private Request $request
    ) {}

    public function index(): Response
    {
        $userId = (int)($_SESSION['user']['id'] ?? 0);
        $user = $this->userRepo->findById($userId);

        if (!$user) {
            unset($_SESSION['user']);
            return $this->redirect(BASE_URL . '/login');
        }

        return $this->view('settings/index', [
            'title' => 'Pengaturan Profil | NetQuiz',
            'user' => $user
        ]);
    }
}
