<?php
declare(strict_types=1);
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Repositories\UserRepository;
use PDO;
use PDOException;

class AuthController extends Controller
{

    /**
     * Display the Auth Page (Login / Sign Up)
     */
    public function index()
    {
        \App\Core\Security::preventBFCache();
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $mode = (strpos($uri, 'signup') !== false) ? 'signup' : 'login';

        if ($mode === 'signup') {
            header('Location: ' . BASE_URL . '/login');
            exit;
        }

        if (isset($_SESSION['user'])) {
            $email = isset($_SESSION['user']['email']) ? trim($_SESSION['user']['email']) : '';
            $redirectUrl = (strcasecmp($email, 'admin@routerosquiz.academy') === 0) ? BASE_URL . '/admin' : BASE_URL . '/';
            header('Location: ' . $redirectUrl);
            exit;
        }

        $this->view('auth/index', [
            'title' => 'Masuk ke NetQuiz',
            'mode' => 'login'
        ]);
    }

    /**
     * API Endpoint: Handle Login Submission (AJAX POST)
     */
    public function apiLogin()
    {
        header('Content-Type: application/json');

        // Only accept POST requests
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['status' => 'error', 'message' => 'Method Not Allowed']);
            exit;
        }

        // Get post input
        $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;

        $csrfToken = $input['csrf_token'] ?? '';
        if (!\App\Core\Security::validateCsrfToken($csrfToken)) {
            $this->jsonResponse(['success' => false, 'message' => 'Sesi tidak valid, silakan muat ulang halaman.'], 403);
        }

        $email = trim($input['email'] ?? '');
        $password = $input['password'] ?? '';

        $email = \App\Core\Security::sanitizeString($email);
        if (!\App\Core\Security::isValidEmail($email)) {
            $this->jsonResponse(['success' => false, 'message' => 'Format email tidak valid.'], 400);
        }
        if (empty($password) || strlen($password) > 128) {
            $this->jsonResponse(['success' => false, 'message' => 'Password tidak valid.'], 400);
        }

        // Server-side validation
        if (empty($email) || empty($password)) {
            echo json_encode(['status' => 'error', 'errors' => ['general' => 'Email dan Password wajib diisi.']]);
            exit;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(['status' => 'error', 'errors' => ['email' => 'Format email tidak valid.']]);
            exit;
        }

        // Check database connection and user query
        try {
            $dbInstance = Database::getInstance();
            $db = $dbInstance->getConnection();

            // Check if users table exists, create it if not
            $this->checkAndCreateUsersTable($db);

            // Check rate limiting (brute force protection)
            $lockSeconds = \App\Core\Security::checkRateLimit($email);
            if ($lockSeconds !== null) {
                $minutes = (int) ceil($lockSeconds / 60);
                echo json_encode([
                    'status' => 'error',
                    'errors' => ['general' => "Terlalu banyak percobaan masuk. Silakan coba kembali dalam {$minutes} menit."]
                ]);
                exit;
            }

            $userRepo = new UserRepository();
            $user = $userRepo->findByEmail($email);

            if ($user && password_verify($password, $user['password'])) {
                session_regenerate_id(true);
                // Regenerate CSRF token on login to prevent fixation
                \App\Core\Security::generateCsrfToken(true);

                // Clear rate limit history on successful login
                \App\Core\Security::clearLoginAttempts($email);

                $_SESSION['user'] = [
                    'id' => $user['id'],
                    'name' => $user['username'],
                    'email' => $user['email']
                ];
                $redirectUrl = (strcasecmp(trim($user['email']), 'admin@routerosquiz.academy') === 0) ? BASE_URL . '/admin' : BASE_URL . '/';
                echo json_encode(['status' => 'success', 'message' => 'Login berhasil!', 'redirect' => $redirectUrl]);
                exit;
            } else {
                // Record failed login attempt for rate limiting
                \App\Core\Security::recordLoginAttempt($email);
                echo json_encode(['status' => 'error', 'errors' => ['password' => 'Password salah atau email tidak terdaftar.']]);
                exit;
            }

        } catch (PDOException | \Exception $e) {
            echo json_encode([
                'status' => 'error',
                'errors' => ['general' => 'Terjadi masalah pada koneksi database. Silakan hubungi administrator.']
            ]);
            exit;
        }
    }



    /**
     * Logout action
     */
    public function logout()
    {
        \App\Core\Security::preventBFCache();
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        unset($_SESSION['user']);
        session_destroy();
        header('Location: ' . BASE_URL . '/login');
        exit;
    }

    /**
     * Helper to guarantee that the users table exists.
     */
    private function checkAndCreateUsersTable($db)
    {
        $sql = "CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(100) NOT NULL,
            email VARCHAR(100) UNIQUE NOT NULL,
            password VARCHAR(255) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
        $db->exec($sql);
    }

}
