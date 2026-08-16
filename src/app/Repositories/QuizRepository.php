<?php
declare(strict_types=1);

namespace App\Repositories;

use PDO;
use PDOException;
use App\Core\Database;
use App\Core\ImageHelper;
use RuntimeException;

/**
 * QuizRepository - Secure Quiz Management Implementation with Transactions.
 */
class QuizRepository implements QuizRepositoryInterface
{
    private PDO $db;
    private Database $database;

    public function __construct(?Database $database = null)
    {
        $this->database = $database ?? Database::getInstance();
        $this->db = $this->database->getConnection();
    }

    /**
     * Get all quizzes ordered by newest.
     */
    public function getAll(): array
    {
        $stmt = $this->db->query("SELECT * FROM quizzes ORDER BY id DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get quiz by ID.
     */
    public function getById(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM quizzes WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        $quiz = $stmt->fetch(PDO::FETCH_ASSOC);
        return $quiz ?: null;
    }

    /**
     * Get quiz by ID along with its questions and options.
     */
    public function getWithQuestions(int $id): ?array
    {
        $quiz = $this->getById($id);
        if (!$quiz) {
            return null;
        }

        $stmtQ = $this->db->prepare("SELECT * FROM questions WHERE quiz_id = :quiz_id ORDER BY id ASC");
        $stmtQ->execute(['quiz_id' => $id]);
        $questions = $stmtQ->fetchAll(PDO::FETCH_ASSOC);

        $formattedQuestions = [];
        foreach ($questions as $q) {
            $formattedQuestions[] = [
                'id' => (int)$q['id'],
                'question' => $q['question'],
                'options' => [
                    'A' => $q['option_a'],
                    'B' => $q['option_b'],
                    'C' => $q['option_c'],
                    'D' => $q['option_d']
                ],
                'correct' => strtoupper($q['correct']),
                'explanation' => $q['explanation'] ?? '',
                'image_path' => $q['image_path'] ?? null
            ];
        }

        $quiz['questions'] = $formattedQuestions;
        return $quiz;
    }

    /**
     * Get quizzes by category.
     */
    public function getByCategory(string $category): array
    {
        $stmt = $this->db->prepare("SELECT * FROM quizzes WHERE category = :category ORDER BY id DESC");
        $stmt->execute(['category' => $category]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Create single quiz metadata.
     */
    public function create(
        string $title,
        string $description,
        string $category,
        int $duration,
        string $difficulty,
        ?string $imagePath = null
    ): int {
        $stmt = $this->db->prepare("
            INSERT INTO quizzes (title, description, category, duration, difficulty, image_path) 
            VALUES (:title, :description, :category, :duration, :difficulty, :image_path)
        ");
        $stmt->execute([
            'title' => $title,
            'description' => $description,
            'category' => $category,
            'duration' => $duration,
            'difficulty' => $difficulty,
            'image_path' => $imagePath
        ]);

        return (int)$this->db->lastInsertId();
    }

    /**
     * Atomically create a quiz and all its question blocks within a single transaction.
     */
    public function createWithQuestions(array $quizData, array $questionsData): int
    {
        return $this->database->transaction(function (PDO $db) use ($quizData, $questionsData) {
            // 1. Insert Quiz
            $stmt = $db->prepare("
                INSERT INTO quizzes (title, description, category, duration, difficulty, image_path) 
                VALUES (:title, :description, :category, :duration, :difficulty, :image_path)
            ");
            $stmt->execute([
                'title' => $quizData['title'],
                'description' => $quizData['description'],
                'category' => $quizData['category'] ?? 'Routing',
                'duration' => (int)($quizData['duration'] ?? 15),
                'difficulty' => $quizData['difficulty'] ?? 'Mudah',
                'image_path' => $quizData['image_path'] ?? null
            ]);
            $quizId = (int)$db->lastInsertId();

            // 2. Insert Questions
            $stmtQ = $db->prepare("
                INSERT INTO questions (quiz_id, question, option_a, option_b, option_c, option_d, correct, explanation, image_path) 
                VALUES (:quiz_id, :question, :option_a, :option_b, :option_c, :option_d, :correct, :explanation, :image_path)
            ");

            $uploadDir = PUBLIC_ROOT . '/uploads/questions/';

            foreach ($questionsData as $q) {
                $qImagePath = null;
                if (!empty($q['image'])) {
                    $savedName = ImageHelper::saveBase64ToWebP($q['image'], $uploadDir);
                    if ($savedName) {
                        $qImagePath = 'uploads/questions/' . $savedName;
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

            return $quizId;
        });
    }

    /**
     * Update quiz metadata.
     */
    public function update(int $id, array $data): bool
    {
        $fields = [];
        $params = ['id' => $id];

        foreach ($data as $key => $val) {
            $fields[] = "{$key} = :{$key}";
            $params[$key] = $val;
        }

        if (empty($fields)) {
            return false;
        }

        $sql = "UPDATE quizzes SET " . implode(', ', $fields) . " WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    /**
     * Delete quiz and questions.
     */
    public function delete(int $id): bool
    {
        return $this->database->transaction(function (PDO $db) use ($id) {
            $stmtQ = $db->prepare("DELETE FROM questions WHERE quiz_id = :quiz_id");
            $stmtQ->execute(['quiz_id' => $id]);

            $stmt = $db->prepare("DELETE FROM quizzes WHERE id = :id");
            return $stmt->execute(['id' => $id]);
        });
    }

    /**
     * Fetch all quizzes categorized with user completion status.
     */
    public function getCategorizedQuizzesWithUserStatus(int $userId, ?string $activeDifficulty = null): array
    {
        $quizzes = $this->getAll();

        // Filter by difficulty if provided
        if (!empty($activeDifficulty) && $activeDifficulty !== 'all') {
            $quizzes = array_filter($quizzes, function ($quiz) use ($activeDifficulty) {
                return strcasecmp($quiz['difficulty'] ?? 'Mudah', $activeDifficulty) === 0;
            });
        }

        // Fetch user completed scores
        $completedQuizzes = [];
        try {
            $stmt = $this->db->prepare("SELECT quiz_id, score FROM quiz_attempts WHERE user_id = :user_id AND quiz_id IS NOT NULL AND status = 'finished'");
            $stmt->execute(['user_id' => $userId]);
            foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $completedQuizzes[(int)$row['quiz_id']] = (int)($row['score'] ?? 0);
            }
        } catch (PDOException $e) {
            // Fallback
        }

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

            $cat = $quiz['category'];
            if (!isset($categorized[$cat])) {
                $categorized[$cat] = [];
            }
            $categorized[$cat][] = $quiz;
        }

        return $categorized;
    }
}
