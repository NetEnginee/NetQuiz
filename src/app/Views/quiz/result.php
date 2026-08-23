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

<div class="quiz-result-wrapper">
    <div class="flex justify-center mb-6">
        <?= renderBreadcrumb([
            ['label' => 'Student', 'url' => BASE_URL . '/'],
            ['label' => 'Kuis', 'url' => BASE_URL . '/quiz'],
            ['label' => $quiz['title'] ?? 'Ujian'],
            ['label' => 'Hasil Skor']
        ]) ?>
    </div>

    <!-- Result Card -->
    <div class="quiz-result-card">
        <span class="panel-crosshair corner-tl">+</span>
        <span class="panel-crosshair corner-tr">+</span>
        <span class="panel-crosshair corner-bl">+</span>
        <span class="panel-crosshair corner-br">+</span>

        <!-- Passing Status Badge -->
        <div class="mb-4">
            <?php if ($isPassed): ?>
                <span class="quiz-status-tag status-finished font-mono text-xs px-3 py-1">
                    <svg class="w-3.5 h-3.5 pixelated" viewBox="0 0 16 16"><use href="#pixel-sparkle"></use></svg>
                    <span>Kompeten (Lulus Passing Grade)</span>
                </span>
            <?php else: ?>
                <span class="quiz-status-tag font-mono text-xs px-3 py-1" style="background-color: rgba(239, 68, 68, 0.12); border: 1px solid rgba(239, 68, 68, 0.3); color: #f87171;">
                    <span>Perlu Evaluasi Ulang (&lt; 70%)</span>
                </span>
            <?php endif; ?>
        </div>

        <!-- Score Badge Circle -->
        <div class="result-score-circle <?= $isPassed ? 'passed' : 'failed' ?>">
            <span class="result-score-number font-mono"><?= (int)$score ?></span>
            <span class="result-score-label font-mono">POIN</span>
        </div>

        <h2 class="text-xl font-bold text-white mb-1 tracking-tight font-sans">
            <?= $isPassed ? 'Simulasi Ujian Selesai' : 'Evaluasi Simulasi Ujian Selesai' ?><span class="text-cyan-400">_</span>
        </h2>
        <p class="font-mono text-xs text-zinc-400 mb-6 max-w-md mx-auto leading-relaxed">
            <?= $isPassed 
                ? 'Selamat! Anda telah menguasai kompetensi materi konfigurasi MikroTik RouterOS ini.' 
                : 'Hasil ujian Anda telah dicatat ke database. Silakan pelajari kembali modul pembahasan untuk memperdalam materi.' ?>
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
                    <svg class="w-4 h-4 pixelated" viewBox="0 0 16 16"><use href="#pixel-coin"></use></svg>
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
        <div class="flex items-center justify-center gap-3 flex-wrap">
            <a href="<?= BASE_URL ?>/quiz" 
               class="btn-hero-secondary font-mono text-xs"
               onclick="window.playPixelSound && window.playPixelSound('click');">
                <span>← Kembali ke Kuis</span>
            </a>
            <?php if (!empty($quiz['id'])): ?>
                <a href="<?= BASE_URL ?>/quiz/play/<?= (int)$quiz['id'] ?>" 
                   class="btn-hero-secondary font-mono text-xs"
                   onclick="window.playPixelSound && window.playPixelSound('click');">
                    <span>🔄 Ulangi Kuis</span>
                </a>
                <a href="<?= BASE_URL ?>/quiz/review/<?= (int)$quiz['id'] ?>" 
                   class="btn-hero-primary font-mono text-xs"
                   onclick="window.playPixelSound && window.playPixelSound('coin');">
                    <span>💡 Review Pembahasan →</span>
                </a>
            <?php endif; ?>
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
