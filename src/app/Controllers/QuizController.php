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
use App\Repositories\AttemptRepositoryInterface;
use App\Repositories\BadgeRepositoryInterface;

#[Authorize(Role::USER, Role::ADMIN)]
class QuizController extends Controller
{
    public function __construct(
        private QuizRepositoryInterface $quizRepo,
        private AttemptRepositoryInterface $attemptRepo,
        private BadgeRepositoryInterface $badgeRepo,
        private Request $request
    ) {}

    /**
     * Display list of quizzes with completion status.
     */
    public function index(): Response
    {
        $userId = (int)($_SESSION['user']['id'] ?? 0);
        $activeDifficulty = (string)$this->request->query('difficulty', 'all');

        $categorized = $this->quizRepo->getCategorizedQuizzesWithUserStatus($userId, $activeDifficulty);

        return $this->view('quiz/index', [
            'title' => 'Daftar Quiz | NetQuiz',
            'categorized' => $categorized,
            'activeDifficulty' => $activeDifficulty
        ]);
    }

    /**
     * Start / Play Quiz.
     */
    public function play(string|int $id): Response
    {
        $id = (int)$id;
        $quiz = $this->quizRepo->getWithQuestions($id);
        if (!$quiz) {
            return $this->redirect(BASE_URL . '/quiz');
        }

        $userId = (int)($_SESSION['user']['id'] ?? 0);

        // Check for active paused state
        $pausedState = null;
        $pausedAttempt = $this->attemptRepo->getPausedAttempt($userId, $id);
        if ($pausedAttempt) {
            $decoded = json_decode($pausedAttempt['user_answers'] ?? '{}', true);
            $pausedState = [
                'answers' => $decoded['answers'] ?? [],
                'time_left' => (int)($decoded['time_left'] ?? ($quiz['duration'] * 60))
            ];
        }

        return $this->view('quiz/play', [
            'title' => 'Mulai Kuis - ' . $quiz['title'] . ' | NetQuiz',
            'quiz' => $quiz,
            'pausedState' => $pausedState
        ]);
    }

    /**
     * Pause Quiz (Saves state in DB).
     */
    public function pause(string|int $id): Response
    {
        $id = (int)$id;
        $quiz = $this->quizRepo->getById($id);
        if (!$quiz) {
            return $this->redirect(BASE_URL . '/quiz');
        }

        $userId = (int)($_SESSION['user']['id'] ?? 0);
        $answers = (array)$this->request->post('answers', []);
        $timeLeft = (int)$this->request->post('time_left', ($quiz['duration'] * 60));

        $this->attemptRepo->savePausedAttempt($userId, $id, $quiz['category'], $answers, $timeLeft);

        $redirect = (string)$this->request->input('redirect', BASE_URL . '/quiz');
        if (!empty($redirect) && str_starts_with($redirect, BASE_URL)) {
            return $this->redirect($redirect);
        }

        return $this->redirect(BASE_URL . '/quiz');
    }

    /**
     * Submit Quiz and Calculate Score Atomically.
     */
    public function submit(string|int $id): Response
    {
        if (!Security::validateCsrfToken($this->request->input('csrf_token'))) {
            return $this->jsonResponse(['success' => false, 'message' => 'Sesi tidak valid.'], 403);
        }

        $id = (int)$id;
        $quiz = $this->quizRepo->getWithQuestions($id);
        if (!$quiz) {
            return $this->redirect(BASE_URL . '/quiz');
        }

        $userId = (int)($_SESSION['user']['id'] ?? 0);
        $answers = (array)$this->request->post('answers', []);

        $questions = $quiz['questions'] ?? [];
        $totalQuestions = count($questions);
        $correctCount = 0;

        foreach ($questions as $index => $q) {
            $userAns = (string)($answers[$index] ?? '');
            if ($userAns !== '' && strtoupper($userAns) === strtoupper($q['correct'])) {
                $correctCount++;
            }
        }

        $score = $totalQuestions > 0 ? (int)round(($correctCount / $totalQuestions) * 100) : 0;

        // Record attempt atomically in database
        $attemptId = $this->attemptRepo->recordFinishedAttempt($userId, $id, $quiz['category'], $score, $answers);

        return $this->redirect(BASE_URL . "/quiz/result/{$attemptId}");
    }

    /**
     * View Quiz Submission Result.
     */
    public function result(string|int $id): Response
    {
        $attemptId = (int)$id;
        $userId = (int)($_SESSION['user']['id'] ?? 0);
        $isAdmin = Security::getCurrentRole() === Role::ADMIN;

        $attempt = $this->attemptRepo->getAttemptById($attemptId);
        if (!$attempt || (!$isAdmin && (int)$attempt['user_id'] !== $userId)) {
            return $this->redirect(BASE_URL . '/quiz');
        }

        $quizId = (int)$attempt['quiz_id'];
        $quiz = $this->quizRepo->getWithQuestions($quizId);
        if (!$quiz) {
            return $this->redirect(BASE_URL . '/quiz');
        }

        $userAnswers = json_decode($attempt['user_answers'] ?? '[]', true) ?: [];
        $questions = $quiz['questions'] ?? [];
        $total = count($questions);
        $correct = 0;

        foreach ($questions as $index => $q) {
            $uAns = (string)($userAnswers[$index] ?? '');
            if ($uAns !== '' && strtoupper($uAns) === strtoupper($q['correct'])) {
                $correct++;
            }
        }

        $score = (int)$attempt['score'];

        return $this->view('quiz/result', [
            'title' => 'Hasil Kuis | NetQuiz',
            'score' => $score,
            'correct' => $correct,
            'total' => $total,
            'quiz' => $quiz,
            'attempt' => $attempt
        ]);
    }

    /**
     * Review Quiz Answers with Explanations.
     */
    public function review(string|int $id): Response
    {
        $quizId = (int)$id;
        $quiz = $this->quizRepo->getWithQuestions($quizId);
        if (!$quiz) {
            return $this->redirect(BASE_URL . '/quiz');
        }

        $userId = (int)($_SESSION['user']['id'] ?? 0);
        $isAdmin = Security::getCurrentRole() === Role::ADMIN;

        $attempt = $this->attemptRepo->getFinishedAttempt($userId, $quizId);
        if (!$attempt && !$isAdmin) {
            $_SESSION['quiz_error'] = 'Anda belum pernah menyelesaikan kuis ini.';
            return $this->redirect(BASE_URL . '/quiz');
        }

        $userAnswers = $attempt ? (json_decode($attempt['user_answers'] ?? '[]', true) ?: []) : [];
        $score = $attempt ? (int)$attempt['score'] : 0;

        return $this->view('quiz/review', [
            'title' => 'Review Jawaban - ' . $quiz['title'] . ' | NetQuiz',
            'quiz' => $quiz,
            'userAnswers' => $userAnswers,
            'score' => $score,
            'attempt' => $attempt
        ]);
    }
}
