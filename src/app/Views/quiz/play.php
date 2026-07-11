<?php require_once dirname(__DIR__) . '/templates/header.php'; ?>

<!-- Custom Styles for Quiz -->
<link rel="stylesheet" href="<?= BASE_URL ?>/css/quiz.css?v=<?= time() ?>">
<style>
    /* Disable page loader on quiz play page by default */
    .page-loader {
        display: none !important;
    }
    
    /* Premium Quiz UI Override */
    .quiz-container {
        max-width: 800px;
        margin: 0 auto;
        padding-bottom: 2rem;
        animation: fadeIn 0.4s ease-out;
    }

    .play-card {
        background: rgba(255, 255, 255, 0.7);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.5);
        border-radius: 16px;
        padding: 1.5rem 2rem;
        box-shadow: 0 10px 40px -10px rgba(0, 0, 0, 0.05), inset 0 1px 0 rgba(255, 255, 255, 0.8);
    }

    .quiz-header {
        text-align: center;
        margin-bottom: 1rem;
    }

    .quiz-title {
        font-size: 1.5rem;
        font-weight: 800;
        background: linear-gradient(135deg, #7c3aed 0%, #4f46e5 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        margin-bottom: 0.25rem;
    }

    .question-block {
        display: none;
        animation: fadeIn 0.4s ease-out;
    }

    .question-block.active {
        display: block;
    }

    .quiz-nav-container {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 1rem;
    }

    .btn-nav {
        background: #f1f5f9;
        color: #475569;
        border: none;
        padding: 0.6rem 1.25rem;
        font-size: 0.9rem;
        font-weight: 700;
        border-radius: 10px;
        cursor: pointer;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .btn-nav:hover:not(:disabled) {
        background: #e2e8f0;
        color: #0f172a;
    }

    .btn-nav:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    .question-text {
        font-size: 1.05rem;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 1rem;
        line-height: 1.4;
    }

    .options-list {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .option-label {
        position: relative;
        display: flex;
        align-items: center;
        padding: 0.75rem 1rem;
        border: 2px solid #f1f5f9;
        border-radius: 10px;
        cursor: pointer;
        transition: all 0.2s ease;
        background: #f8fafc;
        font-weight: 500;
        font-size: 0.9rem;
        color: #475569;
    }

    .option-label:hover {
        background: #f1f5f9;
        border-color: #e2e8f0;
        color: #0f172a;
    }

    .option-label.selected {
        background: #f5f3ff;
        border-color: #7c3aed;
        color: #5b21b6;
        box-shadow: 0 2px 8px rgba(124, 58, 237, 0.1);
    }

    .option-label input[type="radio"] {
        display: none;
    }

    .option-label strong {
        margin-right: 0.5rem;
        font-weight: 700;
        color: inherit;
        background: rgba(124, 58, 237, 0.1);
        width: 24px;
        height: 24px;
        font-size: 0.85rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 6px;
    }

    .option-label.selected strong {
        background: #7c3aed;
        color: #ffffff;
    }

    /* Ultra Minimalist Inline Timer */
    .timer-pill {
        display: flex;
        align-items: center;
        padding: 0.25rem 0;
    }

    .timer-text {
        font-size: 1.1rem;
        font-weight: 800;
        color: #7c3aed;
        font-variant-numeric: tabular-nums;
        transition: color 0.3s ease;
    }

    .timer-pill.warning .timer-text {
        color: #ef4444;
    }

    @keyframes pulse {
        0% {
            transform: scale(1);
        }

        50% {
            transform: scale(1.05);
        }

        100% {
            transform: scale(1);
        }
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Primary Submit Button Redesign */
    .btn-submit-quiz {
        background: linear-gradient(135deg, #7c3aed 0%, #4f46e5 100%);
        color: #ffffff;
        border: none;
        padding: 0.6rem 1.25rem;
        font-size: 0.9rem;
        font-weight: 700;
        border-radius: 10px;
        cursor: pointer;
        transition: all 0.2s ease;
        box-shadow: 0 4px 15px rgba(124, 58, 237, 0.3);
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .btn-submit-quiz:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(124, 58, 237, 0.4);
    }

    /* Quiz Pagination Styles */
    .quiz-pagination {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        justify-content: flex-start;
        margin-top: 1.5rem;
        background: rgba(255, 255, 255, 0.7);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.5);
        border-radius: 16px;
        padding: 1rem 1.5rem;
        box-shadow: 0 10px 40px -10px rgba(0, 0, 0, 0.05), inset 0 1px 0 rgba(255, 255, 255, 0.8);
    }

    .page-number {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 36px;
        height: 36px;
        border-radius: 8px;
        font-size: 0.9rem;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.2s ease;
        border: 2px solid #e2e8f0;
        background: #f8fafc;
        color: #64748b;
    }

    .page-number.active {
        border-color: #7c3aed;
        color: #7c3aed;
        box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.15);
    }

    .page-number.answered {
        background: #10b981;
        border-color: #10b981;
        color: #ffffff;
    }

    .page-number.answered.active {
        border-color: #047857;
        box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.3);
    }

    .page-number:hover {
        transform: translateY(-1px);
        background: #f1f5f9;
        border-color: #cbd5e1;
        color: #0f172a;
    }
    
    .page-number.answered:hover {
        background: #059669;
        border-color: #059669;
        color: #ffffff;
    }
</style>

<div class="quiz-container">
    <!-- Breadcrumb Navigation -->
    <nav class="breadcrumb"
        style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 1rem; font-size: 0.85rem; font-weight: 500; color: #64748b; font-family: 'Plus Jakarta Sans', sans-serif;">
        <span style="color: #64748b;">Dashboard</span>
        <span style="color: #cbd5e1;">/</span>
        <span style="color: #64748b;">Quiz</span>
        <span style="color: #cbd5e1;">/</span>
        <span style="color: #0f172a; font-weight: 600;"><?= htmlspecialchars($quiz['title']) ?></span>
    </nav>

    <!-- Quiz Form Card -->
    <form id="quiz-form" method="POST" action="<?= BASE_URL ?>/quiz/submit/<?= $quiz['id'] ?>" class="play-card">
        <?= \App\Core\Security::csrfField() ?>
        <input type="hidden" name="time_left" id="time_left" value="<?= $durationSeconds ?>">

        <?php
        // Convert duration to seconds
        $durationSeconds = (isset($quiz['duration']) ? (int) $quiz['duration'] : 0) * 60;
        ?>
        <div class="quiz-header" style="display: flex; justify-content: flex-end; align-items: flex-start;">
            <?php if ($durationSeconds > 0): ?>
            <!-- Inline Timer UI -->
            <div id="quiz-timer" class="timer-pill">
                <span id="timer-text" class="timer-text">00:00</span>
            </div>
            <?php endif; ?>
        </div>

        <?php foreach ($quiz['questions'] as $qIndex => $q): ?>
            <div class="question-block <?= $qIndex === 0 ? 'active' : '' ?>">
                <div style="font-size: 0.9rem; font-weight: 700; color: #7c3aed; margin-bottom: 0.5rem;">Pertanyaan
                    <?= ($qIndex + 1) ?> dari <?= count($quiz['questions']) ?>
                </div>

                <?php if (!empty($q['image_path'])): ?>
                    <div style="margin: 0.5rem 0 1rem 0; text-align: left;">
                        <img src="<?= BASE_URL ?>/<?= htmlspecialchars($q['image_path']) ?>" alt="Gambar Pertanyaan" style="max-width: 100%; max-height: 400px; height: auto; border-radius: 8px; border: 1px solid #e2e8f0; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                    </div>
                <?php endif; ?>

                <h3 class="question-text"><?= htmlspecialchars($q['question']) ?></h3>

                <div class="options-list">
                    <?php foreach ($q['options'] as $key => $optionText): ?>
                        <?php $isChecked = isset($pausedState['answers'][$qIndex]) && $pausedState['answers'][$qIndex] === $key; ?>
                        <label class="option-label <?= $isChecked ? 'selected' : '' ?>">
                            <input type="radio" name="answers[<?= $qIndex ?>]" value="<?= $key ?>" required <?= $isChecked ? 'checked' : '' ?>>
                            <span><strong><?= $key ?></strong> <?= htmlspecialchars($optionText) ?></span>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>

        <div class="quiz-nav-container">
            <div style="display: flex; gap: 0.5rem;">
                <button type="button" id="btn-prev" class="btn-nav" disabled>
                    <i data-lucide="arrow-left" style="width: 1.2rem; height: 1.2rem;"></i>
                </button>

                <button type="button" id="btn-next" class="btn-nav">
                    <i data-lucide="arrow-right" style="width: 1.2rem; height: 1.2rem;"></i>
                </button>
            </div>

            <div style="display: flex; gap: 0.5rem;">
                <button type="button" id="btn-pause" class="btn-nav"
                    style="background: #fffbeb; color: #d97706; border: 1px solid #fef3c7;"
                    onclick="window.isQuizSubmitting = true; const f = document.getElementById('quiz-form'); f.action = '<?= BASE_URL ?>/quiz/pause/<?= $quiz['id'] ?>'; f.submit();">
                    <i data-lucide="pause-circle" style="width: 1.2rem; height: 1.2rem;"></i>
                </button>
                <button type="submit" id="btn-submit-quiz" class="btn-submit-quiz" disabled
                    style="opacity: 0.5; cursor: not-allowed;">
                    <i data-lucide="check-circle" style="width: 1.2rem; height: 1.2rem;"></i>
                </button>
            </div>
        </div>
    </form>

    <!-- Quiz Pagination -->
    <div class="quiz-pagination">
        <?php foreach ($quiz['questions'] as $qIndex => $q): ?>
            <?php $isAnswered = isset($pausedState['answers'][$qIndex]); ?>
            <button type="button" class="page-number <?= $qIndex === 0 ? 'active' : '' ?> <?= $isAnswered ? 'answered' : '' ?>" data-slide="<?= $qIndex ?>">
                <?= $qIndex + 1 ?>
            </button>
        <?php endforeach; ?>
    </div>
</div>

<?php if ($durationSeconds > 0): ?>

    <!-- Timer Logic -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            let timeLeft = <?= isset($pausedState['time_left']) && $pausedState['time_left'] > 0 ? $pausedState['time_left'] : $durationSeconds ?>;
            const timerText = document.getElementById('timer-text');
            const timerPill = document.getElementById('quiz-timer');
            const quizForm = document.getElementById('quiz-form');
            const timeLeftInput = document.getElementById('time_left');

            function updateTimerDisplay() {
                const minutes = Math.floor(timeLeft / 60);
                const seconds = timeLeft % 60;
                timerText.textContent = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
                
                if (timeLeftInput) {
                    timeLeftInput.value = timeLeft;
                }

                // Warning state if less than 1 minute
                if (timeLeft <= 60 && timeLeft > 0) {
                    timerPill.classList.add('warning');
                }
            }

            updateTimerDisplay(); // Initial display

            const timerInterval = setInterval(() => {
                timeLeft--;
                if (timeLeft <= 0) {
                    clearInterval(timerInterval);
                    timerText.textContent = "00:00";
                    
                    // Block interaction forcefully
                    const overlay = document.createElement('div');
                    overlay.style.position = 'fixed';
                    overlay.style.top = '0';
                    overlay.style.left = '0';
                    overlay.style.width = '100vw';
                    overlay.style.height = '100vh';
                    overlay.style.backgroundColor = 'rgba(15, 23, 42, 0.9)';
                    overlay.style.backdropFilter = 'blur(10px)';
                    overlay.style.color = '#ffffff';
                    overlay.style.display = 'flex';
                    overlay.style.flexDirection = 'column';
                    overlay.style.justifyContent = 'center';
                    overlay.style.alignItems = 'center';
                    overlay.style.zIndex = '9999';
                    overlay.style.fontFamily = "'Plus Jakarta Sans', sans-serif";
                    
                    overlay.innerHTML = `
                        <div style="background: #ef4444; border-radius: 50%; width: 80px; height: 80px; display: flex; align-items: center; justify-content: center; margin-bottom: 1.5rem; animation: pulse 1.5s infinite;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: white;"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                        </div>
                        <h2 style="font-size:2.5rem; font-weight:800; margin-bottom:0.5rem; color:#f8fafc;">Waktu Habis!</h2>
                        <p style="font-size:1.1rem; color:#94a3b8; font-weight:500;">Sistem sedang mengumpulkan kuis Anda secara otomatis...</p>
                    `;
                    document.body.appendChild(overlay);

                    // Remove required attributes so form can submit even if not fully answered
                    quizForm.querySelectorAll('input[required]').forEach(input => input.removeAttribute('required'));
                    
                    window.isQuizSubmitting = true;
                    // Submit forcefully after 2 seconds
                    setTimeout(() => {
                        quizForm.submit();
                    }, 2000);
                } else {
                    updateTimerDisplay();
                }
            }, 1000);
        });
    </script>
<?php endif; ?>

<!-- Option Select Script & Icons -->
<script>
    document.addEventListener('DOMContentLoaded', () => {
        if (window.lucide) {
            lucide.createIcons();
        }

        // Dynamic styling when selecting options
        const optionLabels = document.querySelectorAll('.option-label');
        optionLabels.forEach(label => {
            const radio = label.querySelector('input[type="radio"]');

            label.addEventListener('click', () => {
                const block = label.closest('.question-block');
                block.querySelectorAll('.option-label').forEach(el => el.classList.remove('selected'));
                label.classList.add('selected');

                // Optional: Auto advance on select could go here (if desired)
            });
        });

        // Slider Navigation Logic
        const blocks = document.querySelectorAll('.question-block');
        const btnPrev = document.getElementById('btn-prev');
        const btnNext = document.getElementById('btn-next');
        const btnSubmit = document.getElementById('btn-submit-quiz');
        let currentSlide = 0;
        const totalSlides = blocks.length;

        function updateSlider() {
            blocks.forEach((block, index) => {
                if (index === currentSlide) {
                    block.classList.add('active');
                } else {
                    block.classList.remove('active');
                }
            });

            if (btnPrev) btnPrev.disabled = currentSlide === 0;
            if (btnNext) btnNext.disabled = currentSlide === totalSlides - 1;

            // Update pagination active state
            document.querySelectorAll('.page-number').forEach((btn, index) => {
                if (index === currentSlide) {
                    btn.classList.add('active');
                } else {
                    btn.classList.remove('active');
                }
            });
        }

        function checkSubmitReadiness() {
            const answeredCount = document.querySelectorAll('.options-list input[type="radio"]:checked').length;
            if (btnSubmit) {
                if (answeredCount === totalSlides) {
                    btnSubmit.disabled = false;
                    btnSubmit.style.opacity = '1';
                    btnSubmit.style.cursor = 'pointer';
                } else {
                    btnSubmit.disabled = true;
                    btnSubmit.style.opacity = '0.5';
                    btnSubmit.style.cursor = 'not-allowed';
                }
            }

            // Highlight answered pagination buttons
            blocks.forEach((block, index) => {
                const radioChecked = block.querySelector('.options-list input[type="radio"]:checked');
                const pageBtn = document.querySelector(`.page-number[data-slide="${index}"]`);
                if (pageBtn) {
                    if (radioChecked) {
                        pageBtn.classList.add('answered');
                    } else {
                        pageBtn.classList.remove('answered');
                    }
                }
            });
        }

        const radioInputs = document.querySelectorAll('.options-list input[type="radio"]');
        radioInputs.forEach(radio => {
            radio.addEventListener('change', checkSubmitReadiness);
        });
        
        // Initial check in case browser auto-fills or preserves state on refresh
        checkSubmitReadiness();
        updateSlider(); // Initial update of active states

        // Pagination buttons click listener
        document.querySelectorAll('.page-number').forEach(btn => {
            btn.addEventListener('click', () => {
                const target = parseInt(btn.getAttribute('data-slide'));
                currentSlide = target;
                updateSlider();
            });
        });

        if (btnNext) {
            btnNext.addEventListener('click', () => {
                if (currentSlide < totalSlides - 1) {
                    currentSlide++;
                    updateSlider();
                }
            });
        }

        if (btnPrev) {
            btnPrev.addEventListener('click', () => {
                if (currentSlide > 0) {
                    currentSlide--;
                    updateSlider();
                }
            });
        }

        // Track when quiz form is submitted
        const quizFormElement = document.getElementById('quiz-form');
        if (quizFormElement) {
            quizFormElement.addEventListener('submit', () => {
                window.isQuizSubmitting = true;
            });
        }
    });

    // Initialize global flag
    window.isQuizSubmitting = false;

    document.addEventListener('DOMContentLoaded', () => {
        const exitModal = document.getElementById('confirm-exit-modal');
        const btnCancel = document.getElementById('exit-modal-btn-cancel');
        if (exitModal && btnCancel) {
            const closeExitModal = () => {
                const loader = document.getElementById('page-loader');
                if (loader) {
                    loader.classList.add('fade-out');
                }
                exitModal.style.opacity = '0';
                exitModal.querySelector('div').style.transform = 'scale(0.95)';
                setTimeout(() => {
                    exitModal.style.display = 'none';
                }, 300);
            };
            btnCancel.addEventListener('click', closeExitModal);
            exitModal.addEventListener('click', (e) => {
                if (e.target === exitModal) closeExitModal();
            });
        }
    });
</script>

<!-- Premium Exit Quiz Confirmation Modal -->
<div id="confirm-exit-modal" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); z-index: 2000000; align-items: center; justify-content: center; opacity: 0; transition: opacity 0.3s ease; font-family: 'Plus Jakarta Sans', sans-serif;">
    <div style="background: rgba(255, 255, 255, 0.95); border: 1px solid rgba(255, 255, 255, 0.8); box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1), 0 10px 10px -5px rgba(0,0,0,0.04); border-radius: 16px; width: 90%; max-width: 420px; padding: 2rem; transform: scale(0.95); transition: transform 0.3s ease; text-align: center;">
        <div style="background: rgba(239, 68, 68, 0.1); width: 56px; height: 56px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.25rem auto; color: #ef4444;">
            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
        </div>
        <h3 style="font-size: 1.25rem; font-weight: 800; color: #0f172a; margin-bottom: 0.5rem; font-family: 'Plus Jakarta Sans', sans-serif;">Tinggalkan Kuis?</h3>
        <p style="font-size: 0.9rem; color: #64748b; line-height: 1.5; margin-bottom: 1.75rem; font-family: 'Plus Jakarta Sans', sans-serif;">Apakah Anda yakin ingin keluar? Progress kuis Anda tidak akan tersimpan jika Anda keluar tanpa menekan tombol jeda.</p>
        <div style="display: flex; gap: 0.75rem; justify-content: center;">
            <button id="exit-modal-btn-cancel" style="flex: 1; padding: 0.65rem; border-radius: 10px; border: 1px solid #cbd5e1; background: #ffffff; color: #475569; font-weight: 700; cursor: pointer; font-size: 0.85rem; transition: all 0.2s; font-family: 'Plus Jakarta Sans', sans-serif;">Lanjutkan Kuis</button>
            <button id="exit-modal-btn-confirm" style="flex: 1; padding: 0.65rem; border-radius: 10px; border: none; background: #ef4444; color: #ffffff; font-weight: 700; cursor: pointer; font-size: 0.85rem; transition: all 0.2s; box-shadow: 0 4px 12px rgba(239, 68, 68, 0.2); font-family: 'Plus Jakarta Sans', sans-serif;">Keluar</button>
        </div>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/templates/footer.php'; ?>