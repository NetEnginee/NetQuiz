<?php
declare(strict_types=1);
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Authorize;
use App\Core\Role;
use App\Repositories\UserRepository;
use PDO;
use PDOException;

#[Authorize(Role::USER)]
class SettingsController extends Controller
{

    /**
     * Helper to verify if user is authenticated
     */
    private function requireAuth()
    {
        if (!isset($_SESSION['user'])) {
            header('Location: ' . BASE_URL . '/login');
            exit;
        }
    }

    public function index()
    {
        $this->requireAuth();

        try {
            $userRepo = new UserRepository();
            $user = $userRepo->findById((int)$_SESSION['user']['id']);

            if (!$user) {
                // If user doesn't exist in DB, clear session and redirect
                unset($_SESSION['user']);
                header('Location: ' . BASE_URL . '/login');
                exit;
            }

            $this->view('settings/index', [
                'title' => 'Pengaturan Profil | NetQuiz',
                'user' => $user
            ]);

        } catch (PDOException | \Exception $e) {
            die("Terjadi kesalahan sistem. Silakan coba lagi.");
        }
    }

    /**
     * API: Update Profile (Username, Email)
     */
    public function updateProfile()
    {
        header('Content-Type: application/json');

        if (!isset($_SESSION['user'])) {
            http_response_code(401);
            echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
            exit;
        }

        if (!\App\Core\Security::validateCsrfToken()) {
            $_SESSION['settings_error'] = 'Sesi tidak valid, silakan muat ulang halaman.';
            header('Location: ' . BASE_URL . '/settings');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['status' => 'error', 'message' => 'Method Not Allowed']);
            exit;
        }

        $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        $username = trim($input['username'] ?? '');
        $email = trim($input['email'] ?? '');
        $userId = (int)$_SESSION['user']['id'];

        $errors = [];

        if (empty($username)) {
            $errors['username'] = 'Nama lengkap tidak boleh kosong.';
        }

        if (empty($email)) {
            $errors['email'] = 'Email tidak boleh kosong.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Format email tidak valid.';
        }

        if (!empty($errors)) {
            echo json_encode(['status' => 'error', 'errors' => $errors]);
            exit;
        }

        try {
            $userRepo = new UserRepository();

            // Check if email already used by ANOTHER user
            if ($userRepo->emailExists($email, $userId)) {
                echo json_encode(['status' => 'error', 'errors' => ['email' => 'Email ini sudah digunakan oleh pengguna lain.']]);
                exit;
            }

            // Check if username already used by ANOTHER user
            if ($userRepo->usernameExists($username, $userId)) {
                echo json_encode(['status' => 'error', 'errors' => ['username' => 'Nama lengkap ini sudah digunakan oleh pengguna lain.']]);
                exit;
            }

            // Update user in DB using Repository
            $userRepo->updateProfile($userId, $username, $email);

            // Update Session Data
            $_SESSION['user']['name'] = $username;
            $_SESSION['user']['email'] = $email;

            echo json_encode([
                'status' => 'success',
                'message' => 'Profil Anda berhasil diperbarui!',
                'user' => [
                    'username' => $username,
                    'email' => $email
                ]
            ]);
            exit;

        } catch (PDOException | \Exception $e) {
            echo json_encode([
                'status' => 'error',
                'errors' => ['general' => 'Gagal memperbarui profil karena kesalahan database.']
            ]);
            exit;
        }
    }

    /**
     * API: Update Password
     */
    public function updatePassword()
    {
        header('Content-Type: application/json');

        if (!isset($_SESSION['user'])) {
            http_response_code(401);
            echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
            exit;
        }

        if (!\App\Core\Security::validateCsrfToken()) {
            $_SESSION['settings_error'] = 'Sesi tidak valid, silakan muat ulang halaman.';
            header('Location: ' . BASE_URL . '/settings');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['status' => 'error', 'message' => 'Method Not Allowed']);
            exit;
        }

        $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        $currentPassword = $input['current_password'] ?? '';
        $newPassword = $input['new_password'] ?? '';
        $confirmPassword = $input['confirm_password'] ?? '';
        $userId = (int)$_SESSION['user']['id'];

        $errors = [];

        if (empty($currentPassword)) {
            $errors['current_password'] = 'Password saat ini wajib diisi.';
        }

        if (empty($newPassword)) {
            $errors['new_password'] = 'Password baru wajib diisi.';
        } elseif (strlen($newPassword) < 8) {
            $errors['new_password'] = 'Password baru minimal 8 karakter.';
        }

        if ($newPassword !== $confirmPassword) {
            $errors['confirm_password'] = 'Konfirmasi password baru tidak cocok.';
        }

        if (!empty($errors)) {
            echo json_encode(['status' => 'error', 'errors' => $errors]);
            exit;
        }

        try {
            $db = Database::getInstance()->getConnection();
            $userRepo = new UserRepository();

            // Fetch hashed password from DB
            $stmt = $db->prepare("SELECT password FROM users WHERE id = :id LIMIT 1");
            $stmt->execute(['id' => $userId]);
            $user = $stmt->fetch();

            if (!$user || !password_verify($currentPassword, $user['password'])) {
                echo json_encode(['status' => 'error', 'errors' => ['current_password' => 'Password saat ini salah.']]);
                exit;
            }

            // Update hashed password in DB using Repository (never plaintext)
            $userRepo->updatePassword($userId, $newPassword);

            echo json_encode([
                'status' => 'success',
                'message' => 'Password Anda berhasil diperbarui!'
            ]);
            exit;

        } catch (PDOException | \Exception $e) {
            echo json_encode([
                'status' => 'error',
                'errors' => ['general' => 'Gagal memperbarui password karena kesalahan database.']
            ]);
            exit;
        }
    }
}
