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
use App\Repositories\QuestionRepositoryInterface;
use App\Repositories\UserRepositoryInterface;
use App\Repositories\MaterialRepositoryInterface;
use App\Repositories\BadgeRepositoryInterface;

#[Authorize(Role::ADMIN)]
class AdminController extends Controller
{
    public function __construct(
        private QuizRepositoryInterface $quizRepo,
        private QuestionRepositoryInterface $questionRepo,
        private UserRepositoryInterface $userRepo,
        private MaterialRepositoryInterface $materialRepo,
        private BadgeRepositoryInterface $badgeRepo,
        private Request $request
    ) {}

    /**
     * Helper to guarantee admin session identity.
     */
    private function checkAdmin(): void
    {
        $email = isset($_SESSION['user']['email']) ? trim($_SESSION['user']['email']) : '';
        if (!isset($_SESSION['user']) || strcasecmp($email, 'admin@routerosquiz.academy') !== 0) {
            header('Location: ' . BASE_URL . '/');
            exit;
        }
    }

    /**
     * Admin Workspace & Management Console.
     */
    public function index(): Response
    {
        $this->checkAdmin();
        Security::preventBFCache();

        $quizzes = $this->quizRepo->getAll();
        $usersList = $this->userRepo->getAllUsers();
        $badgesList = $this->badgeRepo->getAll();
        $materialsList = $this->materialRepo->getAll();

        return $this->view('admin/index', [
            'title' => 'Admin Dashboard | NetQuiz',
            'quizzes' => $quizzes,
            'users_list' => $usersList,
            'badges_list' => $badgesList,
            'materials_list' => $materialsList,
            'stats' => [
                'total_quizzes' => count($quizzes),
                'total_users' => count($usersList),
                'total_materials' => count($materialsList)
            ]
        ]);
    }

    /**
     * Create new Quiz & Questions atomically.
     */
    public function createQuiz(): Response
    {
        $this->checkAdmin();

        if ($this->request->isMethod('POST')) {
            if (!Security::validateCsrfToken($this->request->input('csrf_token'))) {
                $_SESSION['admin_error'] = 'Sesi tidak valid, silakan muat ulang halaman.';
                return $this->redirect(BASE_URL . '/admin#quiz-section');
            }

            $title = trim((string)$this->request->input('title', ''));
            $duration = (int)$this->request->input('duration', 15);
            $description = trim((string)$this->request->input('description', ''));
            $category = (string)$this->request->input('category', 'Routing');
            $difficulty = (string)$this->request->input('difficulty', 'Mudah');
            $questions = $this->request->input('questions', []);

            if (empty($title) || empty($description) || empty($questions) || !is_array($questions)) {
                $_SESSION['admin_error'] = 'Semua data kuis dan minimal 1 butir pertanyaan wajib diisi.';
                return $this->redirect(BASE_URL . '/admin#quiz-section');
            }

            try {
                $quizData = [
                    'title' => $title,
                    'description' => $description,
                    'category' => $category,
                    'duration' => $duration,
                    'difficulty' => $difficulty
                ];

                $this->quizRepo->createWithQuestions($quizData, $questions);
                $_SESSION['admin_success'] = 'Kuis baru dan seluruh pertanyaan ujian berhasil diterbitkan!';
            } catch (\Exception $e) {
                $_SESSION['admin_error'] = 'Gagal membuat kuis: ' . $e->getMessage();
            }
        }

        return $this->redirect(BASE_URL . '/admin#quiz-section');
    }

    /**
     * Delete Quiz.
     */
    public function deleteQuiz(string|int $id): Response
    {
        $this->checkAdmin();

        if (!Security::validateCsrfToken($this->request->input('csrf_token'))) {
            $_SESSION['admin_error'] = 'Sesi tidak valid, silakan muat ulang halaman.';
            return $this->redirect(BASE_URL . '/admin#quiz-section');
        }

        try {
            $this->quizRepo->delete((int)$id);
            $_SESSION['admin_success'] = 'Kuis berhasil dihapus!';
        } catch (\Exception $e) {
            $_SESSION['admin_error'] = 'Gagal menghapus kuis: ' . $e->getMessage();
        }

        return $this->redirect(BASE_URL . '/admin#quiz-section');
    }

    /**
     * Register New Member.
     */
    public function createMember(): Response
    {
        $this->checkAdmin();

        if ($this->request->isMethod('POST')) {
            if (!Security::validateCsrfToken($this->request->input('csrf_token'))) {
                $_SESSION['admin_error'] = 'Sesi tidak valid, silakan muat ulang halaman.';
                return $this->redirect(BASE_URL . '/admin#member-section');
            }

            $username = trim((string)$this->request->input('username', ''));
            $email = trim((string)$this->request->input('email', ''));
            $password = (string)$this->request->input('password', '');
            $confirmPassword = (string)$this->request->input('confirm_password', '');

            if (empty($username) || empty($email) || empty($password)) {
                $_SESSION['admin_error'] = 'Semua data registrasi wajib diisi.';
                return $this->redirect(BASE_URL . '/admin#member-section');
            }

            if (!empty($confirmPassword) && $password !== $confirmPassword) {
                $_SESSION['admin_error'] = 'Kata sandi dan konfirmasi kata sandi tidak cocok.';
                return $this->redirect(BASE_URL . '/admin#member-section');
            }

            if (strlen($password) < 8) {
                $_SESSION['admin_error'] = 'Kata sandi minimal harus 8 karakter.';
                return $this->redirect(BASE_URL . '/admin#member-section');
            }

            if (!Security::isValidEmail($email)) {
                $_SESSION['admin_error'] = 'Format email tidak valid.';
                return $this->redirect(BASE_URL . '/admin#member-section');
            }

            try {
                if ($this->userRepo->emailExists($email)) {
                    $_SESSION['admin_error'] = 'Email sudah terdaftar.';
                    return $this->redirect(BASE_URL . '/admin#member-section');
                }

                if ($this->userRepo->usernameExists($username)) {
                    $_SESSION['admin_error'] = 'Username sudah digunakan.';
                    return $this->redirect(BASE_URL . '/admin#member-section');
                }

                $this->userRepo->create($username, $email, $password);
                $_SESSION['admin_success'] = 'Anggota baru "' . htmlspecialchars($username) . '" berhasil didaftarkan!';
                return $this->redirect(BASE_URL . '/admin#manage-section');
            } catch (\Exception $e) {
                $_SESSION['admin_error'] = 'Gagal mendaftarkan anggota: ' . $e->getMessage();
            }
        }

        return $this->redirect(BASE_URL . '/admin#member-section');
    }

    /**
     * Update Member Data.
     */
    public function updateMember(string|int $id): Response
    {
        $this->checkAdmin();
        $id = (int)$id;

        if ($this->request->isMethod('POST')) {
            if (!Security::validateCsrfToken($this->request->input('csrf_token'))) {
                $_SESSION['admin_error'] = 'Sesi tidak valid, silakan muat ulang halaman.';
                return $this->redirect(BASE_URL . '/admin#manage-section');
            }

            $username = trim((string)$this->request->input('username', ''));
            $email = trim((string)$this->request->input('email', ''));
            $password = (string)$this->request->input('password', '');

            if (empty($username) || empty($email)) {
                $_SESSION['admin_error'] = 'Username dan Email wajib diisi.';
                return $this->redirect(BASE_URL . '/admin#manage-section');
            }

            if (!Security::isValidEmail($email)) {
                $_SESSION['admin_error'] = 'Format email tidak valid.';
                return $this->redirect(BASE_URL . '/admin#manage-section');
            }

            try {
                if ($this->userRepo->emailExists($email, $id)) {
                    $_SESSION['admin_error'] = 'Email sudah digunakan oleh akun lain.';
                    return $this->redirect(BASE_URL . '/admin#manage-section');
                }

                $this->userRepo->updateProfile($id, $username, $email);
                if (!empty($password)) {
                    $this->userRepo->updatePassword($id, $password);
                }

                $_SESSION['admin_success'] = 'Akun anggota "' . htmlspecialchars($username) . '" berhasil diperbarui!';
            } catch (\Exception $e) {
                $_SESSION['admin_error'] = 'Gagal memperbarui akun anggota: ' . $e->getMessage();
            }
        }

        return $this->redirect(BASE_URL . '/admin#manage-section');
    }

    /**
     * Delete Member.
     */
    public function deleteMember(string|int $id): Response
    {
        $this->checkAdmin();
        $id = (int)$id;

        if (!Security::validateCsrfToken($this->request->input('csrf_token'))) {
            $_SESSION['admin_error'] = 'Sesi tidak valid, silakan muat ulang halaman.';
            return $this->redirect(BASE_URL . '/admin#manage-section');
        }

        try {
            $this->userRepo->delete($id);
            $_SESSION['admin_success'] = 'Akun anggota berhasil dihapus!';
        } catch (\Exception $e) {
            $_SESSION['admin_error'] = 'Gagal menghapus akun anggota: ' . $e->getMessage();
        }

        return $this->redirect(BASE_URL . '/admin#manage-section');
    }

    /**
     * Toggle Member Suspended / Active Status.
     */
    public function suspendMember(string|int $id): Response
    {
        $this->checkAdmin();
        $id = (int)$id;

        if (!Security::validateCsrfToken($this->request->input('csrf_token'))) {
            $_SESSION['admin_error'] = 'Sesi tidak valid, silakan muat ulang halaman.';
            return $this->redirect(BASE_URL . '/admin#manage-section');
        }

        $newStatus = trim((string)$this->request->input('status', 'Nonaktif'));

        try {
            $this->userRepo->updateStatus($id, $newStatus);
            $_SESSION['admin_success'] = 'Status member berhasil diperbarui menjadi ' . htmlspecialchars($newStatus) . '!';
        } catch (\Exception $e) {
            $_SESSION['admin_error'] = 'Gagal mengubah status member: ' . $e->getMessage();
        }

        return $this->redirect(BASE_URL . '/admin#manage-section');
    }

    /**
     * Create Badge.
     */
    public function createBadge(): Response
    {
        $this->checkAdmin();

        if ($this->request->isMethod('POST')) {
            if (!Security::validateCsrfToken($this->request->input('csrf_token'))) {
                $_SESSION['admin_error'] = 'Sesi tidak valid, silakan muat ulang halaman.';
                return $this->redirect(BASE_URL . '/admin#badge-section');
            }

            $title = trim((string)$this->request->input('title', ''));
            $description = trim((string)$this->request->input('description', ''));
            $icon = trim((string)$this->request->input('icon', 'award'));
            $metric = trim((string)$this->request->input('metric', 'completed_quizzes'));
            $targetValue = (int)$this->request->input('target_value', 1);

            if (empty($title) || empty($description) || $targetValue <= 0) {
                $_SESSION['admin_error'] = 'Semua field lencana wajib diisi dengan benar.';
                return $this->redirect(BASE_URL . '/admin#badge-section');
            }

            try {
                $this->badgeRepo->create($title, $description, $icon, $metric, $targetValue);
                $_SESSION['admin_success'] = 'Lencana baru berhasil diterbitkan!';
            } catch (\Exception $e) {
                $_SESSION['admin_error'] = 'Gagal membuat lencana: ' . $e->getMessage();
            }
        }

        return $this->redirect(BASE_URL . '/admin#badge-section');
    }

    /**
     * Delete Badge.
     */
    public function deleteBadge(string|int $id): Response
    {
        $this->checkAdmin();
        $id = (int)$id;

        if (!Security::validateCsrfToken($this->request->input('csrf_token'))) {
            $_SESSION['admin_error'] = 'Sesi tidak valid, silakan muat ulang halaman.';
            return $this->redirect(BASE_URL . '/admin#badge-section');
        }

        try {
            $this->badgeRepo->delete($id);
            $_SESSION['admin_success'] = 'Lencana berhasil dihapus!';
        } catch (\Exception $e) {
            $_SESSION['admin_error'] = 'Gagal menghapus lencana: ' . $e->getMessage();
        }

        return $this->redirect(BASE_URL . '/admin#badge-section');
    }

    /**
     * Delete Badges Bulk.
     */
    public function deleteBadgesBulk(): Response
    {
        $this->checkAdmin();

        $ids = $this->request->input('selected_badges', []);
        if (empty($ids) || !is_array($ids)) {
            $_SESSION['admin_error'] = 'Pilih lencana yang ingin dihapus terlebih dahulu.';
            return $this->redirect(BASE_URL . '/admin#badge-section');
        }

        try {
            $this->badgeRepo->deleteBulk($ids);
            $_SESSION['admin_success'] = count($ids) . ' lencana berhasil dihapus!';
        } catch (\Exception $e) {
            $_SESSION['admin_error'] = 'Gagal menghapus lencana massal: ' . $e->getMessage();
        }

        return $this->redirect(BASE_URL . '/admin#badge-section');
    }

    /**
     * Create Material Article.
     */
    public function createMaterial(): Response
    {
        $this->checkAdmin();

        if ($this->request->isMethod('POST')) {
            if (!Security::validateCsrfToken($this->request->input('csrf_token'))) {
                $_SESSION['admin_error'] = 'Sesi tidak valid, silakan muat ulang halaman.';
                return $this->redirect(BASE_URL . '/admin#materials-section');
            }

            $title = trim((string)$this->request->input('title', ''));
            $content = trim((string)$this->request->input('content', ''));
            $category = trim((string)$this->request->input('category', 'Routing'));
            $difficulty = trim((string)$this->request->input('difficulty', 'Mudah'));

            if (empty($title) || empty($content)) {
                $_SESSION['admin_error'] = 'Judul dan Konten materi wajib diisi.';
                return $this->redirect(BASE_URL . '/admin#materials-section');
            }

            try {
                $this->materialRepo->create($title, $content, $category, $difficulty);
                $_SESSION['admin_success'] = 'Materi artikel baru berhasil dipublikasikan!';
            } catch (\Exception $e) {
                $_SESSION['admin_error'] = 'Gagal membuat materi: ' . $e->getMessage();
            }
        }

        return $this->redirect(BASE_URL . '/admin#materials-section');
    }

    /**
     * Update Material Article.
     */
    public function updateMaterial(string|int $id): Response
    {
        $this->checkAdmin();
        $id = (int)$id;

        if ($this->request->isMethod('POST')) {
            if (!Security::validateCsrfToken($this->request->input('csrf_token'))) {
                $_SESSION['admin_error'] = 'Sesi tidak valid, silakan muat ulang halaman.';
                return $this->redirect(BASE_URL . '/admin#materials-section');
            }

            $title = trim((string)$this->request->input('title', ''));
            $content = trim((string)$this->request->input('content', ''));
            $category = trim((string)$this->request->input('category', 'Routing'));
            $difficulty = trim((string)$this->request->input('difficulty', 'Mudah'));

            if (empty($title) || empty($content)) {
                $_SESSION['admin_error'] = 'Judul dan Konten materi wajib diisi.';
                return $this->redirect(BASE_URL . '/admin#materials-section');
            }

            try {
                $this->materialRepo->update($id, $title, $content, $category, $difficulty);
                $_SESSION['admin_success'] = 'Materi artikel berhasil diperbarui!';
            } catch (\Exception $e) {
                $_SESSION['admin_error'] = 'Gagal memperbarui materi: ' . $e->getMessage();
            }
        }

        return $this->redirect(BASE_URL . '/admin#materials-section');
    }

    /**
     * Delete Material Article.
     */
    public function deleteMaterial(string|int $id): Response
    {
        $this->checkAdmin();
        $id = (int)$id;

        if (!Security::validateCsrfToken($this->request->input('csrf_token'))) {
            $_SESSION['admin_error'] = 'Sesi tidak valid, silakan muat ulang halaman.';
            return $this->redirect(BASE_URL . '/admin#materials-section');
        }

        try {
            $this->materialRepo->delete($id);
            $_SESSION['admin_success'] = 'Materi artikel berhasil dihapus!';
        } catch (\Exception $e) {
            $_SESSION['admin_error'] = 'Gagal menghapus materi: ' . $e->getMessage();
        }

        return $this->redirect(BASE_URL . '/admin#materials-section');
    }

    /**
     * Fetch Material JSON for Visual Editor.
     */
    public function getMaterialJson(string|int $id): Response
    {
        $this->checkAdmin();
        $id = (int)$id;

        $material = $this->materialRepo->getById($id);
        if ($material) {
            return $this->jsonResponse($material);
        }

        return $this->jsonResponse(['error' => 'Materi tidak ditemukan.'], 404);
    }
}
