<?php
declare(strict_types=1);

/**
 * NetQuiz E2E Test Suite - Tier 2: Quiz Player, 1-Question Carousel, Palette & Timer
 */

namespace NetQuiz\Tests\E2E;

require_once __DIR__ . '/harness.php';

class Tier2PlayerTests
{
    public static function run(TestReporter $reporter): void
    {
        $reporter->startTier('TIER 2', 'Exam Player, 1-Question Carousel, Jump Palette, Timer & Modals');

        $client = new E2EClient();
        $pdo = E2EConfig::getDbPdo();

        // -------------------------------------------------------------
        // Test 2.1: Unauthenticated Access Guard
        // -------------------------------------------------------------
        $guestResp = $client->get('/quiz/play/1');
        $reporter->assert(
            $guestResp->statusCode === 302 || str_contains($guestResp->redirectUrl ?? '', '/login'),
            'T2.1: Unauthenticated request to /quiz/play/1 redirects to /login',
            "Status: {$guestResp->statusCode}, Redirect: " . ($guestResp->redirectUrl ?? 'none')
        );

        // -------------------------------------------------------------
        // Test 2.2: Admin Authentication for Full Preview Access
        // (Using Admin so we can test player view without attempt lockouts)
        // -------------------------------------------------------------
        $adminLoginOk = $client->login('admin@routerosquiz.academy', 'admin12345');
        $reporter->assert($adminLoginOk, 'T2.2: Admin login successful for player inspection');

        // -------------------------------------------------------------
        // Test 2.3: Player Page Loading for Quiz 1 (MTCNA)
        // -------------------------------------------------------------
        $playResp = $client->get('/quiz/play/1');
        $reporter->assertEquals(200, $playResp->statusCode, 'T2.3: Admin can access /quiz/play/1 with HTTP 200');
        $reporter->assertContains('Mulai Kuis -', $playResp->body, 'T2.3b: Player title contains "Mulai Kuis -"');
        $reporter->assertContains('MTCNA', $playResp->body, 'T2.3c: Quiz title "MTCNA" rendered in player view');

        // -------------------------------------------------------------
        // Test 2.4: 1-Question Carousel DOM Structure
        // -------------------------------------------------------------
        $xpath = $playResp->xpath();
        $qBlocks = $xpath->query("//div[contains(@class, 'question-block')]");
        
        $stmtQ = $pdo->prepare("SELECT COUNT(*) FROM questions WHERE quiz_id = 1");
        $stmtQ->execute();
        $expectedQCount = (int)$stmtQ->fetchColumn();

        $reporter->assertEquals($expectedQCount, $qBlocks->length, "T2.4a: Question blocks count matches DB ({$expectedQCount} questions)");

        // Verify first question is visible and subsequent are hidden
        $firstBlockStyle = $qBlocks->item(0)->getAttribute('style');
        $reporter->assertContains('display: block', $firstBlockStyle, 'T2.4b: First question block (index 0) has style display: block');

        if ($qBlocks->length > 1) {
            $secondBlockStyle = $qBlocks->item(1)->getAttribute('style');
            $reporter->assertContains('display: none', $secondBlockStyle, 'T2.4c: Second question block (index 1) has style display: none');
        }

        // -------------------------------------------------------------
        // Test 2.5: Question Options (A, B, C, D) & Form Inputs
        // -------------------------------------------------------------
        $radioInputs = $xpath->query("//input[@type='radio' and contains(@name, 'answers')]");
        $expectedTotalRadios = $expectedQCount * 4;
        $reporter->assertEquals($expectedTotalRadios, $radioInputs->length, "T2.5a: Total radio options equals {$expectedTotalRadios} (4 options per question)");

        // Verify option labels & badges
        $optionBadges = $xpath->query("//div[contains(@class, 'option-badge')]");
        $reporter->assertEquals($expectedTotalRadios, $optionBadges->length, "T2.5b: Total option badges equals {$expectedTotalRadios}");

        // Verify question statements
        $stmtFirstQ = $pdo->query("SELECT question, option_a, option_b, option_c, option_d FROM questions WHERE quiz_id = 1 ORDER BY id ASC LIMIT 1")->fetch();
        $reporter->assertContains(htmlspecialchars($stmtFirstQ['question']), $playResp->body, 'T2.5c: Question 1 text rendered accurately');
        $reporter->assertContains(htmlspecialchars($stmtFirstQ['option_a']), $playResp->body, 'T2.5d: Option A text rendered accurately');

        // -------------------------------------------------------------
        // Test 2.6: Question Palette Grid & Button States
        // -------------------------------------------------------------
        $paletteBtns = $xpath->query("//button[contains(@class, 'palette-btn')]");
        $reporter->assertEquals($expectedQCount, $paletteBtns->length, "T2.6a: Palette buttons count equals total questions ({$expectedQCount})");

        // Verify active question palette button has .current class
        $firstPaletteClass = $paletteBtns->item(0)->getAttribute('class');
        $reporter->assertContains('current', $firstPaletteClass, 'T2.6b: Palette button 1 has class "current" initially');

        // Verify data-index attributes
        for ($i = 0; $i < $paletteBtns->length; $i++) {
            $dataIdx = $paletteBtns->item($i)->getAttribute('data-index');
            $reporter->assertEquals((string)$i, $dataIdx, "T2.6c: Palette button " . ($i + 1) . " has data-index='{$i}'");
        }

        // Answered counter text
        $reporter->assertContains('0 / ' . $expectedQCount . ' Terjawab', $playResp->body, 'T2.6d: Initial answered counter shows "0 / ' . $expectedQCount . ' Terjawab"');

        // -------------------------------------------------------------
        // Test 2.7: Carousel Navigation Controls
        // -------------------------------------------------------------
        $btnPrev = $xpath->query("//button[@id='btn-prev']");
        $reporter->assertEquals(1, $btnPrev->length, 'T2.7a: Previous button (#btn-prev) exists in carousel footer');

        $btnNext = $xpath->query("//button[@id='btn-next']");
        $reporter->assertEquals(1, $btnNext->length, 'T2.7b: Next button (#btn-next) exists in carousel footer');

        $btnSubmitCarousel = $xpath->query("//button[@id='btn-submit-carousel']");
        $reporter->assertEquals(1, $btnSubmitCarousel->length, 'T2.7c: Submit button (#btn-submit-carousel) exists in carousel footer');

        // Check submit button is hidden on slide 0
        $submitStyle = $btnSubmitCarousel->item(0)->getAttribute('style');
        $reporter->assertContains('display: none', $submitStyle, 'T2.7d: Submit button is hidden on first slide (display: none)');

        // -------------------------------------------------------------
        // Test 2.8: Real-time Countdown Timer & JS Config
        // -------------------------------------------------------------
        $reporter->assertContains('id="quiz-timer-desktop"', $playResp->body, 'T2.8a: Desktop timer container (#quiz-timer-desktop) present');
        $reporter->assertContains('class="timer-display-text"', $playResp->body, 'T2.8b: Timer display text element present');
        $reporter->assertContains('name="time_left"', $playResp->body, 'T2.8c: Hidden input time_left present in form');

        // Verify window.QUIZ_PLAYER_CONFIG
        $reporter->assertContains('window.QUIZ_PLAYER_CONFIG', $playResp->body, 'T2.8d: window.QUIZ_PLAYER_CONFIG script block present');
        $reporter->assertMatchesRegex('/quizId:\s*1/', $playResp->body, 'T2.8e: QUIZ_PLAYER_CONFIG contains quizId: 1');
        $reporter->assertMatchesRegex('/durationSeconds:\s*900/', $playResp->body, 'T2.8f: QUIZ_PLAYER_CONFIG contains durationSeconds: 900 (15 min)');
        $reporter->assertMatchesRegex('/timeLeft:\s*900/', $playResp->body, 'T2.8g: QUIZ_PLAYER_CONFIG contains timeLeft: 900');

        // -------------------------------------------------------------
        // Test 2.9: Submit Confirmation Modal
        // -------------------------------------------------------------
        $reporter->assertContains('id="submit-confirm-modal"', $playResp->body, 'T2.9a: Submit confirmation modal (#submit-confirm-modal) present');
        $reporter->assertContains('Kumpulkan Ujian Kuis?', $playResp->body, 'T2.9b: Submit modal title "Kumpulkan Ujian Kuis?" rendered');
        $reporter->assertContains('id="modal-answered-count"', $playResp->body, 'T2.9c: Modal answered count placeholder (#modal-answered-count) present');
        $reporter->assertContains('id="modal-unanswered-count"', $playResp->body, 'T2.9d: Modal unanswered count placeholder (#modal-unanswered-count) present');
        $reporter->assertContains('id="btn-cancel-submit-modal"', $playResp->body, 'T2.9e: Cancel submit button (#btn-cancel-submit-modal) present');
        $reporter->assertContains('id="btn-final-submit"', $playResp->body, 'T2.9f: Final submit button (#btn-final-submit) present');

        // -------------------------------------------------------------
        // Test 2.10: Pause Confirmation Modal
        // -------------------------------------------------------------
        $reporter->assertContains('id="pause-dialog"', $playResp->body, 'T2.10a: Pause confirmation modal (#pause-dialog) present');
        $reporter->assertContains('Jeda Pengerjaan Ujian?', $playResp->body, 'T2.10b: Pause modal title "Jeda Pengerjaan Ujian?" rendered');
        $reporter->assertContains('id="btn-cancel-pause"', $playResp->body, 'T2.10c: Cancel pause button (#btn-cancel-pause) present');
        $reporter->assertContains('id="btn-confirm-pause"', $playResp->body, 'T2.10d: Confirm pause button (#btn-confirm-pause) present');

        // -------------------------------------------------------------
        // Test 2.11: Form Action & CSRF Token
        // -------------------------------------------------------------
        $quizForm = $xpath->query("//form[@id='quiz-form']");
        $reporter->assertEquals(1, $quizForm->length, 'T2.11a: Quiz form (#quiz-form) present');
        $formAction = $quizForm->item(0)->getAttribute('action');
        $reporter->assertContains('/quiz/submit/1', $formAction, 'T2.11b: Quiz form action points to /quiz/submit/1');

        $csrfInputs = $xpath->query("//input[@name='csrf_token']");
        $reporter->assertGreaterThanOrEqual(1, $csrfInputs->length, 'T2.11c: CSRF token input present in quiz form');

        // -------------------------------------------------------------
        // Test 2.12: Player Image Attachment Handling (Quiz with Image)
        // -------------------------------------------------------------
        $stmtImgQ = $pdo->query("SELECT q.quiz_id, q.image_path FROM questions q WHERE q.image_path IS NOT NULL LIMIT 1")->fetch();
        if ($stmtImgQ) {
            $imgQuizId = (int)$stmtImgQ['quiz_id'];
            $imgPlayResp = $client->get("/quiz/play/{$imgQuizId}");
            $reporter->assertEquals(200, $imgPlayResp->statusCode, "T2.12a: Player renders quiz with image attachment (Quiz {$imgQuizId})");
            $reporter->assertContains(htmlspecialchars($stmtImgQ['image_path']), $imgPlayResp->body, 'T2.12b: Question image path rendered in img src');
        }

        // -------------------------------------------------------------
        // Test 2.13: Edge Case & Boundary Checks
        // -------------------------------------------------------------
        // 1. Non-existent Quiz ID
        $nonExistentResp = $client->get('/quiz/play/99999');
        $reporter->assert(
            $nonExistentResp->statusCode === 302 || str_contains($nonExistentResp->redirectUrl ?? '', '/quiz'),
            'T2.13a: Non-existent quiz ID (/quiz/play/99999) redirects to /quiz',
            "Status: {$nonExistentResp->statusCode}"
        );

        // 2. Negative Quiz ID
        $negativeResp = $client->get('/quiz/play/-5');
        $reporter->assert(
            $negativeResp->statusCode === 302 || $negativeResp->statusCode === 404 || str_contains($negativeResp->redirectUrl ?? '', '/quiz'),
            'T2.13b: Negative quiz ID (/quiz/play/-5) handled safely without 500 error',
            "Status: {$negativeResp->statusCode}"
        );

        // 3. String / SQL Injection in Quiz ID parameter
        $sqliResp = $client->get('/quiz/play/1%27%20OR%201=1--');
        $reporter->assert(
            $sqliResp->statusCode === 200 || $sqliResp->statusCode === 302 || $sqliResp->statusCode === 404,
            'T2.13c: SQL injection payload in quiz play URL handled safely without 500 error',
            "Status: {$sqliResp->statusCode}"
        );

        $reporter->endTier();
    }
}
