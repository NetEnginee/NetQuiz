<?php require_once dirname(__DIR__) . '/templates/header.php'; ?>

<!-- Custom Styles for Quiz -->
<link rel="stylesheet" href="<?= BASE_URL ?>/css/quiz.css?v=<?= time() ?>">

<div class="quiz-container">
    <!-- Breadcrumb Navigation -->
    <nav class="breadcrumb"
        style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 2rem; font-size: 0.85rem; font-weight: 500; color: #64748b; font-family: 'Plus Jakarta Sans', sans-serif;">
        <span style="color: #64748b;">Dashboard</span>
        <span style="color: #cbd5e1;">/</span>
        <span style="color: #64748b;">Quiz</span>
        <span style="color: #cbd5e1;">/</span>
        <span style="color: #0f172a; font-weight: 600;">Hasil Skor</span>
    </nav>

    <!-- Quiz Result Score Card -->
    <div class="result-card">
        <div class="result-score-circle">
            <span class="result-score-value"><?= (int) $score ?></span>
            <span class="result-score-label">Skor Akhir</span>
        </div>

        <h2
            style="font-size: 1.35rem; font-weight: 800; color: #0f172a; margin: 0; font-family: 'Plus Jakarta Sans', sans-serif;">
            <?= $score >= 80 ? 'Luar Biasa! 🎉' : ($score >= 60 ? 'Kerja Bagus! 👍' : 'Tetap Semangat! 💪') ?>
        </h2>
        <p style="font-size: 0.9rem; color: #64748b; margin: 0; line-height: 1.5;">
            <?= $score >= 80 ? 'Anda memiliki pemahaman konfigurasi MikroTik RouterOS yang luar biasa.' : ($score >= 60 ? 'Anda memiliki dasar pemahaman yang baik, teruslah berlatih.' : 'Pelajari kembali topik ini untuk meningkatkan pemahaman Anda.') ?>
        </p>

        <!-- Stats Meta Breakdown -->
        <div class="result-meta-grid">
            <div class="result-meta-item">
                <span class="result-meta-label">Kuis</span>
                <span class="result-meta-value"><?= htmlspecialchars($quiz['title'] ?? 'RouterOS Quiz') ?></span>
            </div>
            <div class="result-meta-item">
                <span class="result-meta-label">Jawaban Benar</span>
                <span class="result-meta-value"><?= (int) $correct ?> / <?= (int) $total ?></span>
            </div>
        </div>

        <!-- Action Links -->
        <div class="result-actions">
            <a href="<?= BASE_URL ?>/quiz" class="btn-secondary" style="text-decoration: none;">
                Kembali ke Quiz
            </a>
            <?php if ($quiz): ?>
                <?php 
                $reviewUrl = method_exists('\App\Core\Security', 'encryptUrlId') 
                    ? BASE_URL . '/quiz/review/' . \App\Core\Security::encryptUrlId($quiz['id']) 
                    : BASE_URL . '/quiz/review/' . $quiz['id'];
                ?>
                <a href="<?= $reviewUrl ?>" class="btn-primary" style="text-decoration: none;">
                    Review Jawaban
                </a>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/templates/footer.php'; ?>