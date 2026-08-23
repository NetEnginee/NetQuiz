<?php

declare(strict_types=1);

namespace App\Repositories;

use PDO;
use App\Core\Database;

/**
 * QuestionRepository - Question Data Access Implementation.
 */
class QuestionRepository implements QuestionRepositoryInterface
{
    private Database $db;

    public function __construct(?Database $database = null)
    {
        $this->db = $database ?? Database::getInstance();
    }

    /**
     * Get all questions for a given quiz ID.
     */
    public function getByQuizId(int $quizId): array
    {
        $stmt = $this->db->prepare("SELECT * FROM questions WHERE quiz_id = :quiz_id ORDER BY id ASC");
        $stmt->execute(['quiz_id' => $quizId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get single question by ID.
     */
    public function getById(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM questions WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        $question = $stmt->fetch(PDO::FETCH_ASSOC);
        return $question ?: null;
    }

    /**
     * Create single question.
     */
    public function create(
        int $quizId,
        string $question,
        string $optionA,
        string $optionB,
        string $optionC,
        string $optionD,
        string $correct,
        ?string $explanation = null,
        ?string $imagePath = null
    ): int {
        $stmt = $this->db->prepare("
            INSERT INTO questions (quiz_id, question, option_a, option_b, option_c, option_d, correct, explanation, image_path) 
            VALUES (:quiz_id, :question, :option_a, :option_b, :option_c, :option_d, :correct, :explanation, :image_path)
        ");
        $stmt->execute([
            'quiz_id' => $quizId,
            'question' => $question,
            'option_a' => $optionA,
            'option_b' => $optionB,
            'option_c' => $optionC,
            'option_d' => $optionD,
            'correct' => strtoupper($correct),
            'explanation' => $explanation,
            'image_path' => $imagePath
        ]);

        return (int)$this->db->lastInsertId();
    }

    /**
     * Bulk create questions inside an atomic transaction.
     */
    public function createBulk(int $quizId, array $questions): bool
    {
        return $this->db->transaction(function (PDO $db) use ($quizId, $questions) {
            $stmt = $db->prepare("
                INSERT INTO questions (quiz_id, question, option_a, option_b, option_c, option_d, correct, explanation, image_path) 
                VALUES (:quiz_id, :question, :option_a, :option_b, :option_c, :option_d, :correct, :explanation, :image_path)
            ");

            foreach ($questions as $q) {
                $stmt->execute([
                    'quiz_id' => $quizId,
                    'question' => $q['question'],
                    'option_a' => $q['option_a'],
                    'option_b' => $q['option_b'],
                    'option_c' => $q['option_c'],
                    'option_d' => $q['option_d'],
                    'correct' => strtoupper($q['correct']),
                    'explanation' => $q['explanation'] ?? null,
                    'image_path' => $q['image_path'] ?? null
                ]);
            }

            return true;
        });
    }

    /**
     * Delete questions by quiz ID.
     */
    public function deleteByQuizId(int $quizId): bool
    {
        $stmt = $this->db->prepare("DELETE FROM questions WHERE quiz_id = :quiz_id");
        return $stmt->execute(['quiz_id' => $quizId]);
    }

    /**
     * Delete single question by ID.
     */
    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM questions WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
}
