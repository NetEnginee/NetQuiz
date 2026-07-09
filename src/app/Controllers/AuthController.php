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
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $mode = (strpos($uri, 'signup') !== false) ? 'signup' : 'login';

        if ($mode === 'signup') {
            header('Location: ' . BASE_URL . '/login');
            exit;
        }

        if (isset($_SESSION['user'])) {
            $redirectUrl = ($_SESSION['user']['email'] === 'admin@routeros.academy') ? BASE_URL . '/admin' : BASE_URL . '/';
            header('Location: ' . $redirectUrl);
            exit;
        }

        $this->view('auth/index', [
            'title' => 'Masuk ke RouterOS Quiz',
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

            $userRepo = new UserRepository();
            $user = $userRepo->findByEmail($email);

            if ($user && password_verify($password, $user['password'])) {
                session_regenerate_id(true);
                // Regenerate CSRF token on login to prevent fixation
                \App\Core\Security::generateCsrfToken(true);

                $_SESSION['user'] = [
                    'id' => $user['id'],
                    'name' => $user['username'],
                    'email' => $user['email']
                ];
                $redirectUrl = ($user['email'] === 'admin@routeros.academy') ? BASE_URL . '/admin' : BASE_URL . '/';
                echo json_encode(['status' => 'success', 'message' => 'Login berhasil!', 'redirect' => $redirectUrl]);
                exit;
            } else {
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
     * API Endpoint: Handle Sign Up Submission (AJAX POST)
     */
    public function apiSignup()
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['status' => 'error', 'message' => 'Method Not Allowed']);
            exit;
        }

        $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;

        $name = trim($input['name'] ?? '');
        $email = trim($input['email'] ?? '');
        $password = $input['password'] ?? '';
        $confirmPassword = $input['confirm_password'] ?? '';

        // Validation array
        $errors = [];

        if (empty($name)) {
            $errors['name'] = 'Nama lengkap wajib diisi.';
        }

        if (empty($email)) {
            $errors['email'] = 'Email wajib diisi.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Format email tidak valid.';
        }

        if (empty($password)) {
            $errors['password'] = 'Password wajib diisi.';
        } elseif (strlen($password) < 8) {
            $errors['password'] = 'Password minimal harus 8 karakter.';
        }

        if ($password !== $confirmPassword) {
            $errors['confirm_password'] = 'Konfirmasi password tidak cocok.';
        }

        if (!empty($errors)) {
            echo json_encode(['status' => 'error', 'errors' => $errors]);
            exit;
        }

        try {
            $dbInstance = Database::getInstance();
            $db = $dbInstance->getConnection();

            $this->checkAndCreateUsersTable($db);

            $userRepo = new UserRepository();

            // Check if email already exists
            if ($userRepo->emailExists($email)) {
                echo json_encode(['status' => 'error', 'errors' => ['email' => 'Email ini sudah terdaftar.']]);
                exit;
            }

            // Check if name (username) already exists
            if ($userRepo->usernameExists($name)) {
                echo json_encode(['status' => 'error', 'errors' => ['name' => 'Nama ini sudah terdaftar. Silakan gunakan nama lain.']]);
                exit;
            }

            // Insert new user
            $userId = $userRepo->create($name, $email, $password);

            // Set session
            session_regenerate_id(true);
            \App\Core\Security::generateCsrfToken(true);

            $_SESSION['user'] = [
                'id' => $userId,
                'name' => $name,
                'email' => $email
            ];

            echo json_encode(['status' => 'success', 'message' => 'Registrasi berhasil!', 'redirect' => BASE_URL . '/']);
            exit;

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
