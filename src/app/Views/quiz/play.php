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
        min-height: 400px;
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

    <?php
    // Convert duration to seconds
    $durationSeconds = (isset($quiz['duration']) ? (int) $quiz['duration'] : 0) * 60;
    ?>

    <!-- Quiz Form Card -->
    <form id="quiz-form" method="POST" action="<?= BASE_URL ?>/quiz/submit/<?= $quiz['id'] ?>" class="play-card">
        <?= \App\Core\Security::csrfField() ?>
        <input type="hidden" name="time_left" id="time_left" value="<?= $durationSeconds ?>">

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
                        <img src="<?= BASE_URL ?>/<?= htmlspecialchars($q['image_path']) ?>" alt="Gambar Pertanyaan"
                            style="max-width: 100%; max-height: 400px; height: auto; border-radius: 8px; border: 1px solid #e2e8f0; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
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
            <button type="button"
                class="page-number <?= $qIndex === 0 ? 'active' : '' ?> <?= $isAnswered ? 'answered' : '' ?>"
                data-slide="<?= $qIndex ?>">
                <?= $qIndex + 1 ?>
            </button>
        <?php endforeach; ?>
    </div>
</div>

<!-- Expose PHP Variables securely to External JS -->
<script>
    window.NetQuizData = <?= json_encode([
        'quizId' => (int)$quiz['id'],
        'userId' => (int)$_SESSION['user']['id'],
        'durationSeconds' => $durationSeconds,
        'timeLeft' => isset($pausedState['time_left']) && $pausedState['time_left'] > 0 ? (int)$pausedState['time_left'] : $durationSeconds,
        'isResumed' => $pausedState !== null
    ], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
</script>

<!-- Load External Quiz Controller Script -->
<script src="<?= BASE_URL ?>/js/quiz-play.js?v=<?= time() ?>" defer></script>

<?php require_once dirname(__DIR__) . '/templates/footer.php'; ?>