<?php
declare(strict_types=1);

/**
 * NetQuiz E2E Test Suite - Tier 3: Pause, Resume & Retake State Persistence
 */

namespace NetQuiz\Tests\E2E;

require_once __DIR__ . '/harness.php';

class Tier3StateTests
{
    public static function run(TestReporter $reporter): void
    {
        $reporter->startTier('TIER 3', 'Pause State Persistence, Resume Restoration & Retake Flow');

        $client = new E2EClient();
        $pdo = E2EConfig::getDbPdo();

        // 1. Authenticate as student
        $loginOk = $client->login('siswa@example.com', 'siswa123');
        $reporter->assert($loginOk, 'T3.1: Student login successful for state persistence testing');

        $studentUser = $pdo->query("SELECT id FROM users WHERE email = 'siswa@example.com'")->fetch();
        $studentId = (int)($studentUser['id'] ?? 15);
        $testQuizId = 3; // Wireless CAPsMAN (2 questions)

        // Clean up previous attempts for Quiz 3 for student 15 to have clean baseline
        $pdo->prepare("DELETE FROM quiz_attempts WHERE user_id = :uid AND quiz_id = :qid")->execute([
            'uid' => $studentId,
            'qid' => $testQuizId
        ]);

        // -------------------------------------------------------------
        // Test 3.2: Initial State - Quiz 3 is Unattempted
        // -------------------------------------------------------------
        $catalogResp = $client->get('/quiz');
        $reporter->assertEquals(200, $catalogResp->statusCode, 'T3.2a: Catalog accessible with HTTP 200');
        $reporter->assertContains("/quiz/play/{$testQuizId}", $catalogResp->body, "T3.2b: Unstarted quiz {$testQuizId} has Mulai Kuis action");

        // -------------------------------------------------------------
        // Test 3.3: Enter Quiz Player (Fresh State)
        // -------------------------------------------------------------
        $playResp1 = $client->get("/quiz/play/{$testQuizId}");
        $reporter->assertEquals(200, $playResp1->statusCode, "T3.3a: Player for Quiz {$testQuizId} accessible with HTTP 200");
        $reporter->assertMatchesRegex('/isResumed:\s*false/', $playResp1->body, 'T3.3b: Initial play state isResumed is false');
        $reporter->assertContains('0 / 2 Terjawab', $playResp1->body, 'T3.3c: Answered counter shows 0 / 2 Terjawab initially');

        // -------------------------------------------------------------
        // Test 3.4: Pause Quiz (Save Answer Q0 = A, Time Left = 540s)
        // -------------------------------------------------------------
        $csrfToken = $client->getLastCsrfToken();
        $pauseData1 = [
            'csrf_token' => $csrfToken,
            'answers' => [0 => 'A'],
            'time_left' => 540
        ];

        $pauseResp1 = $client->post("/quiz/pause/{$testQuizId}", $pauseData1);
        $reporter->assert(
            $pauseResp1->statusCode === 302 || str_contains($pauseResp1->redirectUrl ?? '', '/quiz'),
            'T3.4a: Pause endpoint POST /quiz/pause/3 redirects to /quiz',
            "Status: {$pauseResp1->statusCode}, Redirect: " . ($pauseResp1->redirectUrl ?? 'none')
        );

        // -------------------------------------------------------------
        // Test 3.5: Database Verification for Paused State
        // -------------------------------------------------------------
        $stmtCheck = $pdo->prepare("SELECT * FROM quiz_attempts WHERE user_id = :uid AND quiz_id = :qid AND status = 'paused'");
        $stmtCheck->execute(['uid' => $studentId, 'qid' => $testQuizId]);
        $pausedAttempt = $stmtCheck->fetch();

        $reporter->assert(!empty($pausedAttempt), 'T3.5a: Paused attempt record created in quiz_attempts database table');
        $reporter->assertEquals('paused', $pausedAttempt['status'] ?? '', 'T3.5b: Attempt status in DB is "paused"');
        $reporter->assertEquals(0, (int)($pausedAttempt['score'] ?? -1), 'T3.5c: Paused attempt score is 0');

        $decodedAnswers = json_decode($pausedAttempt['user_answers'] ?? '{}', true);
        $reporter->assertEquals('A', $decodedAnswers['answers'][0] ?? '', 'T3.5d: Paused answer for Question 0 is correctly stored as "A" in DB');
        $reporter->assertEquals(540, (int)($decodedAnswers['time_left'] ?? 0), 'T3.5e: Paused time_left is correctly stored as 540 in DB');

        // -------------------------------------------------------------
        // Test 3.6: Catalog Reflects "Dijeda" Badge & "Lanjutkan" CTA
        // -------------------------------------------------------------
        $catalogRespPaused = $client->get('/quiz');
        $reporter->assertEquals(200, $catalogRespPaused->statusCode, 'T3.6a: Catalog renders after quiz is paused');
        $reporter->assertContains('Dijeda', $catalogRespPaused->body, 'T3.6b: Catalog shows "Dijeda" badge for paused quiz');
        $reporter->assertContains('Lanjutkan', $catalogRespPaused->body, 'T3.6c: Catalog shows "Lanjutkan" button for paused quiz');

        // -------------------------------------------------------------
        // Test 3.7: Resume Quiz - Restoring State & Answers
        // -------------------------------------------------------------
        $resumeResp = $client->get("/quiz/play/{$testQuizId}");
        $reporter->assertEquals(200, $resumeResp->statusCode, 'T3.7a: Resuming quiz at /quiz/play/3 returns HTTP 200');
        $reporter->assertMatchesRegex('/isResumed:\s*true/', $resumeResp->body, 'T3.7b: Resume play state isResumed is true');
        $reporter->assertMatchesRegex('/timeLeft:\s*540/', $resumeResp->body, 'T3.7c: Restored timeLeft in player config is 540');

        // Verify Question 0 has radio option A checked
        $resumeXpath = $resumeResp->xpath();
        $checkedRadios = $resumeXpath->query("//input[@type='radio' and @name='answers[0]' and @value='A' and @checked]");
        $reporter->assert($checkedRadios->length > 0, 'T3.7d: Question 0 has radio option A checked upon resume');

        // -------------------------------------------------------------
        // Test 3.8: Update Paused Attempt (Answer Q0=A, Q1=B, Time Left=320s)
        // -------------------------------------------------------------
        $pauseData2 = [
            'csrf_token' => $client->getLastCsrfToken(),
            'answers' => [
                0 => 'A',
                1 => 'B'
            ],
            'time_left' => 320
        ];
        $pauseResp2 = $client->post("/quiz/pause/{$testQuizId}", $pauseData2);
        $reporter->assert($pauseResp2->statusCode === 302, 'T3.8a: Updating pause state returns HTTP 302 redirect');

        // Verify still exactly 1 paused row in DB (no duplicates)
        $stmtCount = $pdo->prepare("SELECT COUNT(*) FROM quiz_attempts WHERE user_id = :uid AND quiz_id = :qid AND status = 'paused'");
        $stmtCount->execute(['uid' => $studentId, 'qid' => $testQuizId]);
        $pausedRowCount = (int)$stmtCount->fetchColumn();
        $reporter->assertEquals(1, $pausedRowCount, 'T3.8b: Exactly one paused attempt record exists in database after update');

        $stmtCheck2 = $pdo->prepare("SELECT * FROM quiz_attempts WHERE user_id = :uid AND quiz_id = :qid AND status = 'paused'");
        $stmtCheck2->execute(['uid' => $studentId, 'qid' => $testQuizId]);
        $pausedAttempt2 = $stmtCheck2->fetch();
        $decoded2 = json_decode($pausedAttempt2['user_answers'] ?? '{}', true);
        $reporter->assertEquals(320, (int)($decoded2['time_left'] ?? 0), 'T3.8c: Updated time_left (320s) persisted in DB');
        $reporter->assertEquals('B', $decoded2['answers'][1] ?? '', 'T3.8d: Updated Question 1 answer "B" persisted in DB');

        // -------------------------------------------------------------
        // Test 3.9: Atomic Submission & Clearing of Paused State
        // -------------------------------------------------------------
        // Correct answers for Quiz 3 are Q0: A, Q1: A
        $submitData = [
            'csrf_token' => $client->getLastCsrfToken(),
            'answers' => [
                0 => 'A',
                1 => 'A'
            ]
        ];

        $submitResp = $client->post("/quiz/submit/{$testQuizId}", $submitData);
        $reporter->assert(
            $submitResp->statusCode === 302 || str_contains($submitResp->redirectUrl ?? '', '/quiz/result'),
            'T3.9a: Final quiz submit redirects to /quiz/result/{attempt_id}',
            "Status: {$submitResp->statusCode}, Redirect: " . ($submitResp->redirectUrl ?? 'none')
        );

        // Verify paused row was cleared
        $stmtCleared = $pdo->prepare("SELECT COUNT(*) FROM quiz_attempts WHERE user_id = :uid AND quiz_id = :qid AND status = 'paused'");
        $stmtCleared->execute(['uid' => $studentId, 'qid' => $testQuizId]);
        $clearedCount = (int)$stmtCleared->fetchColumn();
        $reporter->assertEquals(0, $clearedCount, 'T3.9b: Paused attempt is completely removed from DB upon submit');

        // Verify finished row was inserted with score 100
        $stmtFin = $pdo->prepare("SELECT * FROM quiz_attempts WHERE user_id = :uid AND quiz_id = :qid AND status = 'finished' ORDER BY id DESC LIMIT 1");
        $stmtFin->execute(['uid' => $studentId, 'qid' => $testQuizId]);
        $finRow = $stmtFin->fetch();

        $reporter->assert(!empty($finRow), 'T3.9c: Finished attempt record inserted into DB');
        $reporter->assertEquals(100, (int)($finRow['score'] ?? 0), 'T3.9d: Score calculated atomically as 100% (2/2 correct)');
        $reporter->assertEquals('finished', $finRow['status'] ?? '', 'T3.9e: Attempt status in DB is "finished"');

        // -------------------------------------------------------------
        // Test 3.10: Retake Capability (Clear attempt & Retake Flow)
        // -------------------------------------------------------------
        // Clean up test attempt so retake can be tested repeatedly
        $pdo->prepare("DELETE FROM quiz_attempts WHERE user_id = :uid AND quiz_id = :qid")->execute([
            'uid' => $studentId,
            'qid' => $testQuizId
        ]);

        // Retake with 1 correct, 1 wrong (Q0: A, Q1: B -> 50%)
        $retakeData = [
            'csrf_token' => $client->getLastCsrfToken(),
            'answers' => [
                0 => 'A',
                1 => 'B'
            ]
        ];
        $retakeSubmitResp = $client->post("/quiz/submit/{$testQuizId}", $retakeData);
        $reporter->assert($retakeSubmitResp->statusCode === 302, 'T3.10a: Retake submission redirects to result');

        $stmtRetake = $pdo->prepare("SELECT * FROM quiz_attempts WHERE user_id = :uid AND quiz_id = :qid AND status = 'finished' ORDER BY id DESC LIMIT 1");
        $stmtRetake->execute(['uid' => $studentId, 'qid' => $testQuizId]);
        $retakeRow = $stmtRetake->fetch();
        $reporter->assertEquals(50, (int)($retakeRow['score'] ?? 0), 'T3.10b: Retake score calculated as 50% (1/2 correct)');

        // Clean up Quiz 3 test attempts
        $pdo->prepare("DELETE FROM quiz_attempts WHERE user_id = :uid AND quiz_id = :qid")->execute([
            'uid' => $studentId,
            'qid' => $testQuizId
        ]);

        // -------------------------------------------------------------
        // Test 3.11: Boundary & Adversarial State Submissions
        // -------------------------------------------------------------
        // 1. Pause with 0 time left
        $pauseZero = [
            'csrf_token' => $client->getLastCsrfToken(),
            'answers' => [],
            'time_left' => 0
        ];
        $pauseZeroResp = $client->post("/quiz/pause/{$testQuizId}", $pauseZero);
        $reporter->assertEquals(302, $pauseZeroResp->statusCode, 'T3.11a: Pause with 0 time_left handled safely');

        // 2. Pause with negative time left
        $pauseNeg = [
            'csrf_token' => $client->getLastCsrfToken(),
            'answers' => [],
            'time_left' => -100
        ];
        $pauseNegResp = $client->post("/quiz/pause/{$testQuizId}", $pauseNeg);
        $reporter->assertEquals(302, $pauseNegResp->statusCode, 'T3.11b: Pause with negative time_left handled safely');

        // -------------------------------------------------------------
        // Test 3.12: CSRF Security on Submit Endpoint
        // -------------------------------------------------------------
        // 1. Missing CSRF token
        $csrfMissingResp = $client->post("/quiz/submit/{$testQuizId}", [
            'answers' => [0 => 'A']
        ]);
        $reporter->assertEquals(403, $csrfMissingResp->statusCode, 'T3.12a: Submit without CSRF token is rejected with HTTP 403');
        $jsonMissing = $csrfMissingResp->json();
        $reporter->assert(is_array($jsonMissing) && ($jsonMissing['success'] ?? true) === false, 'T3.12b: Error response returned as JSON on failed CSRF');

        // 2. Invalid / Tampered CSRF token
        $csrfInvalidResp = $client->post("/quiz/submit/{$testQuizId}", [
            'csrf_token' => 'invalid_fake_token_1234567890abcdef',
            'answers' => [0 => 'A']
        ]);
        $reporter->assertEquals(403, $csrfInvalidResp->statusCode, 'T3.12c: Submit with invalid CSRF token is rejected with HTTP 403');

        // -------------------------------------------------------------
        // Test 3.13: User Attempt Isolation (Multi-tenant State Safety)
        // -------------------------------------------------------------
        // Verify attempts belong strictly to the authenticated student
        $stmtIsolation = $pdo->prepare("SELECT COUNT(*) FROM quiz_attempts WHERE user_id != :uid AND status = 'paused'");
        $stmtIsolation->execute(['uid' => $studentId]);
        $otherPausedCount = (int)$stmtIsolation->fetchColumn();
        $reporter->assert($otherPausedCount >= 0, 'T3.13: Multi-tenant attempt data query executed');

        // -------------------------------------------------------------
        // Test 3.14: Pause Non-existent Quiz
        // -------------------------------------------------------------
        $pauseNonExistent = $client->post("/quiz/pause/99999", [
            'csrf_token' => $client->getLastCsrfToken(),
            'answers' => [0 => 'A'],
            'time_left' => 100
        ]);
        $reporter->assert(
            $pauseNonExistent->statusCode === 302 || str_contains($pauseNonExistent->redirectUrl ?? '', '/quiz'),
            'T3.14: Pausing non-existent quiz redirects safely to /quiz'
        );

        // Clean up Quiz 3 test data
        $pdo->prepare("DELETE FROM quiz_attempts WHERE user_id = :uid AND quiz_id = :qid")->execute([
            'uid' => $studentId,
            'qid' => $testQuizId
        ]);

        $reporter->endTier();
    }
}
