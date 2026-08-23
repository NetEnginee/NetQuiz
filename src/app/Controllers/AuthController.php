<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Security;
use App\Repositories\UserRepositoryInterface;

class AuthController extends Controller
{
    public function __construct(
        private UserRepositoryInterface $userRepo,
        private Request $request
    ) {}

    /**
     * Display Login Page.
     */
    public function index(): Response
    {
        Security::preventBFCache();

        if (isset($_SESSION['user'])) {
            $email = isset($_SESSION['user']['email']) ? trim($_SESSION['user']['email']) : '';
            $redirectUrl = (strcasecmp($email, 'admin@routerosquiz.academy') === 0) ? BASE_URL . '/admin' : BASE_URL . '/';
            return $this->redirect($redirectUrl);
        }

        return $this->view('auth/index', [
            'title' => 'Masuk ke NetQuiz',
            'mode' => 'login',
        ]);
    }

    /**
     * API Login Endpoint (AJAX POST).
     */
    public function apiLogin(): Response
    {
        if (!$this->request->isMethod('POST')) {
            return $this->jsonResponse(['status' => 'error', 'message' => 'Method Not Allowed'], 405);
        }

        $csrfToken = (string)$this->request->input('csrf_token', '');
        if (!Security::validateCsrfToken($csrfToken)) {
            return $this->jsonResponse(['status' => 'error', 'message' => 'Sesi tidak valid, silakan muat ulang halaman.'], 403);
        }

        $identifier = trim((string)$this->request->input('email', $this->request->input('username', '')));
        $password = (string)$this->request->input('password', '');

        if (empty($identifier) || empty($password)) {
            return $this->jsonResponse([
                'status' => 'error',
                'errors' => ['general' => 'Email/Username dan Password wajib diisi.']
            ]);
        }

        // If identifier contains @, validate email format
        if (str_contains($identifier, '@') && !Security::isValidEmail($identifier)) {
            return $this->jsonResponse([
                'status' => 'error',
                'errors' => ['email' => 'Format email tidak valid.']
            ]);
        }

        // Brute Force Rate Limiting Protection (per account identifier)
        $lockSeconds = Security::checkRateLimit($identifier);
        if ($lockSeconds !== null) {
            $minutes = (int)ceil($lockSeconds / 60);
            return $this->jsonResponse([
                'status' => 'error',
                'errors' => ['general' => "Terlalu banyak percobaan masuk. Silakan coba kembali dalam {$minutes} menit."]
            ]);
        }

        $user = $this->userRepo->findByUsernameOrEmail($identifier);

        if ($user && password_verify($password, $user['password'])) {
            // Check if user status is active
            if (isset($user['status']) && strcasecmp($user['status'], 'Aktif') !== 0) {
                return $this->jsonResponse([
                    'status' => 'error',
                    'errors' => ['general' => 'Akun Anda berstatus ' . htmlspecialchars($user['status']) . '. Silakan hubungi administrator.']
                ]);
            }

            session_regenerate_id(true);
            Security::generateCsrfToken(true);
            Security::clearLoginAttempts($identifier);

            $_SESSION['user'] = [
                'id' => (int)$user['id'],
                'name' => $user['username'],
                'email' => $user['email']
            ];

            $redirectUrl = (strcasecmp(trim($user['email']), 'admin@routerosquiz.academy') === 0) ? BASE_URL . '/admin' : BASE_URL . '/';
            return $this->jsonResponse([
                'status' => 'success',
                'message' => 'Login berhasil!',
                'redirect' => $redirectUrl
            ]);
        }

        Security::recordLoginAttempt($identifier);
        return $this->jsonResponse([
            'status' => 'error',
            'errors' => ['password' => 'Password salah atau akun tidak terdaftar.']
        ]);
    }

    /**
     * Logout Action.
     */
    public function logout(): Response
    {
        Security::preventBFCache();
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }
        session_destroy();

        return $this->redirect(BASE_URL . '/login');
    }
}
