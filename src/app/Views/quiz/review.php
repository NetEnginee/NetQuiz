<?php
$quiz = $quiz ?? [
    'id' => 0,
    'title' => 'Kuis',
    'category' => 'MikroTik',
    'questions' => []
];
$userAnswers = $userAnswers ?? [];
$score = isset($score) ? (int) $score : 0;
$questions = $quiz['questions'] ?? [];
$totalQuestions = count($questions);

$correctCount = 0;
$wrongCount = 0;

foreach ($questions as $idx => $q) {
    $uAns = strtoupper((string)($userAnswers[$idx] ?? ''));
    $cAns = strtoupper((string)($q['correct'] ?? ''));
    if ($uAns !== '' && $uAns === $cAns) {
        $correctCount++;
    } else {
        $wrongCount++;
    }
}

require_once dirname(__DIR__) . '/templates/header.php';
?>

<div class="quiz-review-page-container">

    <!-- 1. Clean Top Header (Without Breadcrumb & Router Icon) -->
    <section class="dashboard-hero-header mb-6 pb-4">
        <div class="hero-title-area">
            <h1 class="hero-main-title">
                <span>Review: <?= htmlspecialchars($quiz['title']) ?></span>
            </h1>
            <div class="flex items-center gap-2 mt-1.5">
                <span class="quiz-diff-pill font-mono font-bold text-cyan-400">
                    <?= htmlspecialchars($quiz['category']) ?>
                </span>
                <span class="quiz-diff-pill font-mono font-bold <?= $score >= 70 ? 'text-emerald-400' : 'text-red-400' ?>">
                    Skor Akhir: <?= (int)$score ?>%
                </span>
            </div>
        </div>

        <div class="hero-action-bar">
            <a href="<?= BASE_URL ?>/quiz" class="btn-hero-secondary font-mono text-xs" onclick="window.playPixelSound && window.playPixelSound('click');">
                <span>← Kembali ke Kuis</span>
            </a>
            <a href="<?= BASE_URL ?>/quiz/play/<?= (int)$quiz['id'] ?>" class="btn-hero-primary font-mono text-xs" onclick="window.playPixelSound && window.playPixelSound('click');">
                <span>Ulangi Kuis</span>
            </a>
        </div>
    </section>

    <!-- 2. Main 2-Column Review Layout -->
    <div class="quiz-review-layout">

        <!-- LEFT COLUMN: Question & Evaluated Options -->
        <div class="review-column-left">
            <!-- Review Question Card -->
            <div class="question-card-container">
                <span class="panel-crosshair corner-tl">+</span>
                <span class="panel-crosshair corner-tr">+</span>
                <span class="panel-crosshair corner-bl">+</span>
                <span class="panel-crosshair corner-br">+</span>

                <div id="quiz-review-stack">
                    <?php foreach ($questions as $index => $q): ?>
                        <?php
                        $qNum = $index + 1;
                        $userAns = strtoupper((string)($userAnswers[$index] ?? ''));
                        $correctAns = strtoupper((string)($q['correct'] ?? ''));
                        $isCorrect = ($userAns !== '' && $userAns === $correctAns);
                        $options = $q['options'] ?? [
                            'A' => $q['option_a'] ?? '',
                            'B' => $q['option_b'] ?? '',
                            'C' => $q['option_c'] ?? '',
                            'D' => $q['option_d'] ?? ''
                        ];
                        ?>
                        <div class="review-question-block"
                            data-index="<?= $index ?>"
                            data-correct="<?= $isCorrect ? '1' : '0' ?>"
                            style="<?= $index === 0 ? 'display: block;' : 'display: none;' ?>">

                            <!-- Header Status -->
                            <div class="question-header-bar">
                                <span class="question-count-label font-sans">
                                    Pertanyaan <?= $qNum ?> dari <?= $totalQuestions ?>
                                </span>
                                <?php if ($isCorrect): ?>
                                    <span class="quiz-status-tag status-finished font-mono text-xs">
                                        <svg class="w-3.5 h-3.5 pixelated" viewBox="0 0 16 16">
                                            <use href="#pixel-sparkle"></use>
                                        </svg>
                                        <span>Jawaban Benar</span>
                                    </span>
                                <?php else: ?>
                                    <span class="quiz-status-tag font-mono text-xs" style="background-color: rgba(239, 68, 68, 0.12); border: 1px solid rgba(239, 68, 68, 0.3); color: #f87171;">
                                        <span>✕ Jawaban Salah</span>
                                    </span>
                                <?php endif; ?>
                            </div>

                            <!-- Question Text -->
                            <div class="question-statement-text font-sans">
                                <?= nl2br(htmlspecialchars($q['question'])) ?>
                            </div>

                            <!-- Question Image Attachment -->
                            <?php if (!empty($q['image_path'])): ?>
                                <div class="question-image-frame">
                                    <img src="<?= BASE_URL ?>/<?= htmlspecialchars($q['image_path']) ?>" alt="Lampiran Gambar Soal">
                                </div>
                            <?php endif; ?>

                            <!-- 4 Evaluated Options -->
                            <div class="options-stack-container" style="margin-bottom: 0;">
                                <?php foreach (['A', 'B', 'C', 'D'] as $optKey): ?>
                                    <?php
                                    $optText = $options[$optKey] ?? '';
                                    $isThisCorrect = ($optKey === $correctAns);
                                    $isThisUserChoice = ($optKey === $userAns);

                                    $optClass = '';
                                    $badgeStyle = '';
                                    $tagLabel = '';

                                    if ($isThisCorrect) {
                                        $optClass = 'opt-correct';
                                        $badgeStyle = 'background-color: #10b981; border-color: #10b981; color: #000000; font-weight: 800;';
                                        if ($isThisUserChoice) {
                                            $tagLabel = '<span class="quiz-status-tag status-finished font-mono text-[11px]"><svg class="w-3 h-3 pixelated" viewBox="0 0 16 16"><use href="#pixel-sparkle"></use></svg> Jawaban Anda (Benar)</span>';
                                        } else {
                                            $tagLabel = '<span class="quiz-status-tag status-finished font-mono text-[11px]"><svg class="w-3 h-3 pixelated" viewBox="0 0 16 16"><use href="#pixel-sparkle"></use></svg> Kunci Jawaban</span>';
                                        }
                                    } elseif ($isThisUserChoice && !$isCorrect) {
                                        $optClass = 'opt-wrong';
                                        $badgeStyle = 'background-color: #ef4444; border-color: #ef4444; color: #ffffff; font-weight: 800;';
                                        $tagLabel = '<span class="quiz-status-tag font-mono text-[11px]" style="background-color: rgba(239, 68, 68, 0.15); border: 1px solid rgba(239, 68, 68, 0.4); color: #f87171;">✕ Jawaban Anda (Salah)</span>';
                                    }
                                    ?>
                                    <div class="review-evaluated-option <?= $optClass ?>">
                                        <div class="flex items-center gap-3 flex-1 min-w-0">
                                            <div class="option-badge font-mono" style="<?= $badgeStyle ?>">
                                                <?= $optKey ?>
                                            </div>
                                            <span class="option-text-content font-sans">
                                                <?= htmlspecialchars($optText) ?>
                                            </span>
                                        </div>
                                        <?php if (!empty($tagLabel)): ?>
                                            <div class="flex-shrink-0">
                                                <?= $tagLabel ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                        </div>
                    <?php endforeach; ?>
                </div>

            </div>
        </div>

        <!-- RIGHT COLUMN: Palette & Technical Explanation (Without Ringkasan Hasil) -->
        <div class="review-column-right space-y-5">
            <!-- Card 1: Question Review Palette Grid -->
            <div class="palette-card-box">
                <span class="panel-crosshair corner-tl">+</span>
                <span class="panel-crosshair corner-tr">+</span>
                <span class="panel-crosshair corner-bl">+</span>
                <span class="panel-crosshair corner-br">+</span>

                <div class="flex items-center justify-between pb-2 mb-3 border-b border-zinc-800">
                    <h4 class="font-sans text-xs font-bold text-white uppercase tracking-wider m-0">
                        Palet Soal Evaluasi
                    </h4>
                </div>

                <!-- Palette Grid -->
                <div id="review-palette-grid" class="palette-buttons-grid">
                    <?php foreach ($questions as $index => $q): ?>
                        <?php
                        $userAns = strtoupper((string)($userAnswers[$index] ?? ''));
                        $correctAns = strtoupper((string)($q['correct'] ?? ''));
                        $isCorrect = ($userAns !== '' && $userAns === $correctAns);
                        ?>
                        <button type="button"
                            class="review-palette-btn font-mono <?= $index === 0 ? 'current' : '' ?> <?= $isCorrect ? 'is-correct' : 'is-wrong' ?>"
                            data-index="<?= $index ?>"
                            data-correct="<?= $isCorrect ? '1' : '0' ?>"
                            onclick="window.playPixelSound && window.playPixelSound('click');">
                            <?= $index + 1 ?>
                        </button>
                    <?php endforeach; ?>
                </div>

                <!-- Palette Legend with Counts & Indicators -->
                <div class="palette-legend-row font-mono">
                    <span class="legend-badge-item legend-correct">
                        <span class="legend-dot bg-emerald-500"></span>
                        <span>Benar: <strong class="text-emerald-400 font-bold"><?= $correctCount ?></strong></span>
                    </span>
                    <span class="legend-badge-item legend-wrong">
                        <span class="legend-dot bg-red-500"></span>
                        <span>Salah: <strong class="text-red-400 font-bold"><?= $wrongCount ?></strong></span>
                    </span>
                    <span class="legend-badge-item legend-active">
                        <span class="legend-dot-active"></span>
                        <span>Aktif</span>
                    </span>
                </div>
            </div>

            <!-- Card 2: Technical RouterOS Explanation Sidebar Terminal -->
            <div class="review-explanation-sidebar-card">
                <span class="panel-crosshair corner-tl">+</span>
                <span class="panel-crosshair corner-tr">+</span>
                <span class="panel-crosshair corner-bl">+</span>
                <span class="panel-crosshair corner-br">+</span>

                <div class="explanation-card-header flex items-center justify-between pb-2 mb-3 border-b border-zinc-800">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 pixelated text-cyan-400" viewBox="0 0 16 16">
                            <use href="#pixel-robot"></use>
                        </svg>
                        <h4 class="font-sans text-xs font-bold text-cyan-400 uppercase tracking-wider m-0">
                            Pembahasan Teknis RouterOS
                        </h4>
                    </div>
                    <span id="explanation-q-badge" class="font-mono text-[11px] text-zinc-400 font-bold">
                        Soal #1
                    </span>
                </div>

                <!-- Dynamic Explanation Stack -->
                <div id="review-explanation-stack">
                    <?php foreach ($questions as $index => $q): ?>
                        <div class="review-explanation-item"
                            data-index="<?= $index ?>"
                            style="<?= $index === 0 ? 'display: block;' : 'display: none;' ?>">
                            <div class="font-mono text-xs text-zinc-300 leading-relaxed">
                                <?= !empty($q['explanation']) ? nl2br(htmlspecialchars($q['explanation'])) : 'Tidak ada catatan pembahasan teknis tambahan untuk butir soal ini.' ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

        </div>

    </div>

</div>

<!-- Reviewer JS Module -->
<script src="<?= function_exists('assetUrl') ? assetUrl('/js/quiz-review.js') : (BASE_URL . '/js/quiz-review.js') ?>"></script>

<?php require_once dirname(__DIR__) . '/templates/footer.php'; ?>