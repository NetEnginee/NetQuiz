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

require_once dirname(__DIR__) . '/templates/header.php';
?>

<!-- Quiz Player Container (Max Width 860px) -->
<div style="max-width: 860px; margin: 0 auto;">
    <!-- Breadcrumb & Exam Banner Header -->
    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.5rem;">
        <div>
            <nav class="admin-breadcrumb-nav" aria-label="Breadcrumb" style="margin-bottom: 0.25rem;">
                <span class="breadcrumb-item">Katalog</span>
                <span class="breadcrumb-separator">/</span>
                <span class="breadcrumb-active"><?= htmlspecialchars($quiz['title']) ?></span>
            </nav>
            <div style="display: flex; align-items: center; gap: 0.5rem;">
                <span class="status-badge" style="background-color: #F4F4F5; font-size: 0.725rem;"><?= htmlspecialchars($quiz['category']) ?></span>
                <span class="status-badge" style="background-color: #F4F4F5; font-size: 0.725rem;"><?= htmlspecialchars($quiz['difficulty']) ?></span>
            </div>
        </div>

        <!-- Timer & Pause Control -->
        <div style="display: flex; align-items: center; gap: 0.75rem;">
            <!-- Monochrome Timer Badge -->
            <div id="quiz-timer" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.4rem 0.85rem; background-color: #18181B; color: #FFFFFF; border-radius: 6px; font-family: var(--font-mono); font-size: 0.95rem; font-weight: 700;">
                <i data-lucide="clock" style="width: 15px; height: 15px;"></i>
                <span id="timer-text">--:--</span>
            </div>

            <!-- Pause Button -->
            <button type="button" id="btn-pause-quiz" class="btn-secondary-outline" style="font-size: 0.8rem; padding: 0.4rem 0.75rem;" title="Jeda & Simpan Progres Ujian">
                <i data-lucide="pause" style="width: 13px; height: 13px;"></i>
                <span>Jeda</span>
            </button>
        </div>
    </div>

    <!-- MAIN EXAM STUDIO CARD -->
    <div class="supabase-panel-card" style="padding: 2rem;">
        <span class="corner-crosshair corner-tl">+</span>
        <span class="corner-crosshair corner-tr">+</span>
        <span class="corner-crosshair corner-bl">+</span>
        <span class="corner-crosshair corner-br">+</span>

        <form id="quiz-form" action="<?= BASE_URL ?>/quiz/submit/<?= (int)$quiz['id'] ?>" method="POST">
            <input type="hidden" name="csrf_token" value="<?= \App\Core\Security::generateCsrfToken() ?>">
            <input type="hidden" name="time_left" id="time_left" value="<?= $initialTimeLeft ?>">

            <!-- QUESTIONS CAROUSEL STACK -->
            <div id="quiz-questions-stack">
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
                    <div class="question-block <?= $index === 0 ? 'active' : '' ?>" data-index="<?= $index ?>" style="<?= $index === 0 ? 'display: block;' : 'display: none;' ?>">
                        <!-- Question Header -->
                        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.25rem; padding-bottom: 0.75rem; border-bottom: 1px solid #E5E7EB;">
                            <span style="font-size: 0.8rem; font-weight: 800; color: #71717A; text-transform: uppercase; letter-spacing: 0.05em;">
                                Pertanyaan <?= $qNum ?> dari <?= count($questions) ?>
                            </span>
                            <span class="font-mono" style="font-size: 0.75rem; color: #71717A;">Pilih 1 jawaban yang tepat</span>
                        </div>

                        <!-- Question Statement -->
                        <div style="font-size: 1.05rem; font-weight: 700; color: #18181B; margin-bottom: 1.5rem; line-height: 1.5;">
                            <?= nl2br(htmlspecialchars($q['question'])) ?>
                        </div>

                        <?php if (!empty($q['image_path'])): ?>
                            <div style="margin-bottom: 1.5rem; border: 1px solid #E5E7EB; border-radius: 6px; overflow: hidden; max-height: 300px; display: flex; align-items: center; justify-content: center; background-color: #FAFAFA;">
                                <img src="<?= BASE_URL ?>/<?= htmlspecialchars($q['image_path']) ?>" alt="Soal Gambar" style="max-width: 100%; max-height: 300px; object-fit: contain;">
                            </div>
                        <?php endif; ?>

                        <!-- 4 Interactive Options (A, B, C, D) -->
                        <div style="display: flex; flex-direction: column; gap: 0.75rem; margin-bottom: 2rem;">
                            <?php foreach (['A', 'B', 'C', 'D'] as $optKey): ?>
                                <?php
                                $optText = $options[$optKey] ?? '';
                                $isChecked = (strtoupper($selectedAns) === $optKey);
                                ?>
                                <label class="quiz-option-label" style="display: flex; align-items: center; gap: 1rem; padding: 0.85rem 1.15rem; background-color: #FAFAFA; border: 1px solid <?= $isChecked ? '#18181B' : '#E5E7EB' ?>; border-radius: 8px; cursor: pointer; transition: all 0.15s ease;" onmouseover="if(!this.querySelector('input').checked) this.style.borderColor='#A1A1AA';" onmouseout="if(!this.querySelector('input').checked) this.style.borderColor='#E5E7EB';">
                                    <div style="display: flex; align-items: center; justify-content: center; width: 26px; height: 26px; border-radius: 6px; border: 1px solid <?= $isChecked ? '#18181B' : '#D4D4D8' ?>; background-color: <?= $isChecked ? '#18181B' : '#FFFFFF' ?>; color: <?= $isChecked ? '#FFFFFF' : '#18181B' ?>; font-weight: 800; font-size: 0.8rem; font-family: var(--font-mono); flex-shrink: 0;" class="option-badge">
                                        <?= $optKey ?>
                                    </div>
                                    <input type="radio" name="answers[<?= $index ?>]" value="<?= $optKey ?>" <?= $isChecked ? 'checked' : '' ?> style="display: none;" class="option-radio" onchange="window.onOptionSelect(this)">
                                    <span style="font-size: 0.9rem; font-weight: 500; color: #18181B; line-height: 1.4; flex: 1;">
                                        <?= htmlspecialchars($optText) ?>
                                    </span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- PLAYER SLIDER NAVIGATION CONTROLS -->
            <div style="display: flex; align-items: center; justify-content: space-between; padding-top: 1.25rem; border-top: 1px solid #E5E7EB; flex-wrap: wrap; gap: 1rem;">
                <button type="button" id="btn-prev" class="btn-secondary-outline" style="font-size: 0.85rem; padding: 0.5rem 1rem;">
                    <i data-lucide="arrow-left" style="width: 14px; height: 14px;"></i>
                    <span>Sebelumnya</span>
                </button>

                <div style="display: flex; align-items: center; gap: 0.5rem;">
                    <button type="button" id="btn-next" class="btn-primary-black" style="font-size: 0.85rem; padding: 0.5rem 1.15rem;">
                        <span>Selanjutnya</span>
                        <i data-lucide="arrow-right" style="width: 14px; height: 14px;"></i>
                    </button>

                    <button type="button" id="btn-submit-quiz" class="btn-primary-black" style="font-size: 0.85rem; padding: 0.5rem 1.25rem; display: none; background-color: #18181B;">
                        <i data-lucide="check-circle" style="width: 14px; height: 14px;"></i>
                        <span>Selesaikan Ujian</span>
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- BOTTOM QUESTION NUMBERS PAGINATION GRID -->
    <div class="supabase-panel-card" style="margin-top: 1.5rem; padding: 1.25rem;">
        <span class="corner-crosshair corner-tl">+</span>
        <span class="corner-crosshair corner-tr">+</span>
        <span class="corner-crosshair corner-bl">+</span>
        <span class="corner-crosshair corner-br">+</span>

        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.85rem;">
            <span style="font-size: 0.775rem; font-weight: 700; color: #71717A; text-transform: uppercase;">Navigasi Nomor Soal</span>
            <div style="display: flex; align-items: center; gap: 1rem; font-size: 0.75rem; color: #71717A;">
                <span style="display: inline-flex; align-items: center; gap: 0.35rem;">
                    <span style="width: 10px; height: 10px; border-radius: 2px; background-color: #18181B; display: inline-block;"></span>
                    <span>Terjawab</span>
                </span>
                <span style="display: inline-flex; align-items: center; gap: 0.35rem;">
                    <span style="width: 10px; height: 10px; border-radius: 2px; background-color: #F4F4F5; border: 1px solid #D4D4D8; display: inline-block;"></span>
                    <span>Belum</span>
                </span>
            </div>
        </div>

        <div id="quiz-page-buttons-grid" style="display: flex; flex-wrap: wrap; gap: 0.5rem;">
            <?php foreach ($questions as $index => $q): ?>
                <?php
                $isAnswered = isset($pausedAnswers[$index]) && $pausedAnswers[$index] !== '';
                ?>
                <button type="button" class="page-number font-mono <?= $index === 0 ? 'current' : '' ?> <?= $isAnswered ? 'answered' : '' ?>" data-index="<?= $index ?>" style="width: 36px; height: 36px; border-radius: 6px; border: 1px solid <?= $index === 0 ? '#18181B' : ($isAnswered ? '#18181B' : '#E5E7EB') ?>; background-color: <?= $isAnswered ? '#18181B' : '#FFFFFF' ?>; color: <?= $isAnswered ? '#FFFFFF' : '#18181B' ?>; font-weight: 700; font-size: 0.8rem; cursor: pointer; transition: all 0.15s ease;">
                    <?= $index + 1 ?>
                </button>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Selesai / Jeda Ujian -->
<div id="pause-dialog" class="dialog-overlay" style="display: none; position: fixed; inset: 0; background-color: rgba(0,0,0,0.5); backdrop-filter: blur(4px); z-index: 2000; align-items: center; justify-content: center;">
    <div class="supabase-panel-card" style="max-width: 440px; width: 90%; padding: 1.75rem; background: #FFFFFF;">
        <span class="corner-crosshair corner-tl">+</span>
        <span class="corner-crosshair corner-tr">+</span>
        <span class="corner-crosshair corner-bl">+</span>
        <span class="corner-crosshair corner-br">+</span>
        <h3 style="font-family: var(--font-heading); font-size: 1.1rem; font-weight: 800; color: #18181B; margin: 0 0 0.4rem 0;">Jeda Pengerjaan Kuis?</h3>
        <p style="font-size: 0.85rem; color: #71717A; margin: 0 0 1.5rem 0; line-height: 1.45;">
            Progres jawaban dan sisa waktu pengerjaan Anda akan disimpan di sistem sehingga Anda dapat melanjutkannya nanti.
        </p>
        <div style="display: flex; align-items: center; justify-content: flex-end; gap: 0.5rem;">
            <button type="button" id="btn-cancel-pause" class="btn-secondary-outline" style="font-size: 0.85rem; padding: 0.45rem 0.85rem;">Kembali Ujian</button>
            <button type="button" id="btn-confirm-pause" class="btn-primary-black" style="font-size: 0.85rem; padding: 0.45rem 1rem;">Ya, Simpan & Jeda</button>
        </div>
    </div>
</div>

<!-- Quiz Player JS State Machine -->
<script src="<?= BASE_URL ?>/js/quiz-play.js?v=<?= time() ?>"></script>
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
        const labels = block.querySelectorAll('.quiz-option-label');
        labels.forEach(l => {
            l.style.borderColor = '#E5E7EB';
            const b = l.querySelector('.option-badge');
            if (b) {
                b.style.backgroundColor = '#FFFFFF';
                b.style.borderColor = '#D4D4D8';
                b.style.color = '#18181B';
            }
        });

        const parent = radio.closest('.quiz-option-label');
        if (parent) {
            parent.style.borderColor = '#18181B';
            const b = parent.querySelector('.option-badge');
            if (b) {
                b.style.backgroundColor = '#18181B';
                b.style.borderColor = '#18181B';
                b.style.color = '#FFFFFF';
            }
        }

        const btnPage = document.querySelector(`.page-number[data-index="${qIndex}"]`);
        if (btnPage) {
            btnPage.style.backgroundColor = '#18181B';
            btnPage.style.borderColor = '#18181B';
            btnPage.style.color = '#FFFFFF';
            btnPage.classList.add('answered');
        }
    };
</script>

<?php require_once dirname(__DIR__) . '/templates/footer.php'; ?>
