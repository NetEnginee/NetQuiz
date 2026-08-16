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

        $email = trim((string)$this->request->input('email', ''));
        $password = (string)$this->request->input('password', '');

        if (empty($email) || empty($password)) {
            return $this->jsonResponse([
                'status' => 'error',
                'errors' => ['general' => 'Email dan Password wajib diisi.']
            ]);
        }

        if (!Security::isValidEmail($email)) {
            return $this->jsonResponse([
                'status' => 'error',
                'errors' => ['email' => 'Format email tidak valid.']
            ]);
        }

        // Brute Force Rate Limiting Protection
        $lockSeconds = Security::checkRateLimit($email);
        if ($lockSeconds !== null) {
            $minutes = (int)ceil($lockSeconds / 60);
            return $this->jsonResponse([
                'status' => 'error',
                'errors' => ['general' => "Terlalu banyak percobaan masuk. Silakan coba kembali dalam {$minutes} menit."]
            ]);
        }

        $user = $this->userRepo->findByEmail($email);

        if ($user && password_verify($password, $user['password'])) {
            session_regenerate_id(true);
            Security::generateCsrfToken(true);
            Security::clearLoginAttempts($email);

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

        Security::recordLoginAttempt($email);
        return $this->jsonResponse([
            'status' => 'error',
            'errors' => ['password' => 'Password salah atau email tidak terdaftar.']
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
        unset($_SESSION['user']);
        session_destroy();

        return $this->redirect(BASE_URL . '/login');
    }
}
