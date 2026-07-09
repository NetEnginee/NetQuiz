<?php require_once dirname(__DIR__) . '/templates/header.php'; ?>

<!-- Custom Styles for Quiz -->
<link rel="stylesheet" href="<?= BASE_URL ?>/css/quiz.css?v=<?= time() ?>">
<style>
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
        background: #f8fafc;
        font-weight: 500;
        font-size: 0.9rem;
        color: #475569;
        cursor: default;
    }

    /* States for Review */
    .option-label.correct-answer {
        background: #ecfdf5;
        border-color: #10b981;
        color: #065f46;
    }

    .option-label.correct-answer strong {
        background: #10b981;
        color: #ffffff;
    }

    .option-label.wrong-answer {
        background: #fef2f2;
        border-color: #ef4444;
        color: #991b1b;
    }

    .option-label.wrong-answer strong {
        background: #ef4444;
        color: #ffffff;
    }

    .option-label input[type="radio"] {
        display: none;
    }

    .option-label strong {
        margin-right: 0.5rem;
        font-weight: 700;
        color: inherit;
        background: rgba(15, 23, 42, 0.1);
        width: 24px;
        height: 24px;
        font-size: 0.85rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 6px;
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

    .btn-back {
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
        text-decoration: none;
    }

    .btn-back:hover {
        background: #e2e8f0;
        color: #0f172a;
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
        <span style="color: #0f172a; font-weight: 600;">Review: <?= htmlspecialchars($quiz['title']) ?></span>
    </nav>

    <div class="play-card">
        <div class="quiz-header">
            <h1 class="quiz-title">Review Kuis</h1>
            <p style="color: #64748b; font-size: 1rem; font-weight: 600; margin: 0;">Skor Anda: <span
                    style="color: #10b981; font-size: 1.25rem;"><?= $score ?></span> / 100</p>
        </div>

        <?php foreach ($quiz['questions'] as $qIndex => $q): ?>
            <?php
            $userAns = strtoupper($userAnswers[$qIndex] ?? '');
            $correctAns = strtoupper($q['correct']);
            ?>
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
                        <?php
                        $optClass = '';
                        $iconHtml = '';
                        if ($key === $correctAns) {
                            $optClass = 'correct-answer'; // True answer is always green
                            $iconHtml = '<i data-lucide="check" style="position: absolute; right: 1rem; color: #10b981; width: 1.25rem; height: 1.25rem;"></i>';
                        } elseif ($key === $userAns && $userAns !== $correctAns) {
                            $optClass = 'wrong-answer'; // User picked this wrong answer
                            $iconHtml = '<i data-lucide="x" style="position: absolute; right: 1rem; color: #ef4444; width: 1.25rem; height: 1.25rem;"></i>';
                        }
                        ?>
                        <label class="option-label <?= $optClass ?>">
                            <span><strong><?= $key ?></strong> <?= htmlspecialchars($optionText) ?></span>
                            <?= $iconHtml ?>
                        </label>
                    <?php endforeach; ?>
                </div>

                <?php if ($userAns === ''): ?>

                    <?php endif; ?>
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

            <a href="<?= BASE_URL ?>/quiz" class="btn-back"
                style="background: #fff1f2; color: #e11d48; border: 1px solid #ffe4e6;">
                <i data-lucide="log-out" style="width: 1.2rem; height: 1.2rem;"></i>
                Keluar
            </a>
        </div>
    </div>
</div>

<script src="https://unpkg.com/lucide@latest"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        if (window.lucide) {
            lucide.createIcons();
        }

        // Slider Navigation Logic
        const blocks = document.querySelectorAll('.question-block');
        const btnPrev = document.getElementById('btn-prev');
        const btnNext = document.getElementById('btn-next');
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
        }

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
    });
</script>

<?php require_once dirname(__DIR__) . '/templates/footer.php'; ?>