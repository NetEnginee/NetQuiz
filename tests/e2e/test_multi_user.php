<?php

declare(strict_types=1);

namespace NetQuiz\Tests\E2E;

require_once __DIR__ . '/harness.php';

$reporter = new TestReporter();
$reporter->startTier('MULTI-USER AUTH', 'Multi-User Authentication, Username/Email Support & Rate-Limit Isolation');

$pdo = E2EConfig::getDbPdo();

// 1. Ensure login_attempts table exists
$pdo->exec("
    CREATE TABLE IF NOT EXISTS `login_attempts` (
      `id` int NOT NULL AUTO_INCREMENT,
      `ip_address` varchar(45) NOT NULL,
      `email` varchar(100) NOT NULL,
      `attempted_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`),
      KEY `idx_ip_email` (`ip_address`, `email`),
      KEY `idx_email` (`email`),
      KEY `idx_attempted_at` (`attempted_at`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
");
$pdo->exec("DELETE FROM login_attempts");

// 2. Setup Test Users in Database
$passwordHash = password_hash('password123', PASSWORD_DEFAULT);

// User 1: student_one / student1@example.com
$pdo->exec("DELETE FROM users WHERE email IN ('student1@example.com', 'student2@example.com', 'suspended@example.com')");
$pdo->exec("DELETE FROM users WHERE username IN ('student_one', 'student_two', 'suspended_user')");

$stmt = $pdo->prepare("INSERT INTO users (username, email, password, status) VALUES (:u, :e, :p, :s)");
$stmt->execute(['u' => 'student_one', 'e' => 'student1@example.com', 'p' => $passwordHash, 's' => 'Aktif']);
$user1Id = (int)$pdo->lastInsertId();

$stmt->execute(['u' => 'student_two', 'e' => 'student2@example.com', 'p' => $passwordHash, 's' => 'Aktif']);
$user2Id = (int)$pdo->lastInsertId();

$stmt->execute(['u' => 'suspended_user', 'e' => 'suspended@example.com', 'p' => $passwordHash, 's' => 'Nonaktif']);
$userSuspendedId = (int)$pdo->lastInsertId();

// -------------------------------------------------------------
// Test 1: User 1 Login with Email
// -------------------------------------------------------------
$client1 = new E2EClient();
$login1 = $client1->login('student1@example.com', 'password123');
$reporter->assert($login1, 'Test 1: User 1 can log in with email (student1@example.com)');

// Check User 1 Dashboard shows User 1
$dash1 = $client1->get('/');
$reporter->assertEquals(200, $dash1->statusCode, 'Test 1b: User 1 can access dashboard');
$reporter->assertContains('student_one', $dash1->body, 'Test 1c: Dashboard shows User 1 username (student_one)');

// -------------------------------------------------------------
// Test 2: User 2 Login with Email (Concurrent session on another client)
// -------------------------------------------------------------
$client2 = new E2EClient();
$login2 = $client2->login('student2@example.com', 'password123');
$reporter->assert($login2, 'Test 2: User 2 can log in with email (student2@example.com)');

// Check User 2 Dashboard shows User 2
$dash2 = $client2->get('/');
$reporter->assertEquals(200, $dash2->statusCode, 'Test 2b: User 2 can access dashboard');
$reporter->assertContains('student_two', $dash2->body, 'Test 2c: Dashboard shows User 2 username (student_two)');

// -------------------------------------------------------------
// Test 3: User 1 and User 2 Login with Username (not email)
// -------------------------------------------------------------
$client3 = new E2EClient();
$login3 = $client3->login('student_one', 'password123');
$reporter->assert($login3, 'Test 3a: User 1 can log in with username (student_one)');

$client4 = new E2EClient();
$login4 = $client4->login('student_two', 'password123');
$reporter->assert($login4, 'Test 3b: User 2 can log in with username (student_two)');

// -------------------------------------------------------------
// Test 4: Suspended User cannot login
// -------------------------------------------------------------
$clientSusp = new E2EClient();
$loginSusp = $clientSusp->login('suspended@example.com', 'password123');
$reporter->assert(!$loginSusp, 'Test 4: Suspended user is rejected from logging in');

// -------------------------------------------------------------
// Test 5: Rate-Limit Isolation (User 1 fails 5 times -> User 2 must STILL be able to log in!)
// -------------------------------------------------------------
$attackerClient = new E2EClient();
for ($i = 1; $i <= 5; $i++) {
    $attackerClient->login('student1@example.com', 'wrong_password_' . $i);
}

// User 1 should now be rate-limited
$user1Blocked = $attackerClient->login('student1@example.com', 'password123');
$reporter->assert(!$user1Blocked, 'Test 5a: User 1 is rate-limited after 5 failed attempts');

// CRITICAL TEST: User 2 on the SAME IP / machine MUST NOT be blocked!
$client2Fresh = new E2EClient();
$user2Allowed = $client2Fresh->login('student2@example.com', 'password123');
$reporter->assert($user2Allowed, 'Test 5b: User 2 CAN STILL LOG IN successfully despite User 1 being rate-limited on the same IP');

// -------------------------------------------------------------
// Test 6: Logout isolation
// -------------------------------------------------------------
$logoutClient = new E2EClient();
$logoutClient->login('student1@example.com', 'password123');
$logoutResp = $logoutClient->get('/logout');
$reporter->assert(
    $logoutResp->statusCode === 302 || str_contains($logoutResp->redirectUrl ?? '', '/login'),
    'Test 6a: Logout redirects to /login'
);

// After logout, accessing protected route redirects to /login
$afterLogout = $logoutClient->get('/quiz');
$reporter->assert(
    $afterLogout->statusCode === 302 || str_contains($afterLogout->redirectUrl ?? '', '/login'),
    'Test 6b: Session is destroyed after logout, protected /quiz redirects to /login'
);

// Cleanup test users
$pdo->exec("DELETE FROM users WHERE email IN ('student1@example.com', 'student2@example.com', 'suspended@example.com')");
$pdo->exec("DELETE FROM login_attempts");

$reporter->endTier();
$reporter->printFinalReport();
