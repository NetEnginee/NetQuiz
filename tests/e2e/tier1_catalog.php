<?php
declare(strict_types=1);

/**
 * NetQuiz E2E Test Suite - Tier 1: Quiz Catalog, Topic Groupings & Filters
 */

namespace NetQuiz\Tests\E2E;

require_once __DIR__ . '/harness.php';

class Tier1CatalogTests
{
    public static function run(TestReporter $reporter): void
    {
        $reporter->startTier('TIER 1', 'Quiz Catalog, Topic Groupings, Difficulty Filtering & Badges');

        $client = new E2EClient();
        $pdo = E2EConfig::getDbPdo();

        // -------------------------------------------------------------
        // Test 1.1: Unauthenticated Access Guard
        // -------------------------------------------------------------
        $guestResp = $client->get('/quiz');
        $reporter->assert(
            $guestResp->statusCode === 302 || str_contains($guestResp->redirectUrl ?? '', '/login'),
            'T1.1: Unauthenticated request to /quiz redirects to /login',
            "Status: {$guestResp->statusCode}, Redirect: " . ($guestResp->redirectUrl ?? 'none')
        );

        // -------------------------------------------------------------
        // Test 1.2: Student Authentication
        // -------------------------------------------------------------
        $loginOk = $client->login('siswa@example.com', 'siswa123');
        $reporter->assert($loginOk, 'T1.2: Student login successful with credentials (siswa@example.com / siswa123)');

        // -------------------------------------------------------------
        // Test 1.3: Catalog Page Loading & HTTP 200
        // -------------------------------------------------------------
        $catalogResp = $client->get('/quiz');
        $reporter->assertEquals(200, $catalogResp->statusCode, 'T1.3: Student can access /quiz catalog with HTTP 200');
        $reporter->assertContains('Daftar Quiz | NetQuiz', $catalogResp->body, 'T1.3b: Catalog page title rendered');

        // -------------------------------------------------------------
        // Test 1.4: Dynamic Breadcrumbs Rendering
        // -------------------------------------------------------------
        $xpath = $catalogResp->xpath();
        $breadcrumbNodes = $xpath->query("//nav[@aria-label='Breadcrumb'] | //div[contains(@class, 'breadcrumb')] | //div[contains(., 'Siswa')]");
        $reporter->assert($breadcrumbNodes->length > 0, 'T1.4: Breadcrumb container is present on catalog page');
        $reporter->assertContains('Siswa', $catalogResp->body, 'T1.4b: Breadcrumb contains "Siswa" root label');
        $reporter->assertContains('Kuis', $catalogResp->body, 'T1.4c: Breadcrumb contains "Kuis" current label');

        // -------------------------------------------------------------
        // Test 1.5: Difficulty Filter Bar Tabs
        // -------------------------------------------------------------
        $reporter->assertContains('Tingkat Kesulitan:', $catalogResp->body, 'T1.5a: Filter bar label "Tingkat Kesulitan:" present');
        $reporter->assertContains('difficulty=all', $catalogResp->body, 'T1.5b: Filter option "Semua" (?difficulty=all) link present');
        $reporter->assertContains('difficulty=Mudah', $catalogResp->body, 'T1.5c: Filter option "Mudah" (?difficulty=Mudah) link present');
        $reporter->assertContains('difficulty=Sedang', $catalogResp->body, 'T1.5d: Filter option "Sedang" (?difficulty=Sedang) link present');
        $reporter->assertContains('difficulty=Sulit', $catalogResp->body, 'T1.5e: Filter option "Sulit" (?difficulty=Sulit) link present');

        // Check active class on default (all)
        $activeTabs = $xpath->query("//a[contains(@class, 'quiz-segment-tab') and contains(@class, 'active')]");
        $reporter->assert($activeTabs->length > 0, 'T1.5f: Active difficulty filter tab is highlighted');

        // -------------------------------------------------------------
        // Test 1.6: Topic Categories Rendered in DOM
        // -------------------------------------------------------------
        $expectedCategories = ['Routing', 'Firewall & NAT', 'Wireless', 'Network Management'];
        foreach ($expectedCategories as $cat) {
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM quizzes WHERE category = :cat");
            $stmt->execute(['cat' => $cat]);
            $countInDb = (int)$stmt->fetchColumn();

            if ($countInDb > 0) {
                $escapedCat = htmlspecialchars($cat);
                $reporter->assertContains($escapedCat, $catalogResp->body, "T1.6: Category '{$cat}' section rendered in catalog");
                $reporter->assertMatchesRegex('/' . preg_quote($escapedCat, '/') . '.*?\(' . $countInDb . '\s+Kuis\)/s', $catalogResp->body, "T1.6b: Category '{$cat}' shows correct count ({$countInDb} Kuis)");
            }
        }

        // -------------------------------------------------------------
        // Test 1.7: Card Blueprint Styling & Crosshair Accents
        // -------------------------------------------------------------
        $crosshairs = $xpath->query("//span[contains(@class, 'corner-crosshair')]");
        $reporter->assertGreaterThanOrEqual(4, $crosshairs->length, 'T1.7: Corner crosshairs (+) present on quiz cards');

        // -------------------------------------------------------------
        // Test 1.8: Card Duration & Question Count Metadata
        // -------------------------------------------------------------
        $reporter->assertMatchesRegex('/\d+\s*mnt/', $catalogResp->body, 'T1.8a: Duration indicator (X mnt) rendered');
        $reporter->assertMatchesRegex('/\d+\s*soal/', $catalogResp->body, 'T1.8b: Question count indicator (X soal) rendered');

        // -------------------------------------------------------------
        // Test 1.9: Difficulty Filter - "Mudah"
        // -------------------------------------------------------------
        $mudahResp = $client->get('/quiz?difficulty=Mudah');
        $reporter->assertEquals(200, $mudahResp->statusCode, 'T1.9a: Filter /quiz?difficulty=Mudah returns 200');
        $mudahXpath = $mudahResp->xpath();
        $mudahActive = $mudahXpath->query("//a[contains(@href, 'difficulty=Mudah') and contains(@class, 'active')]");
        $reporter->assert($mudahActive->length > 0, 'T1.9b: "Mudah" tab is marked active when filter is applied');

        // Verify quizzes returned under Mudah are indeed Mudah
        $stmtMudah = $pdo->query("SELECT title FROM quizzes WHERE difficulty = 'Mudah'");
        $mudahQuizzes = $stmtMudah->fetchAll(\PDO::FETCH_COLUMN);
        foreach ($mudahQuizzes as $title) {
            $reporter->assertContains(htmlspecialchars($title), $mudahResp->body, "T1.9c: Mudah quiz '{$title}' visible under Mudah filter");
        }

        // Verify quizzes with other difficulties are NOT shown under Mudah
        $stmtNonMudah = $pdo->query("SELECT title FROM quizzes WHERE difficulty != 'Mudah'");
        $nonMudahQuizzes = $stmtNonMudah->fetchAll(\PDO::FETCH_COLUMN);
        foreach ($nonMudahQuizzes as $title) {
            $reporter->assert(
                !str_contains($mudahResp->body, htmlspecialchars($title)),
                "T1.9d: Non-Mudah quiz '{$title}' is excluded from Mudah filter"
            );
        }

        // -------------------------------------------------------------
        // Test 1.10: Difficulty Filter - "Sedang"
        // -------------------------------------------------------------
        $sedangResp = $client->get('/quiz?difficulty=Sedang');
        $reporter->assertEquals(200, $sedangResp->statusCode, 'T1.10a: Filter /quiz?difficulty=Sedang returns 200');
        $stmtSedang = $pdo->query("SELECT title FROM quizzes WHERE difficulty = 'Sedang'");
        $sedangQuizzes = $stmtSedang->fetchAll(\PDO::FETCH_COLUMN);
        foreach ($sedangQuizzes as $title) {
            $reporter->assertContains(htmlspecialchars($title), $sedangResp->body, "T1.10b: Sedang quiz '{$title}' visible under Sedang filter");
        }

        // -------------------------------------------------------------
        // Test 1.11: Difficulty Filter - "Sulit"
        // -------------------------------------------------------------
        $sulitResp = $client->get('/quiz?difficulty=Sulit');
        $reporter->assertEquals(200, $sulitResp->statusCode, 'T1.11a: Filter /quiz?difficulty=Sulit returns 200');
        $stmtSulit = $pdo->query("SELECT title FROM quizzes WHERE difficulty = 'Sulit'");
        $sulitQuizzes = $stmtSulit->fetchAll(\PDO::FETCH_COLUMN);
        if (empty($sulitQuizzes)) {
            $reporter->assertContains('Tidak Ada Kuis Tersedia', $sulitResp->body, 'T1.11b: Empty state rendered when no quizzes match filter');
        } else {
            foreach ($sulitQuizzes as $title) {
                $reporter->assertContains(htmlspecialchars($title), $sulitResp->body, "T1.11c: Sulit quiz '{$title}' visible under Sulit filter");
            }
        }

        // -------------------------------------------------------------
        // Test 1.12: Status Badges & Dynamic CTAs for Student
        // -------------------------------------------------------------
        // Check student (id 15) attempts in database
        $studentUser = $pdo->query("SELECT id FROM users WHERE email = 'siswa@example.com'")->fetch();
        $studentId = (int)($studentUser['id'] ?? 15);

        // Check finished attempt
        $stmtFin = $pdo->prepare("SELECT quiz_id, score FROM quiz_attempts WHERE user_id = :uid AND status = 'finished' ORDER BY id DESC");
        $stmtFin->execute(['uid' => $studentId]);
        $finAttempts = $stmtFin->fetchAll();

        foreach ($finAttempts as $fin) {
            $qId = (int)$fin['quiz_id'];
            if ($qId <= 0) continue;
            $score = (int)$fin['score'];
            $stmtQ = $pdo->prepare("SELECT title FROM quizzes WHERE id = :id");
            $stmtQ->execute(['id' => $qId]);
            $quizTitle = $stmtQ->fetchColumn();

            if ($quizTitle) {
                $reporter->assertContains("/quiz/review/{$qId}", $catalogResp->body, "T1.12a: Finished quiz '{$quizTitle}' has Review CTA button (/quiz/review/{$qId})");
            }
        }

        // Check paused attempt
        $stmtPause = $pdo->prepare("SELECT quiz_id FROM quiz_attempts WHERE user_id = :uid AND status = 'paused' LIMIT 1");
        $stmtPause->execute(['uid' => $studentId]);
        $pausedQuizId = (int)($stmtPause->fetchColumn() ?: 0);

        if ($pausedQuizId > 0) {
            $reporter->assertContains("/quiz/play/{$pausedQuizId}", $catalogResp->body, "T1.12b: Paused quiz has Lanjutkan/Play CTA button (/quiz/play/{$pausedQuizId})");
        }

        // Check unattempted quiz has "Mulai Kuis"
        $stmtUnstarted = $pdo->prepare("
            SELECT id, title FROM quizzes 
            WHERE id NOT IN (SELECT quiz_id FROM quiz_attempts WHERE user_id = :uid AND quiz_id IS NOT NULL)
            LIMIT 1
        ");
        $stmtUnstarted->execute(['uid' => $studentId]);
        $unstartedQuiz = $stmtUnstarted->fetch();

        if ($unstartedQuiz) {
            $unstartedId = (int)$unstartedQuiz['id'];
            $reporter->assertContains("/quiz/play/{$unstartedId}", $catalogResp->body, "T1.12c: Unstarted quiz has Mulai CTA button (/quiz/play/{$unstartedId})");
        }

        // -------------------------------------------------------------
        // Test 1.13: Admin Catalog View
        // -------------------------------------------------------------
        $adminClient = new E2EClient();
        $adminLoginOk = $adminClient->login('admin@routerosquiz.academy', 'admin12345');
        $reporter->assert($adminLoginOk, 'T1.13a: Admin login successful with credentials (admin@routerosquiz.academy / admin12345)');

        $adminCatalogResp = $adminClient->get('/quiz');
        $reporter->assertEquals(200, $adminCatalogResp->statusCode, 'T1.13b: Admin can access /quiz catalog with HTTP 200');
        $reporter->assertContains('Daftar Quiz | NetQuiz', $adminCatalogResp->body, 'T1.13c: Admin catalog title rendered correctly');

        // -------------------------------------------------------------
        // Test 1.14: Adversarial & Boundary Parameters
        // -------------------------------------------------------------
        // 1. Malformed difficulty parameter (SQL injection / XSS payload)
        $xssPayload = '<script>alert(1)</script>';
        $adversarialResp1 = $client->get('/quiz?difficulty=' . urlencode($xssPayload));
        $reporter->assertEquals(200, $adversarialResp1->statusCode, 'T1.14a: Malformed difficulty query parameter handled gracefully with HTTP 200');
        $reporter->assert(
            !str_contains($adversarialResp1->body, '<script>alert(1)</script>'),
            'T1.14b: XSS payload in query parameter is neutralized/escaped'
        );

        // 2. Extra long difficulty string
        $longStr = str_repeat('A', 500);
        $adversarialResp2 = $client->get("/quiz?difficulty={$longStr}");
        $reporter->assertEquals(200, $adversarialResp2->statusCode, 'T1.14c: Extreme length difficulty parameter handled with HTTP 200');

        $reporter->endTier();
    }
}
