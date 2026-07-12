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

#[Authorize(Role::ADMIN)]
class AdminController extends Controller
{

    private function checkAdmin()
    {
        $email = isset($_SESSION['user']['email']) ? trim($_SESSION['user']['email']) : '';
        if (!isset($_SESSION['user']) || strcasecmp($email, 'admin@routerosquiz.academy') !== 0) {
            header('Location: ' . BASE_URL . '/');
            exit;
        }
    }

    public function index()
    {
        $this->checkAdmin();
        \App\Core\Security::preventBFCache();

        $db = Database::getInstance()->getConnection();

        // Fetch all quizzes
        $stmt = $db->query("SELECT * FROM quizzes ORDER BY id DESC");
        $quizzes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Fetch all users (excluding admin) for user management using Repository
        $userRepo = new UserRepository();
        $usersList = $userRepo->getAllUsers();

        $totalQuizzes = count($quizzes);
        $totalUsers = count($usersList);

        // Fetch all badges
        $stmtBadges = $db->query("SELECT * FROM badges ORDER BY id DESC");
        $badgesList = $stmtBadges->fetchAll(PDO::FETCH_ASSOC);

        $this->view('admin/index', [
            'title' => 'Admin Dashboard | NetQuiz',
            'quizzes' => $quizzes,
            'users_list' => $usersList,
            'badges_list' => $badgesList,
            'stats' => [
                'total_quizzes' => $totalQuizzes,
                'total_users' => $totalUsers
            ]
        ]);
    }

    public function createQuiz()
    {
        $this->checkAdmin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!\App\Core\Security::validateCsrfToken()) {
                $_SESSION['admin_error'] = 'Sesi tidak valid, silakan muat ulang halaman.';
                header('Location: ' . BASE_URL . '/admin');
                exit;
            }
            $title = $_POST['title'] ?? '';
            $duration = isset($_POST['duration']) ? (int) $_POST['duration'] : 0;
            $description = $_POST['description'] ?? '';
            $category = $_POST['category'] ?? 'Routing';
            $difficulty = $_POST['difficulty'] ?? 'Mudah';
            $questions = $_POST['questions'] ?? [];

            if (empty($title) || empty($description) || empty($questions)) {
                $_SESSION['admin_error'] = 'Semua field wajib diisi.';
                header('Location: ' . BASE_URL . '/admin');
                exit;
            }

            try {
                $db = Database::getInstance()->getConnection();
                $db->beginTransaction();

                // 1. Insert Quiz (Reverted to no image)
                $stmt = $db->prepare("INSERT INTO quizzes (title, description, category, duration, difficulty) VALUES (:title, :description, :category, :duration, :difficulty)");
                $stmt->execute([
                    'title' => $title,
                    'description' => $description,
                    'category' => $category,
                    'duration' => $duration,
                    'difficulty' => $difficulty
                ]);
                $quizId = $db->lastInsertId();

                // 2. Insert Questions
                $stmtQ = $db->prepare("
                    INSERT INTO questions (quiz_id, question, option_a, option_b, option_c, option_d, correct, explanation, image_path) 
                    VALUES (:quiz_id, :question, :option_a, :option_b, :option_c, :option_d, :correct, :explanation, :image_path)
                ");

                foreach ($questions as $q) {
                    $qImagePath = null;

                    if (!empty($q['image']) && strpos($q['image'], 'data:image/') === 0) {
                        $imageData = $q['image'];
                        $imageData = str_replace(' ', '+', $imageData);

                        list(, $data) = explode(';', $imageData);
                        list(, $data) = explode(',', $data);
                        $data = base64_decode($data);

                        $image = imagecreatefromstring($data);
                        if ($image) {
                            $newFilename = uniqid('qimg_') . '.webp';
                            $uploadDir = PUBLIC_ROOT . '/uploads/questions/';
                            if (!is_dir($uploadDir)) {
                                mkdir($uploadDir, 0755, true);
                            }

                            // Preserve transparency for PNG conversion to WebP
                            imagepalettetotruecolor($image);
                            imagealphablending($image, true);
                            imagesavealpha($image, true);

                            if (imagewebp($image, $uploadDir . $newFilename, 80)) {
                                $qImagePath = 'uploads/questions/' . $newFilename;
                            }
                        }
                    }

                    $stmtQ->execute([
                        'quiz_id' => $quizId,
                        'question' => $q['question'],
                        'option_a' => $q['option_a'],
                        'option_b' => $q['option_b'],
                        'option_c' => $q['option_c'],
                        'option_d' => $q['option_d'],
                        'correct' => strtoupper($q['correct']),
                        'explanation' => !empty($q['explanation']) ? $q['explanation'] : null,
                        'image_path' => $qImagePath
                    ]);
                }

                $db->commit();
                $_SESSION['admin_success'] = 'Kuis baru berhasil dibuat!';
            } catch (PDOException $e) {
                if ($db->inTransaction()) {
                    $db->rollBack();
                }
                $_SESSION['admin_error'] = 'Gagal membuat kuis: ' . $e->getMessage();
            }

            header('Location: ' . BASE_URL . '/admin');
            exit;
        }
    }

    public function deleteQuiz($id)
    {
        $this->checkAdmin();
        if (!\App\Core\Security::validateCsrfToken()) {
            $_SESSION['admin_error'] = 'Sesi tidak valid, silakan muat ulang halaman.';
            header('Location: ' . BASE_URL . '/admin');
            exit;
        }
        $id = (int) $id;

        try {
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare("DELETE FROM quizzes WHERE id = :id");
            $stmt->execute(['id' => $id]);
            $_SESSION['admin_success'] = 'Kuis berhasil dihapus!';
        } catch (PDOException $e) {
            $_SESSION['admin_error'] = 'Gagal menghapus kuis: ' . $e->getMessage();
        }

        header('Location: ' . BASE_URL . '/admin');
        exit;
    }
    public function createMember()
    {
        $this->checkAdmin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!\App\Core\Security::validateCsrfToken()) {
                $_SESSION['admin_error'] = 'Sesi tidak valid, silakan muat ulang halaman.';
                header('Location: ' . BASE_URL . '/admin');
                exit;
            }
            $username = trim($_POST['username'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';

            if (empty($username) || empty($email) || empty($password)) {
                $_SESSION['admin_error'] = 'Semua data registrasi wajib diisi.';
                header('Location: ' . BASE_URL . '/admin');
                exit;
            }

            try {
                $userRepo = new UserRepository();

                // Check if email already exists
                if ($userRepo->emailExists($email)) {
                    $_SESSION['admin_error'] = 'Email sudah terdaftar.';
                    header('Location: ' . BASE_URL . '/admin');
                    exit;
                }

                // Check if username already exists
                if ($userRepo->usernameExists($username)) {
                    $_SESSION['admin_error'] = 'Username sudah digunakan.';
                    header('Location: ' . BASE_URL . '/admin');
                    exit;
                }

                $userRepo->create($username, $email, $password);

                $_SESSION['admin_success'] = 'Anggota baru "' . htmlspecialchars($username) . '" berhasil terdaftar!';
            } catch (PDOException $e) {
                $_SESSION['admin_error'] = 'Gagal mendaftarkan anggota: ' . $e->getMessage();
            }

            header('Location: ' . BASE_URL . '/admin');
            exit;
        }
    }

    public function updateMember($id)
    {
        $this->checkAdmin();
        $id = (int) $id;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!\App\Core\Security::validateCsrfToken()) {
                $_SESSION['admin_error'] = 'Sesi tidak valid, silakan muat ulang halaman.';
                header('Location: ' . BASE_URL . '/admin');
                exit;
            }
            $username = trim($_POST['username'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';

            if (empty($username) || empty($email)) {
                $_SESSION['admin_error'] = 'Username dan Email wajib diisi.';
                header('Location: ' . BASE_URL . '/admin');
                exit;
            }

            try {
                $userRepo = new UserRepository();

                // Check if email already exists on OTHER users
                if ($userRepo->emailExists($email, $id)) {
                    $_SESSION['admin_error'] = 'Email sudah digunakan oleh akun lain.';
                    header('Location: ' . BASE_URL . '/admin');
                    exit;
                }

                $userRepo->updateProfile($id, $username, $email);
                if (!empty($password)) {
                    $userRepo->updatePassword($id, $password);
                }

                $_SESSION['admin_success'] = 'Akun anggota "' . htmlspecialchars($username) . '" berhasil diperbarui!';
            } catch (PDOException $e) {
                $_SESSION['admin_error'] = 'Gagal memperbarui akun anggota: ' . $e->getMessage();
            }

            header('Location: ' . BASE_URL . '/admin');
            exit;
        }
    }

    public function deleteMember($id)
    {
        $this->checkAdmin();
        if (!\App\Core\Security::validateCsrfToken()) {
            $_SESSION['admin_error'] = 'Sesi tidak valid, silakan muat ulang halaman.';
            header('Location: ' . BASE_URL . '/admin');
            exit;
        }
        $id = (int) $id;

        try {
            $userRepo = new UserRepository();
            $userRepo->delete($id);
            $_SESSION['admin_success'] = 'Akun anggota berhasil dihapus!';
        } catch (PDOException $e) {
            $_SESSION['admin_error'] = 'Gagal menghapus akun anggota: ' . $e->getMessage();
        }

        header('Location: ' . BASE_URL . '/admin');
        exit;
    }

    public function updateProfile()
    {
        $this->checkAdmin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // CSRF validation added for security
            if (!\App\Core\Security::validateCsrfToken()) {
                $_SESSION['admin_error'] = 'Sesi tidak valid, silakan muat ulang halaman.';
                header('Location: ' . BASE_URL . '/admin');
                exit;
            }

            $username = trim($_POST['username'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $adminId = (int) $_SESSION['user']['id'];

            if (empty($username) || empty($email)) {
                $_SESSION['admin_error'] = 'Username dan Email wajib diisi.';
                header('Location: ' . BASE_URL . '/admin');
                exit;
            }

            try {
                $userRepo = new UserRepository();

                // Check if email already exists on OTHER users
                if ($userRepo->emailExists($email, $adminId)) {
                    $_SESSION['admin_error'] = 'Email sudah digunakan oleh akun lain.';
                    header('Location: ' . BASE_URL . '/admin');
                    exit;
                }

                $userRepo->updateProfile($adminId, $username, $email);
                if (!empty($password)) {
                    $userRepo->updatePassword($adminId, $password);
                }

                // Update session info
                $_SESSION['user']['name'] = $username;
                $_SESSION['user']['email'] = $email;

                $_SESSION['admin_success'] = 'Profil admin berhasil diperbarui!';
            } catch (PDOException $e) {
                $_SESSION['admin_error'] = 'Gagal memperbarui profil: ' . $e->getMessage();
            }

            header('Location: ' . BASE_URL . '/admin');
            exit;
        }
    }

    public function createBadge()
    {
        $this->checkAdmin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!\App\Core\Security::validateCsrfToken()) {
                $_SESSION['admin_error'] = 'Sesi tidak valid, silakan muat ulang halaman.';
                header('Location: ' . BASE_URL . '/admin');
                exit;
            }
            $title = trim($_POST['title'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $icon = trim($_POST['icon'] ?? 'award');
            $metric = trim($_POST['metric'] ?? 'completed_quizzes');
            $targetValue = (int) ($_POST['target_value'] ?? 0);

            if (empty($title) || empty($description) || $targetValue <= 0) {
                $_SESSION['admin_error'] = 'Semua field lencana wajib diisi dengan benar.';
                header('Location: ' . BASE_URL . '/admin');
                exit;
            }

            try {
                $db = Database::getInstance()->getConnection();
                $stmt = $db->prepare("INSERT INTO badges (title, description, icon, metric, target_value) VALUES (:title, :description, :icon, :metric, :target_value)");
                $stmt->execute([
                    'title' => $title,
                    'description' => $description,
                    'icon' => $icon,
                    'metric' => $metric,
                    'target_value' => $targetValue
                ]);
                $_SESSION['admin_success'] = 'Lencana baru berhasil dibuat!';
            } catch (PDOException $e) {
                $_SESSION['admin_error'] = 'Gagal membuat lencana: ' . $e->getMessage();
            }

            header('Location: ' . BASE_URL . '/admin');
            exit;
        }
    }

    public function deleteBadge($id)
    {
        $this->checkAdmin();
        if (!\App\Core\Security::validateCsrfToken()) {
            $_SESSION['admin_error'] = 'Sesi tidak valid, silakan muat ulang halaman.';
            header('Location: ' . BASE_URL . '/admin');
            exit;
        }
        $id = (int) $id;

        try {
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare("DELETE FROM badges WHERE id = :id");
            $stmt->execute(['id' => $id]);
            $_SESSION['admin_success'] = 'Lencana berhasil dihapus!';
        } catch (PDOException $e) {
            $_SESSION['admin_error'] = 'Gagal menghapus lencana: ' . $e->getMessage();
        }

        header('Location: ' . BASE_URL . '/admin');
        exit;
    }

    public function deleteBadgesBulk()
    {
        $this->checkAdmin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $ids = $_POST['selected_badges'] ?? [];

            if (empty($ids)) {
                $_SESSION['admin_error'] = 'Pilih lencana yang ingin dihapus terlebih dahulu.';
                header('Location: ' . BASE_URL . '/admin');
                exit;
            }

            // Sanitize ids to integers
            $ids = array_map('intval', $ids);

            try {
                $db = Database::getInstance()->getConnection();
                // Create placeholders
                $placeholders = implode(',', array_fill(0, count($ids), '?'));
                $stmt = $db->prepare("DELETE FROM badges WHERE id IN ($placeholders)");
                $stmt->execute($ids);
                $_SESSION['admin_success'] = count($ids) . ' lencana berhasil dihapus!';
            } catch (PDOException $e) {
                $_SESSION['admin_error'] = 'Gagal menghapus lencana secara massal: ' . $e->getMessage();
            }

            header('Location: ' . BASE_URL . '/admin');
            exit;
        }
    }
}
