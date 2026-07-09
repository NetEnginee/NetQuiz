<?php require_once dirname(__DIR__) . '/templates/header.php'; ?>

<!-- Custom Styles for Quiz -->
<link rel="stylesheet" href="<?= BASE_URL ?>/css/quiz.css?v=<?= time() ?>">
<style>
    .btn-quiz-action {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        background: linear-gradient(135deg, #7c3aed 0%, #4f46e5 100%);
        color: #ffffff;
        text-decoration: none;
        padding: 0.6rem 1.25rem;
        font-size: 0.9rem;
        font-weight: 600;
        border-radius: 10px;
        transition: all 0.2s ease;
        border: none;
        box-shadow: 0 4px 12px rgba(124, 58, 237, 0.2);
    }
    .btn-quiz-action:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 16px rgba(124, 58, 237, 0.3);
        color: #ffffff;
    }
    .btn-quiz-done {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        background: #f1f5f9;
        color: #94a3b8;
        padding: 0.6rem 1.25rem;
        font-size: 0.9rem;
        font-weight: 600;
        border-radius: 10px;
        border: 1px solid #e2e8f0;
        cursor: not-allowed;
        box-shadow: none;
    }
    .btn-quiz-review {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        background: #f8fafc;
        color: #6366f1;
        padding: 0.6rem 1.25rem;
        font-size: 0.9rem;
        font-weight: 600;
        border-radius: 10px;
        border: 1px solid #e0e7ff;
        text-decoration: none;
        transition: all 0.2s ease;
        box-shadow: 0 2px 4px rgba(99, 102, 241, 0.05);
    }
    .btn-quiz-review:hover {
        background: #e0e7ff;
        color: #4f46e5;
        border-color: #c7d2fe;
        transform: translateY(-1px);
    }
</style>

<div class="quiz-container">
    <!-- Breadcrumb Navigation -->
    <nav class="breadcrumb"
        style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 2rem; font-size: 0.85rem; font-weight: 500; color: #64748b; font-family: 'Plus Jakarta Sans', sans-serif;">
        <span style="color: #64748b;">Dashboard</span>
        <span style="color: #cbd5e1;">/</span>
        <span style="color: #0f172a; font-weight: 600;">Quiz</span>
    </nav>

    <?php if (isset($_SESSION['quiz_error'])): ?>
        <div style="background-color: #fee2e2; border-left: 4px solid #ef4444; padding: 1rem; border-radius: 8px; margin-bottom: 2rem; color: #b91c1c; display: flex; align-items: center; gap: 0.5rem; animation: fadeIn 0.3s ease-out;">
            <i data-lucide="alert-circle" style="width: 1.2rem; height: 1.2rem;"></i>
            <?= htmlspecialchars($_SESSION['quiz_error']) ?>
        </div>
        <?php unset($_SESSION['quiz_error']); ?>
    <?php endif; ?>

    <?php foreach ($categorized as $categoryName => $quizList): ?>
        <section class="category-section">
            <h2 class="category-title-header">
                <i data-lucide="folder"></i>
                <?= htmlspecialchars($categoryName) ?>
            </h2>

            <?php if (empty($quizList)): ?>
                <div class="empty-state-box"
                    style="background-color: #ffffff; border: 1px dashed #cbd5e1; border-radius: 12px; padding: 2rem 1.5rem; text-align: center; color: #94a3b8; font-family: 'Plus Jakarta Sans', sans-serif;">
                    <p class="empty-text" style="font-style: italic; margin: 0; font-size: 0.85rem;">Kuis belum tersedia untuk
                        kategori ini.</p>
                </div>
            <?php else: ?>
                <div class="quiz-grid">
                    <?php foreach ($quizList as $quiz): ?>
                        <div class="quiz-card">
                            <div class="quiz-info">
                                <h3 class="quiz-title"><?= htmlspecialchars($quiz['title']) ?></h3>
                                <p class="quiz-desc"><?= htmlspecialchars($quiz['description']) ?></p>
                            </div>
                            <?php if (!empty($quiz['is_completed'])): ?>
                                <div style="display: flex; flex-direction: column; gap: 0.75rem; margin-top: auto; padding-top: 1rem; border-top: 1px solid #f1f5f9;">
                                    <div style="display: flex; justify-content: space-between; align-items: center;">
                                        <span style="font-size: 0.8rem; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px;">Skor Akhir</span>
                                        <span style="font-size: 1.5rem; font-weight: 800; color: <?= $quiz['score'] >= 80 ? '#10b981' : ($quiz['score'] >= 60 ? '#f59e0b' : '#ef4444') ?>; font-variant-numeric: tabular-nums;"><?= $quiz['score'] ?></span>
                                    </div>
                                    <a href="<?= BASE_URL ?>/quiz/review/<?= $quiz['id'] ?>" class="btn-quiz-review" style="width: 100%; justify-content: center; box-sizing: border-box;">
                                        <i data-lucide="eye" style="width: 1rem; height: 1rem;"></i>
                                        Review Jawaban
                                    </a>
                                </div>
                            <?php else: ?>
                                <?php $isPaused = isset($_SESSION['paused_quiz'][$quiz['id']]); ?>
                                <a href="<?= BASE_URL ?>/quiz/play/<?= $quiz['id'] ?>" class="btn-quiz-action" <?= $isPaused ? 'style="background: #fef3c7; border: 1px solid #fde68a; color: #d97706; box-shadow: 0 4px 15px rgba(217, 119, 6, 0.1);"' : '' ?>>
                                    <i data-lucide="<?= $isPaused ? 'play' : 'play-circle' ?>" style="width: 1rem; height: 1rem;"></i>
                                    <?= $isPaused ? 'Lanjutkan Kuis' : 'Mulai Kuis' ?>
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>
    <?php endforeach; ?>
</div>

<?php require_once dirname(__DIR__) . '/templates/footer.php'; ?>