<?php
$score = isset($score) ? (int) $score : 0;
$correct = isset($correct) ? (int) $correct : 0;
$total = isset($total) ? (int) $total : 0;
$quiz = $quiz ?? [
    'id' => 0,
    'title' => 'NetQuiz',
    'category' => 'MikroTik'
];

$isPassed = $score >= 70;

require_once dirname(__DIR__) . '/templates/header.php';
?>

<div class="quiz-result-page-container">
    <div class="quiz-result-wrapper">
        <!-- Result Card -->
        <div class="quiz-result-card">
            <span class="panel-crosshair corner-tl">+</span>
            <span class="panel-crosshair corner-tr">+</span>
            <span class="panel-crosshair corner-bl">+</span>
            <span class="panel-crosshair corner-br">+</span>

            <!-- Passing Status Badge -->


            <!-- Score Badge Circle -->
            <div class="result-score-circle <?= $isPassed ? 'passed' : 'failed' ?>">
                <span class="result-score-number font-mono"><?= (int)$score ?></span>
                <span class="result-score-label font-mono">POIN</span>
            </div>

            <h2 class="result-title-text font-sans">
                <?= $isPassed ? 'Lulus' : 'Tidak Lulus' ?><span class="text-cyan-400"></span>
            </h2>
            <p class="result-desc-text font-mono">
                <?= $isPassed
                    ? 'Selamat! Anda telah menguasai materi saat ini.'
                    : 'Silakan pelajari kembali untuk memperdalam materi.' ?>
            </p>

            <!-- Stats Breakdown Grid -->
            <div class="result-stats-breakdown-grid">
                <div class="result-stat-box">
                    <span class="result-stat-label font-mono">Topik Ujian</span>
                    <span class="result-stat-val truncate" title="<?= htmlspecialchars($quiz['title'] ?? 'Kuis') ?>">
                        <?= htmlspecialchars($quiz['title'] ?? 'Kuis') ?>
                    </span>
                </div>
                <div class="result-stat-box">
                    <span class="result-stat-label font-mono">Akurasi Jawaban</span>
                    <span class="result-stat-val font-mono text-cyan-400">
                        <?= (int)$correct ?> / <?= (int)$total ?> Soal (<?= (int)$score ?>%)
                    </span>
                </div>
                <div class="result-stat-box">
                    <span class="result-stat-label font-mono">Perolehan Poin</span>
                    <span class="result-stat-val font-mono text-amber-400 flex items-center gap-1">
                        <span>+<?= (int)$score ?> pts</span>
                    </span>
                </div>
                <div class="result-stat-box">
                    <span class="result-stat-label font-mono">Kategori Modul</span>
                    <span class="result-stat-val font-mono text-zinc-300">
                        <?= htmlspecialchars($quiz['category'] ?? 'MikroTik') ?>
                    </span>
                </div>
            </div>

            <!-- Action CTA Buttons -->
            <div class="result-actions-group">
                <a href="<?= BASE_URL ?>/quiz"
                    class="btn-hero-secondary font-mono"
                    onclick="window.playPixelSound && window.playPixelSound('click');">
                    <span>← Kembali</span>
                </a>
                <?php if (!empty($quiz['id'])): ?>
                    <a href="<?= BASE_URL ?>/quiz/review/<?= (int)$quiz['id'] ?>"
                        class="btn-hero-primary font-mono"
                        onclick="window.playPixelSound && window.playPixelSound('coin');">
                        <span>Review →</span>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        if (window.playPixelSound) {
            window.playPixelSound('<?= $isPassed ? "badge" : "coin" ?>');
        }
    });
</script>

<?php require_once dirname(__DIR__) . '/templates/footer.php'; ?>