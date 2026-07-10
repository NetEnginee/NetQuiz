<?php
declare(strict_types=1);
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Authorize;
use App\Core\Role;
use PDO;
use PDOException;

#[Authorize(Role::USER)]
class QuizController extends Controller
{

    private function loadQuizzesFromDb()
    {
        try {
            $db = Database::getInstance()->getConnection();

            $stmt = $db->query("SELECT * FROM quizzes ORDER BY id ASC");
            $quizzesRaw = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $quizzes = [];
            foreach ($quizzesRaw as $q) {
                $quizId = (int) $q['id'];

                $qStmt = $db->prepare("SELECT * FROM questions WHERE quiz_id = :quiz_id ORDER BY id ASC");
                $qStmt->execute(['quiz_id' => $quizId]);
                $questionsRaw = $qStmt->fetchAll(PDO::FETCH_ASSOC);

                $questions = [];
                foreach ($questionsRaw as $quest) {
                    $questions[] = [
                        'question' => $quest['question'],
                        'image_path' => $quest['image_path'] ?? null,
                        'options' => [
                            'A' => $quest['option_a'],
                            'B' => $quest['option_b'],
                            'C' => $quest['option_c'],
                            'D' => $quest['option_d']
                        ],
                        'correct' => $quest['correct']
                    ];
                }

                $quizzes[$quizId] = [
                    'id' => $quizId,
                    'title' => $q['title'],
                    'description' => $q['description'],
                    'category' => $q['category'],
                    'duration' => isset($q['duration']) ? (int) $q['duration'] : 0,
                    'created_at' => $q['created_at'] ?? null,
                    'questions' => $questions
                ];
            }
            return $quizzes;
        } catch (PDOException $e) {
            return [];
        }
    }

    private function getDecryptedId($id)
    {
        if (empty($id)) {
            return null;
        }
        if (method_exists(\App\Core\Security::class, 'decryptUrlId')) {
            $decrypted = \App\Core\Security::decryptUrlId((string)$id);
            if ($decrypted !== null) {
                return (int)$decrypted;
            }
        }
        if (is_numeric($id)) {
            return (int)$id;
        }
        return null;
    }

    public function index()
    {
        $quizzes = $this->loadQuizzesFromDb();
        $completedQuizzes = [];
        try {
            $db = Database::getInstance()->getConnection();
            try {
                $stmt = $db->prepare("SELECT quiz_id, score FROM quiz_attempts WHERE user_id = :user_id AND quiz_id IS NOT NULL");
                $stmt->execute(['user_id' => $_SESSION['user']['id']]);
                $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
                foreach ($rows as $row) {
                    $completedQuizzes[(int)$row['quiz_id']] = (int)($row['score'] ?? 0);
                }
            } catch (PDOException $e) {
                // Fallback if the 'score' column does not exist
                $stmt = $db->prepare("SELECT quiz_id FROM quiz_attempts WHERE user_id = :user_id AND quiz_id IS NOT NULL");
                $stmt->execute(['user_id' => $_SESSION['user']['id']]);
                $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
                foreach ($rows as $row) {
                    $completedQuizzes[(int)$row['quiz_id']] = 0;
                }
            }
        } catch (PDOException $e) {}

        // Initialize default categories so they are always listed
        $categorized = [
            'Routing' => [],
            'Firewall & NAT' => [],
            'Wireless' => [],
            'Network Management' => []
        ];
        foreach ($quizzes as $quiz) {
            $quizId = (int)$quiz['id'];
            $quiz['is_completed'] = array_key_exists($quizId, $completedQuizzes);
            $quiz['score'] = $completedQuizzes[$quizId] ?? 0;
            if (isset($categorized[$quiz['category']])) {
                $categorized[$quiz['category']][] = $quiz;
            }
        }

        $this->view('quiz/index', [
            'title' => 'Daftar Quiz | RouterOS Quiz',
            'categorized' => $categorized
        ]);
    }

    public function play($id)
    {
        $id = $this->getDecryptedId($id);
        if ($id === null) {
            header('Location: ' . BASE_URL . '/quiz');
            exit;
        }
        $id = (int) $id;
        $quizzes = $this->loadQuizzesFromDb();
        if (!isset($quizzes[$id])) {
            header('Location: ' . BASE_URL . '/quiz');
            exit;
        }

        try {
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare("SELECT id FROM quiz_attempts WHERE user_id = :user_id AND quiz_id = :quiz_id");
            $stmt->execute(['user_id' => $_SESSION['user']['id'], 'quiz_id' => $id]);
            if ($stmt->fetch()) {
                $_SESSION['quiz_error'] = 'Anda sudah pernah mengerjakan kuis ini.';
                header('Location: ' . BASE_URL . '/quiz');
                exit;
            }
        } catch (PDOException $e) {}

        $quiz = $quizzes[$id];
        $pausedState = $_SESSION['paused_quiz'][$id] ?? null;

        $this->view('quiz/play', [
            'title' => 'Mulai Kuis - ' . $quiz['title'] . ' | RouterOS Quiz',
            'quiz' => $quiz,
            'pausedState' => $pausedState
        ]);
    }

    public function pause($id)
    {
        if (!\App\Core\Security::validateCsrfToken()) {
            $_SESSION['quiz_error'] = 'Sesi tidak valid.';
            header('Location: ' . BASE_URL . '/quiz');
            exit;
        }

        $id = $this->getDecryptedId($id);
        if ($id === null) {
            header('Location: ' . BASE_URL . '/quiz');
            exit;
        }
        $id = (int) $id;
        $_SESSION['paused_quiz'][$id] = [
            'answers' => $_POST['answers'] ?? [],
            'time_left' => (int)($_POST['time_left'] ?? 0)
        ];

        header('Location: ' . BASE_URL . '/quiz');
        exit;
    }

    public function submit($id)
    {
        if (!\App\Core\Security::validateCsrfToken()) {
            $this->jsonResponse(['success' => false, 'message' => 'Sesi tidak valid.'], 403);
        }

        $id = $this->getDecryptedId($id);
        if ($id === null) {
            header('Location: ' . BASE_URL . '/quiz');
            exit;
        }
        $id = (int) $id;
        $quizzes = $this->loadQuizzesFromDb();
        if (!isset($quizzes[$id])) {
            header('Location: ' . BASE_URL . '/quiz');
            exit;
        }

        $quiz = $quizzes[$id];
        $answers = $_POST['answers'] ?? [];

        // Clear paused state since it's submitted
        unset($_SESSION['paused_quiz'][$id]);

        $totalQuestions = count($quiz['questions']);
        $correctCount = 0;

        foreach ($quiz['questions'] as $index => $q) {
            $userAns = $answers[$index] ?? '';
            if (strtoupper($userAns) === strtoupper($q['correct'])) {
                $correctCount++;
            }
        }

        $score = $totalQuestions > 0 ? (int) round(($correctCount / $totalQuestions) * 100) : 0;

        $attemptId = 0;
        try {
            $db = Database::getInstance()->getConnection();
            $userId = $_SESSION['user']['id'];
            $category = $quiz['category'];

            $stmt = $db->prepare("
                INSERT INTO quiz_attempts (user_id, quiz_id, category, score, status, user_answers) 
                VALUES (:user_id, :quiz_id, :category, :score, 'finished', :user_answers)
            ");
            $stmt->execute([
                'user_id' => $userId,
                'quiz_id' => $id,
                'category' => $category,
                'score' => $score,
                'user_answers' => json_encode($answers)
            ]);
            $attemptId = $db->lastInsertId();
        } catch (PDOException $e) {
            // Log error
        }

        $encryptedAttemptId = method_exists(\App\Core\Security::class, 'encryptUrlId') ? \App\Core\Security::encryptUrlId($attemptId ?: $id) : ($attemptId ?: $id);
        $encryptedQuizId = method_exists(\App\Core\Security::class, 'encryptUrlId') ? \App\Core\Security::encryptUrlId($id) : $id;

        header('Location: ' . BASE_URL . '/quiz/result/' . $encryptedAttemptId . '?score=' . $score . '&correct=' . $correctCount . '&total=' . $totalQuestions . '&quiz_id=' . $encryptedQuizId);
        exit;
    }

    public function review($id)
    {
        $quizId = $this->getDecryptedId($id);
        if ($quizId === null) {
            header('Location: ' . BASE_URL . '/quiz');
            exit;
        }
        $quizId = (int) $quizId;
        $quizzes = $this->loadQuizzesFromDb();
        if (!isset($quizzes[$quizId])) {
            header('Location: ' . BASE_URL . '/quiz');
            exit;
        }

        $quiz = $quizzes[$quizId];

        $attempt = null;
        try {
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare("SELECT * FROM quiz_attempts WHERE user_id = :user_id AND quiz_id = :quiz_id");
            $stmt->execute(['user_id' => $_SESSION['user']['id'], 'quiz_id' => $quizId]);
            $attempt = $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {}

        if (!$attempt) {
            $_SESSION['quiz_error'] = 'Anda belum mengerjakan kuis ini.';
            header('Location: ' . BASE_URL . '/quiz');
            exit;
        }

        $userAnswers = json_decode($attempt['user_answers'] ?? '{}', true) ?: [];

        $this->view('quiz/review', [
            'title' => 'Review Jawaban - ' . $quiz['title'] . ' | RouterOS Quiz',
            'quiz' => $quiz,
            'userAnswers' => $userAnswers,
            'score' => $attempt['score']
        ]);
    }

    public function result($id)
    {
        $idDecrypted = $this->getDecryptedId($id);
        if ($idDecrypted === null) {
            header('Location: ' . BASE_URL . '/quiz');
            exit;
        }
        $id = (int) $idDecrypted;
        $score = $_GET['score'] ?? 0;
        $correct = $_GET['correct'] ?? 0;
        $total = $_GET['total'] ?? 0;
        
        $quizIdRaw = $_GET['quiz_id'] ?? '';
        $quizIdDecrypted = $this->getDecryptedId($quizIdRaw);
        $quizId = $quizIdDecrypted !== null ? (int) $quizIdDecrypted : 1;

        // Fetch the attempt and verify ownership
        try {
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare("SELECT * FROM quiz_attempts WHERE id = :id LIMIT 1");
            $stmt->execute(['id' => $id]);
            $attempt = $stmt->fetch();
        } catch (PDOException $e) {
            $attempt = null;
        }

        if (!$attempt || (int)$attempt['user_id'] !== (int)$_SESSION['user']['id']) {
            header('Location: ' . BASE_URL . '/quiz');
            exit;
        }

        $quizzes = $this->loadQuizzesFromDb();
        $quiz = $quizzes[(int) $quizId] ?? null;

        $this->view('quiz/result', [
            'title' => 'Hasil Kuis | RouterOS Quiz',
            'score' => $score,
            'correct' => $correct,
            'total' => $total,
            'quiz' => $quiz
        ]);
    }
}
