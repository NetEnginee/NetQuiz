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

<!-- Dynamic Breadcrumb & Quiz Top Info -->
<div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.5rem;">
    <div>
        <?= renderBreadcrumb([
            ['label' => 'Siswa', 'url' => BASE_URL . '/'],
            ['label' => 'Kuis', 'url' => BASE_URL . '/quiz'],
            ['label' => $quiz['title']]
        ]) ?>
        <div style="display: flex; align-items: center; gap: 0.5rem; margin-top: 0.25rem;">
            <span class="status-badge" style="background-color: #F4F4F5; font-size: 0.725rem; font-weight: 700;"><?= htmlspecialchars($quiz['category']) ?></span>
            <span class="status-badge" style="background-color: #F4F4F5; font-size: 0.725rem;"><?= htmlspecialchars($quiz['difficulty']) ?></span>
        </div>
    </div>

    <!-- Quick Status Badges for Mobile -->
    <div class="mobile-timer-bar" style="display: none; align-items: center; gap: 0.5rem;">
        <div class="quiz-timer-pill" style="display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.35rem 0.75rem; background-color: #18181B; color: #FFFFFF; border-radius: 6px; font-family: var(--font-mono); font-size: 0.85rem; font-weight: 700;">
            <i data-lucide="clock" style="width: 14px; height: 14px;"></i>
            <span class="timer-display-text">--:--</span>
        </div>
        <button type="button" class="btn-pause-trigger btn-secondary-outline" style="font-size: 0.775rem; padding: 0.35rem 0.7rem;">
            <i data-lucide="pause" style="width: 12px; height: 12px;"></i>
            <span>Jeda</span>
        </button>
    </div>
</div>

<style>
    .quiz-exam-layout {
        display: grid;
        grid-template-columns: 1.7fr 1fr;
        gap: 1.5rem;
        align-items: start;
    }
    @media (max-width: 900px) {
        .quiz-exam-layout {
            grid-template-columns: 1fr;
        }
        .mobile-timer-bar {
            display: inline-flex !important;
        }
        .desktop-timer-card {
            display: none !important;
        }
    }
    .palette-btn {
        width: 38px;
        height: 38px;
        border-radius: 6px;
        border: 1px solid #E5E7EB;
        background-color: #FFFFFF;
        color: #18181B;
        font-weight: 700;
        font-size: 0.825rem;
        cursor: pointer;
        transition: all 0.15s ease;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .palette-btn:hover {
        border-color: #18181B;
    }
    .palette-btn.current {
        outline: 2px solid #18181B;
        outline-offset: 2px;
    }
    .palette-btn.answered {
        background-color: #18181B !important;
        border-color: #18181B !important;
        color: #FFFFFF !important;
    }
</style>

<!-- MAIN 2-COLUMN EXAM LAYOUT -->
<div class="quiz-exam-layout">
    <!-- LEFT COLUMN: Active Question Carousel -->
    <div>
        <div class="supabase-panel-card" style="padding: 1.75rem;">
            <span class="corner-crosshair corner-tl">+</span>
            <span class="corner-crosshair corner-tr">+</span>
            <span class="corner-crosshair corner-bl">+</span>
            <span class="corner-crosshair corner-br">+</span>

            <form id="quiz-form" action="<?= BASE_URL ?>/quiz/submit/<?= (int)$quiz['id'] ?>" method="POST">
                <input type="hidden" name="csrf_token" value="<?= \App\Core\Security::generateCsrfToken() ?>">
                <input type="hidden" name="time_left" id="time_left" value="<?= $initialTimeLeft ?>">

                <!-- QUESTIONS CAROUSEL STACK -->
                <div id="quiz-questions-stack">
                    <?php if (empty($questions)): ?>
                        <div style="padding: 3rem 1rem; text-align: center; color: #71717A;">
                            Belum ada pertanyaan pada kuis ini.
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
                                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.25rem; padding-bottom: 0.75rem; border-bottom: 1px solid #E5E7EB;">
                                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                                        <span style="font-family: var(--font-heading); font-size: 0.85rem; font-weight: 800; color: #18181B; text-transform: uppercase; letter-spacing: 0.05em;">
                                            Soal <?= $qNum ?> dari <?= $totalQuestions ?>
                                        </span>
                                    </div>
                                    <span class="font-mono" style="font-size: 0.75rem; color: #71717A;">Pilih 1 Opsi</span>
                                </div>

                                <!-- Question Statement -->
                                <div style="font-size: 1.05rem; font-weight: 700; color: #18181B; margin-bottom: 1.5rem; line-height: 1.55;">
                                    <?= nl2br(htmlspecialchars($q['question'])) ?>
                                </div>

                                <!-- Optional Image Attachment -->
                                <?php if (!empty($q['image_path'])): ?>
                                    <div style="margin-bottom: 1.5rem; border: 1px solid #E5E7EB; border-radius: 8px; overflow: hidden; max-height: 300px; display: flex; align-items: center; justify-content: center; background-color: #FAFAFA;">
                                        <img src="<?= BASE_URL ?>/<?= htmlspecialchars($q['image_path']) ?>" alt="Lampiran Gambar Soal" style="max-width: 100%; max-height: 300px; object-fit: contain;">
                                    </div>
                                <?php endif; ?>

                                <!-- 4 Interactive Radio Options (A, B, C, D) -->
                                <div style="display: flex; flex-direction: column; gap: 0.75rem; margin-bottom: 2rem;">
                                    <?php foreach (['A', 'B', 'C', 'D'] as $optKey): ?>
                                        <?php
                                        $optText = $options[$optKey] ?? '';
                                        $isChecked = (strtoupper((string)$selectedAns) === $optKey);
                                        ?>
                                        <label class="quiz-option-label" style="display: flex; align-items: center; gap: 1rem; padding: 0.85rem 1.15rem; background-color: <?= $isChecked ? '#FFFFFF' : '#FAFAFA' ?>; border: 1px solid <?= $isChecked ? '#18181B' : '#E5E7EB' ?>; border-radius: 8px; cursor: pointer; transition: all 0.15s ease;" onmouseover="if(!this.querySelector('input').checked) this.style.borderColor='#A1A1AA';" onmouseout="if(!this.querySelector('input').checked) this.style.borderColor='#E5E7EB';">
                                            <div class="option-badge font-mono" style="display: flex; align-items: center; justify-content: center; width: 28px; height: 28px; border-radius: 6px; border: 1px solid <?= $isChecked ? '#18181B' : '#D4D4D8' ?>; background-color: <?= $isChecked ? '#18181B' : '#FFFFFF' ?>; color: <?= $isChecked ? '#FFFFFF' : '#18181B' ?>; font-weight: 800; font-size: 0.825rem; flex-shrink: 0;">
                                                <?= $optKey ?>
                                            </div>
                                            <input type="radio" name="answers[<?= $index ?>]" value="<?= $optKey ?>" <?= $isChecked ? 'checked' : '' ?> style="display: none;" class="option-radio" onchange="window.onOptionSelect(this)">
                                            <span style="font-size: 0.9rem; font-weight: 500; color: #18181B; line-height: 1.45; flex: 1;">
                                                <?= htmlspecialchars($optText) ?>
                                            </span>
                                        </label>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <!-- CAROUSEL NAVIGATION FOOTER -->
                <div style="display: flex; align-items: center; justify-content: space-between; padding-top: 1.25rem; border-top: 1px solid #E5E7EB; flex-wrap: wrap; gap: 0.75rem;">
                    <button type="button" id="btn-prev" class="btn-secondary-outline" style="font-size: 0.825rem; padding: 0.45rem 0.95rem;">
                        <i data-lucide="arrow-left" style="width: 14px; height: 14px;"></i>
                        <span>Sebelumnya</span>
                    </button>

                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <button type="button" id="btn-next" class="btn-primary-black" style="font-size: 0.825rem; padding: 0.45rem 1rem;">
                            <span>Selanjutnya</span>
                            <i data-lucide="arrow-right" style="width: 14px; height: 14px;"></i>
                        </button>

                        <button type="button" id="btn-submit-carousel" class="btn-primary-black btn-open-submit-modal" style="font-size: 0.825rem; padding: 0.45rem 1.15rem; display: none;">
                            <i data-lucide="check-circle" style="width: 14px; height: 14px;"></i>
                            <span>Kumpulkan Ujian</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- RIGHT COLUMN: Question Palette & Realtime Timer -->
    <div style="display: flex; flex-direction: column; gap: 1.25rem;">
        <!-- Card 1: Timer & Action Box -->
        <div class="supabase-panel-card desktop-timer-card" style="padding: 1.25rem;">
            <span class="corner-crosshair corner-tl">+</span>
            <span class="corner-crosshair corner-tr">+</span>
            <span class="corner-crosshair corner-bl">+</span>
            <span class="corner-crosshair corner-br">+</span>

            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.75rem;">
                <span style="font-size: 0.75rem; font-weight: 700; color: #71717A; text-transform: uppercase; letter-spacing: 0.05em;">Sisa Waktu Ujian</span>
                <button type="button" class="btn-pause-trigger btn-secondary-outline" style="font-size: 0.75rem; padding: 0.25rem 0.65rem;" title="Jeda & Simpan Progres">
                    <i data-lucide="pause" style="width: 12px; height: 12px;"></i>
                    <span>Jeda</span>
                </button>
            </div>

            <!-- Big Monospace Timer Badge -->
            <div id="quiz-timer-desktop" style="padding: 0.75rem; background-color: #18181B; color: #FFFFFF; border-radius: 8px; text-align: center; font-family: var(--font-mono); font-size: 1.75rem; font-weight: 800; letter-spacing: 0.05em; margin-bottom: 0.75rem;">
                <span class="timer-display-text">--:--</span>
            </div>
            
            <p style="font-size: 0.75rem; color: #71717A; margin: 0; text-align: center;">
                Ujian akan otomatis terkumpul saat timer menyentuh 00:00.
            </p>
        </div>

        <!-- Card 2: Question Palette Grid -->
        <div class="supabase-panel-card" style="padding: 1.25rem;">
            <span class="corner-crosshair corner-tl">+</span>
            <span class="corner-crosshair corner-tr">+</span>
            <span class="corner-crosshair corner-bl">+</span>
            <span class="corner-crosshair corner-br">+</span>

            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem; padding-bottom: 0.5rem; border-bottom: 1px solid #E5E7EB;">
                <h4 style="font-family: var(--font-heading); font-size: 0.85rem; font-weight: 800; color: #18181B; margin: 0; text-transform: uppercase; letter-spacing: 0.05em;">
                    Palet Nomor Soal
                </h4>
                <span id="answered-counter-text" class="font-mono" style="font-size: 0.75rem; font-weight: 700; color: #18181B;">
                    0 / <?= $totalQuestions ?> Terjawab
                </span>
            </div>

            <!-- Palette Grid -->
            <div id="quiz-page-buttons-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(38px, 1fr)); gap: 0.5rem; margin-bottom: 1.25rem;">
                <?php foreach ($questions as $index => $q): ?>
                    <?php
                    $isAnswered = isset($pausedAnswers[$index]) && $pausedAnswers[$index] !== '';
                    ?>
                    <button type="button" class="palette-btn font-mono <?= $index === 0 ? 'current' : '' ?> <?= $isAnswered ? 'answered' : '' ?>" data-index="<?= $index ?>">
                        <?= $index + 1 ?>
                    </button>
                <?php endforeach; ?>
            </div>

            <!-- Palette Legend -->
            <div style="display: flex; align-items: center; justify-content: space-between; font-size: 0.725rem; color: #71717A; padding-top: 0.75rem; border-top: 1px solid #E5E7EB; margin-bottom: 1.25rem;">
                <span style="display: inline-flex; align-items: center; gap: 0.35rem;">
                    <span style="width: 10px; height: 10px; border-radius: 2px; background-color: #18181B; display: inline-block;"></span>
                    <span>Terjawab</span>
                </span>
                <span style="display: inline-flex; align-items: center; gap: 0.35rem;">
                    <span style="width: 10px; height: 10px; border-radius: 2px; background-color: #FFFFFF; border: 1px solid #D4D4D8; display: inline-block;"></span>
                    <span>Belum</span>
                </span>
                <span style="display: inline-flex; align-items: center; gap: 0.35rem;">
                    <span style="width: 10px; height: 10px; border-radius: 2px; outline: 1.5px solid #18181B; display: inline-block;"></span>
                    <span>Aktif</span>
                </span>
            </div>

            <!-- Palette Direct Submit Button -->
            <button type="button" class="btn-primary-black btn-open-submit-modal" style="width: 100%; justify-content: center; font-size: 0.825rem; padding: 0.55rem 1rem;">
                <i data-lucide="check-circle" style="width: 14px; height: 14px;"></i>
                <span>Kumpulkan Ujian Sekarang</span>
            </button>
        </div>
    </div>
</div>

<!-- 1. MODAL KONFIRMASI KUMPULKAN UJIAN -->
<div id="submit-confirm-modal" class="dialog-overlay" style="display: none; position: fixed; inset: 0; background-color: rgba(0,0,0,0.5); backdrop-filter: blur(6px); -webkit-backdrop-filter: blur(6px); z-index: 2000; align-items: center; justify-content: center;">
    <div class="supabase-panel-card" style="max-width: 440px; width: 90%; padding: 1.75rem; background: #FFFFFF;">
        <span class="corner-crosshair corner-tl">+</span>
        <span class="corner-crosshair corner-tr">+</span>
        <span class="corner-crosshair corner-bl">+</span>
        <span class="corner-crosshair corner-br">+</span>

        <h3 style="font-family: var(--font-heading); font-size: 1.15rem; font-weight: 800; color: #18181B; margin: 0 0 0.5rem 0;">
            Kumpulkan Ujian Kuis?
        </h3>
        <p style="font-size: 0.85rem; color: #71717A; margin: 0 0 1.25rem 0; line-height: 1.45;">
            Pastikan seluruh pertanyaan telah Anda jawab dengan teliti sebelum mengirimkan lembar ujian.
        </p>

        <!-- Status Box -->
        <div style="background-color: #FAFAFA; border: 1px solid #E5E7EB; border-radius: 8px; padding: 1rem; margin-bottom: 1.5rem; display: flex; flex-direction: column; gap: 0.5rem;">
            <div style="display: flex; align-items: center; justify-content: space-between; font-size: 0.825rem;">
                <span style="color: #71717A;">Total Pertanyaan:</span>
                <span class="font-mono" style="font-weight: 800; color: #18181B;"><?= $totalQuestions ?> Soal</span>
            </div>
            <div style="display: flex; align-items: center; justify-content: space-between; font-size: 0.825rem;">
                <span style="color: #71717A;">Sudah Terjawab:</span>
                <span id="modal-answered-count" class="font-mono" style="font-weight: 800; color: #18181B;">0</span>
            </div>
            <div style="display: flex; align-items: center; justify-content: space-between; font-size: 0.825rem;">
                <span style="color: #71717A;">Belum Terjawab:</span>
                <span id="modal-unanswered-count" class="font-mono" style="font-weight: 800; color: #EF4444;">0</span>
            </div>
        </div>

        <div style="display: flex; align-items: center; justify-content: flex-end; gap: 0.65rem;">
            <button type="button" id="btn-cancel-submit-modal" class="btn-secondary-outline" style="font-size: 0.825rem; padding: 0.45rem 0.95rem;">
                Periksa Kembali
            </button>
            <button type="button" id="btn-final-submit" class="btn-primary-black" style="font-size: 0.825rem; padding: 0.45rem 1.15rem;">
                Ya, Kumpulkan Ujian
            </button>
        </div>
    </div>
</div>

<!-- 2. MODAL JEDA (PAUSE & RESUME) -->
<div id="pause-dialog" class="dialog-overlay" style="display: none; position: fixed; inset: 0; background-color: rgba(0,0,0,0.5); backdrop-filter: blur(6px); -webkit-backdrop-filter: blur(6px); z-index: 2000; align-items: center; justify-content: center;">
    <div class="supabase-panel-card" style="max-width: 440px; width: 90%; padding: 1.75rem; background: #FFFFFF;">
        <span class="corner-crosshair corner-tl">+</span>
        <span class="corner-crosshair corner-tr">+</span>
        <span class="corner-crosshair corner-bl">+</span>
        <span class="corner-crosshair corner-br">+</span>

        <h3 style="font-family: var(--font-heading); font-size: 1.15rem; font-weight: 800; color: #18181B; margin: 0 0 0.4rem 0;">
            Jeda Pengerjaan Ujian?
        </h3>
        <p style="font-size: 0.85rem; color: #71717A; margin: 0 0 1.5rem 0; line-height: 1.45;">
            Progres jawaban dan sisa waktu pengerjaan Anda akan disimpan di database. Anda dapat melanjutkannya kapan saja melalui katalog kuis.
        </p>
        <div style="display: flex; align-items: center; justify-content: flex-end; gap: 0.65rem;">
            <button type="button" id="btn-cancel-pause" class="btn-secondary-outline" style="font-size: 0.825rem; padding: 0.45rem 0.95rem;">
                Lanjutkan Ujian
            </button>
            <button type="button" id="btn-confirm-pause" class="btn-primary-black" style="font-size: 0.825rem; padding: 0.45rem 1.15rem;">
                Ya, Simpan & Jeda
            </button>
        </div>
    </div>
</div>

<!-- Quiz Player Scripts -->
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
        
        // Reset labels on current question block
        const labels = block.querySelectorAll('.quiz-option-label');
        labels.forEach(l => {
            l.style.borderColor = '#E5E7EB';
            l.style.backgroundColor = '#FAFAFA';
            const b = l.querySelector('.option-badge');
            if (b) {
                b.style.backgroundColor = '#FFFFFF';
                b.style.borderColor = '#D4D4D8';
                b.style.color = '#18181B';
            }
        });

        // Highlight selected label
        const parent = radio.closest('.quiz-option-label');
        if (parent) {
            parent.style.borderColor = '#18181B';
            parent.style.backgroundColor = '#FFFFFF';
            const b = parent.querySelector('.option-badge');
            if (b) {
                b.style.backgroundColor = '#18181B';
                b.style.borderColor = '#18181B';
                b.style.color = '#FFFFFF';
            }
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
<script src="<?= BASE_URL ?>/js/quiz-play.js?v=<?= time() ?>"></script>

<?php require_once dirname(__DIR__) . '/templates/footer.php'; ?>
