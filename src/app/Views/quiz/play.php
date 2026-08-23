<?php
$quiz = $quiz ?? [
    'id' => 0,
    'title' => 'Kuis',
    'duration' => 15,
    'category' => 'MikroTik',
    'difficulty' => 'Mudah',
    'questions' => []
];
$pausedState = $pausedState ?? null;
$pausedAnswers = $pausedState['answers'] ?? [];
$durationSeconds = (int)($quiz['duration'] ?? 15) * 60;
$initialTimeLeft = (int)($pausedState['time_left'] ?? $durationSeconds);
$questions = $quiz['questions'] ?? [];
$totalQuestions = count($questions);

require_once dirname(__DIR__) . '/templates/header.php';
?>

<div class="quiz-play-page-container">

    <!-- 1. Header & Breadcrumb Bar -->
    <section class="dashboard-hero-header mb-6 pb-4">
        <div class="hero-brand-group">
            <div class="hero-title-area">
                <h1 class="hero-main-title">
                    <span><?= htmlspecialchars($quiz['title']) ?></span>
                </h1>
                <div class="flex items-center gap-2 mt-1">
                    <span class="quiz-diff-pill font-mono font-bold text-cyan-400">
                        <?= htmlspecialchars($quiz['category']) ?>
                    </span>
                    <span class="quiz-diff-pill font-mono">
                        <?= htmlspecialchars($quiz['difficulty']) ?>
                    </span>
                </div>
            </div>
        </div>
    </section>

    <!-- 2. Main 2-Column Exam Arena -->
    <div class="quiz-exam-layout">

        <!-- LEFT COLUMN: Active Question Carousel -->
        <div class="question-column-left">
            <div class="question-card-container">
                <span class="panel-crosshair corner-tl">+</span>
                <span class="panel-crosshair corner-tr">+</span>
                <span class="panel-crosshair corner-bl">+</span>
                <span class="panel-crosshair corner-br">+</span>

                <form id="quiz-form" action="<?= BASE_URL ?>/quiz/submit/<?= (int)$quiz['id'] ?>" method="POST">
                    <input type="hidden" name="csrf_token" value="<?= \App\Core\Security::generateCsrfToken() ?>">
                    <input type="hidden" name="time_left" id="time_left" value="<?= $initialTimeLeft ?>">

                    <!-- QUESTIONS CAROUSEL STACK -->
                    <div id="quiz-questions-stack">
                        <?php if (empty($questions)): ?>
                            <div class="text-center py-12 font-mono text-xs text-zinc-500">
                                Belum ada pertanyaan yang terdaftar pada kuis ini.
                            </div>
                        <?php else: ?>
                            <?php foreach ($questions as $index => $q): ?>
                                <?php
                                $qNum = $index + 1;
                                $selectedAns = $pausedAnswers[$index] ?? '';
                                $options = $q['options'] ?? [
                                    'A' => $q['option_a'] ?? '',
                                    'B' => $q['option_b'] ?? '',
                                    'C' => $q['option_c'] ?? '',
                                    'D' => $q['option_d'] ?? ''
                                ];
                                ?>
                                <div class="question-block" data-index="<?= $index ?>" style="<?= $index === 0 ? 'display: block;' : 'display: none;' ?>">
                                    <!-- Question Header Bar -->
                                    <div class="question-header-bar">
                                        <div class="flex items-center gap-2">
                                            <span class="question-count-label font-sans">
                                                Soal <?= $qNum ?> dari <?= $totalQuestions ?>
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Question Statement -->
                                    <div class="question-statement-text font-sans">
                                        <?= nl2br(htmlspecialchars($q['question'])) ?>
                                    </div>

                                    <!-- Optional Image Attachment -->
                                    <?php if (!empty($q['image_path'])): ?>
                                        <div class="question-image-frame">
                                            <img src="<?= BASE_URL ?>/<?= htmlspecialchars($q['image_path']) ?>" alt="Lampiran Gambar Soal">
                                        </div>
                                    <?php endif; ?>

                                    <!-- 4 Interactive Radio Options (A, B, C, D) -->
                                    <div class="options-stack-container" style="margin-bottom: 0;">
                                        <?php foreach (['A', 'B', 'C', 'D'] as $optKey): ?>
                                            <?php
                                            $optText = $options[$optKey] ?? '';
                                            $isChecked = (strtoupper((string)$selectedAns) === $optKey);
                                            ?>
                                            <label class="quiz-option-label <?= $isChecked ? 'is-selected' : '' ?>">
                                                <div class="option-badge font-mono">
                                                    <?= $optKey ?>
                                                </div>
                                                <input type="radio"
                                                    name="answers[<?= $index ?>]"
                                                    value="<?= $optKey ?>"
                                                    <?= $isChecked ? 'checked' : '' ?>
                                                    style="display: none;"
                                                    class="option-radio"
                                                    onchange="window.onOptionSelect(this)">
                                                <span class="option-text-content font-sans">
                                                    <?= htmlspecialchars($optText) ?>
                                                </span>
                                            </label>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>

        <!-- RIGHT COLUMN: Question Palette & Realtime Timer -->
        <div class="timer-palette-column-right space-y-5">
            <!-- Card 1: Timer & Action Box -->
            <div class="desktop-timer-card">
                <span class="panel-crosshair corner-tl">+</span>
                <span class="panel-crosshair corner-tr">+</span>
                <span class="panel-crosshair corner-bl">+</span>
                <span class="panel-crosshair corner-br">+</span>

                <div class="timer-box-header">
                    <span class="font-mono text-xs font-bold text-zinc-400 uppercase tracking-wider">Sisa Waktu Ujian</span>
                    <button type="button" class="btn-pause-trigger btn-hero-secondary font-mono text-xs px-2 py-0.5" title="Jeda & Simpan Progres" onclick="window.playPixelSound && window.playPixelSound('blip');">
                        <span>⏸ Jeda</span>
                    </button>
                </div>

                <!-- Big Monospace Timer Badge -->
                <div id="quiz-timer-desktop" class="timer-digits-display font-mono" style="margin-bottom: 0;">
                    <span class="timer-display-text">--:--</span>
                </div>
            </div>

            <!-- Card 2: Question Palette Grid -->
            <div class="palette-card-box">
                <span class="panel-crosshair corner-tl">+</span>
                <span class="panel-crosshair corner-tr">+</span>
                <span class="panel-crosshair corner-bl">+</span>
                <span class="panel-crosshair corner-br">+</span>

                <div class="flex items-center justify-between pb-2 mb-3 border-b border-zinc-800">
                    <h4 class="font-sans text-xs font-bold text-white uppercase tracking-wider m-0">
                        Palet Nomor Soal
                    </h4>
                    <span id="answered-counter-text" class="font-mono text-xs font-bold text-cyan-400">
                        0 / <?= $totalQuestions ?> Terjawab
                    </span>
                </div>

                <!-- Palette Grid -->
                <div id="quiz-page-buttons-grid" class="palette-buttons-grid">
                    <?php foreach ($questions as $index => $q): ?>
                        <?php
                        $isAnswered = isset($pausedAnswers[$index]) && $pausedAnswers[$index] !== '';
                        ?>
                        <button type="button"
                            class="palette-btn font-mono <?= $index === 0 ? 'current' : '' ?> <?= $isAnswered ? 'answered' : '' ?>"
                            data-index="<?= $index ?>"
                            onclick="window.playPixelSound && window.playPixelSound('click');">
                            <?= $index + 1 ?>
                        </button>
                    <?php endforeach; ?>
                </div>

                <!-- Palette Legend with Badges & Explicit Indicators -->
                <div class="palette-legend-row font-mono">
                    <span class="legend-badge-item">
                        <span class="legend-dot bg-white"></span>
                        <span>Terjawab</span>
                    </span>
                    <span class="legend-badge-item">
                        <span class="legend-dot bg-zinc-900 border border-zinc-700"></span>
                        <span>Belum</span>
                    </span>
                    <span class="legend-badge-item legend-active">
                        <span class="legend-dot-active"></span>
                        <span>Aktif</span>
                    </span>
                </div>

                <!-- Palette Direct Submit Button -->
                <button type="button" class="btn-hero-primary btn-open-submit-modal font-mono text-xs w-full justify-center" onclick="window.playPixelSound && window.playPixelSound('coin');">
                    <span>✓ Kumpulkan Ujian Sekarang</span>
                </button>
            </div>
        </div>

    </div>

</div>

<!-- 1. MODAL KONFIRMASI KUMPULKAN UJIAN -->
<div id="submit-confirm-modal" class="dialog-overlay" style="display: none;">
    <div class="modal-dark-card">
        <span class="panel-crosshair corner-tl">+</span>
        <span class="panel-crosshair corner-tr">+</span>
        <span class="panel-crosshair corner-bl">+</span>
        <span class="panel-crosshair corner-br">+</span>

        <h3 class="modal-title-text font-sans">
            Kumpulkan Ujian Kuis?
        </h3>
        <p class="modal-desc-text font-sans">
            Pastikan seluruh pertanyaan telah Anda teliti sebelum mengirimkan lembar ujian.
        </p>

        <!-- Status Summary Table -->
        <div class="modal-stats-table font-mono text-xs">
            <div class="flex items-center justify-between text-zinc-400">
                <span>Total Pertanyaan:</span>
                <span class="font-bold text-white"><?= $totalQuestions ?> Soal</span>
            </div>
            <div class="flex items-center justify-between text-zinc-400">
                <span>Sudah Terjawab:</span>
                <span id="modal-answered-count" class="font-bold text-cyan-400">0</span>
            </div>
            <div class="flex items-center justify-between text-zinc-400">
                <span>Belum Terjawab:</span>
                <span id="modal-unanswered-count" class="font-bold text-red-400">0</span>
            </div>
        </div>

        <div class="flex items-center justify-end gap-2.5">
            <button type="button" id="btn-cancel-submit-modal" class="btn-hero-secondary font-mono text-xs" onclick="window.playPixelSound && window.playPixelSound('blip');">
                Periksa Kembali
            </button>
            <button type="button" id="btn-final-submit" class="btn-hero-primary font-mono text-xs" onclick="window.playPixelSound && window.playPixelSound('badge');">
                Ya, Kumpulkan Ujian
            </button>
        </div>
    </div>
</div>

<!-- 2. MODAL JEDA (PAUSE & RESUME) -->
<div id="pause-dialog" class="dialog-overlay" style="display: none;">
    <div class="modal-dark-card">
        <span class="panel-crosshair corner-tl">+</span>
        <span class="panel-crosshair corner-tr">+</span>
        <span class="panel-crosshair corner-bl">+</span>
        <span class="panel-crosshair corner-br">+</span>

        <h3 class="modal-title-text font-sans">
            Jeda Pengerjaan Ujian?
        </h3>
        <p class="modal-desc-text font-sans">
            Progres jawaban dan sisa waktu pengerjaan Anda akan disimpan di database. Anda dapat melanjutkannya kapan saja melalui katalog kuis.
        </p>
        <div class="flex items-center justify-end gap-2.5">
            <button type="button" id="btn-cancel-pause" class="btn-hero-secondary font-mono text-xs" onclick="window.playPixelSound && window.playPixelSound('blip');">
                Lanjutkan Ujian
            </button>
            <button type="button" id="btn-confirm-pause" class="btn-hero-primary font-mono text-xs" onclick="window.playPixelSound && window.playPixelSound('click');">
                Ya, Simpan & Jeda
            </button>
        </div>
    </div>
</div>

<!-- Quiz Player Configuration & Execution Scripts -->
<script>
    window.BASE_URL = "<?= BASE_URL ?>";
    window.QUIZ_PLAYER_CONFIG = {
        quizId: <?= (int)$quiz['id'] ?>,
        userId: <?= (int)($_SESSION['user']['id'] ?? 0) ?>,
        durationSeconds: <?= (int)$durationSeconds ?>,
        timeLeft: <?= (int)$initialTimeLeft ?>,
        isResumed: <?= !empty($pausedState) ? 'true' : 'false' ?>
    };

    window.onOptionSelect = function(radio) {
        const block = radio.closest('.question-block');
        if (!block) return;
        const qIndex = parseInt(block.getAttribute('data-index'), 10);

        // Reset all labels in current question block
        const labels = block.querySelectorAll('.quiz-option-label');
        labels.forEach(l => {
            l.classList.remove('is-selected');
        });

        // Add selected class to chosen label
        const parent = radio.closest('.quiz-option-label');
        if (parent) {
            parent.classList.add('is-selected');
        }

        // Sound trigger
        if (window.playPixelSound) {
            window.playPixelSound('click');
        }

        // Update palette button status
        const btnPage = document.querySelector(`.palette-btn[data-index="${qIndex}"]`);
        if (btnPage) {
            btnPage.classList.add('answered');
        }

        if (window.quizPlayer && window.quizPlayer.updateAnsweredCounter) {
            window.quizPlayer.updateAnsweredCounter();
        }
    };
</script>
<script src="<?= function_exists('assetUrl') ? assetUrl('/js/quiz-play.js') : (BASE_URL . '/js/quiz-play.js') ?>"></script>

<?php require_once dirname(__DIR__) . '/templates/footer.php'; ?>