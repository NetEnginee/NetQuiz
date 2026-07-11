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
                                <?php
                                $isNew = false;
                                $timeAgoText = '';
                                if (!empty($quiz['created_at'])) {
                                    $createdAt = strtotime($quiz['created_at']);
                                    $diff = time() - $createdAt;
                                    $isNew = $diff < (1 * 24 * 60 * 60); // 1 hari terakhir
                                    
                                    if (!$isNew) {
                                        if (!function_exists('getQuizTimeAgo')) {
                                            function getQuizTimeAgo($diff) {
                                                $intervals = [
                                                    31536000 => 'tahun',
                                                    2592000  => 'bulan',
                                                    604800   => 'minggu',
                                                    86400    => 'hari',
                                                    3600     => 'jam',
                                                    60       => 'menit'
                                                ];
                                                foreach ($intervals as $secs => $label) {
                                                    $div = $diff / $secs;
                                                    if ($div >= 1) {
                                                        $num = round($div);
                                                        return $num . ' ' . $label . ' yang lalu';
                                                    }
                                                }
                                                return 'Baru saja';
                                            }
                                        }
                                        $timeAgoText = getQuizTimeAgo($diff);
                                    }
                                }
                                ?>
                                <div style="display: flex; align-items: flex-start; justify-content: space-between; gap: 0.5rem; width: 100%; margin-bottom: 0.75rem;">
                                    <h3 class="quiz-title" style="flex: 1;"><?= htmlspecialchars($quiz['title']) ?></h3>
                                    <?php if ($isNew): ?>
                                        <span class="badge-new" style="background-color: #f0fdf4; border: 1px solid #bbf7d0; color: #16a34a; font-size: 0.65rem; font-weight: 800; padding: 0.15rem 0.5rem; border-radius: 6px; text-transform: uppercase; letter-spacing: 0.5px; flex-shrink: 0; font-family: 'Plus Jakarta Sans', sans-serif;">Baru</span>
                                    <?php elseif (!empty($timeAgoText)): ?>
                                        <span style="color: #64748b; font-size: 0.75rem; font-weight: 500; font-family: 'Plus Jakarta Sans', sans-serif; flex-shrink: 0; align-self: center;"><?= htmlspecialchars($timeAgoText) ?></span>
                                    <?php endif; ?>
                                </div>
                                <p class="quiz-desc"><?= htmlspecialchars($quiz['description']) ?></p>
                            </div>
                            <?php if (!empty($quiz['is_completed'])): ?>
                                <div style="display: flex; flex-direction: column; gap: 0.75rem; margin-top: auto; padding-top: 1rem; border-top: 1px solid #f1f5f9;">
                                    <div style="display: flex; justify-content: space-between; align-items: center;">
                                        <span style="font-size: 0.8rem; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px;">Skor Akhir</span>
                                        <?php $quizScore = $quiz['score'] ?? 0; ?>
                                        <span style="font-size: 1.5rem; font-weight: 800; color: <?= $quizScore >= 80 ? '#10b981' : ($quizScore >= 60 ? '#f59e0b' : '#ef4444') ?>; font-variant-numeric: tabular-nums;"><?= $quizScore ?></span>
                                    </div>
                                    <?php 
                                    $reviewUrl = method_exists('\App\Core\Security', 'encryptUrlId') 
                                        ? BASE_URL . '/quiz/review/' . \App\Core\Security::encryptUrlId($quiz['id']) 
                                        : BASE_URL . '/quiz/review/' . $quiz['id'];
                                    ?>
                                    <a href="<?= $reviewUrl ?>" class="btn-quiz-review" style="width: 100%; justify-content: center; box-sizing: border-box;">
                                        <i data-lucide="eye" style="width: 1rem; height: 1rem;"></i>
                                        Review Jawaban
                                    </a>
                                </div>
                            <?php else: ?>
                                <?php 
                                $isPaused = isset($_SESSION['paused_quiz'][$quiz['id']]); 
                                $playUrl = method_exists('\App\Core\Security', 'encryptUrlId') 
                                    ? BASE_URL . '/quiz/play/' . \App\Core\Security::encryptUrlId($quiz['id']) 
                                    : BASE_URL . '/quiz/play/' . $quiz['id'];
                                ?>
                                <a href="<?= $playUrl ?>" class="btn-quiz-action" <?= $isPaused ? 'style="background: #fef3c7; border: 1px solid #fde68a; color: #d97706; box-shadow: 0 4px 15px rgba(217, 119, 6, 0.1);"' : '' ?>>
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

<!-- Premium Start Quiz Confirmation Modal -->
<div id="confirm-start-modal" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.8); z-index: 2000000; align-items: center; justify-content: center; opacity: 0; transition: opacity 0.3s ease; font-family: 'Plus Jakarta Sans', sans-serif;">
    <div style="background: rgba(255, 255, 255, 0.95); border: 1px solid rgba(255, 255, 255, 0.8); box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1), 0 10px 10px -5px rgba(0,0,0,0.04); border-radius: 16px; width: 90%; max-width: 420px; padding: 2rem; transform: scale(0.95); transition: transform 0.3s ease; text-align: center;">
        <div style="background: rgba(124, 58, 237, 0.1); width: 56px; height: 56px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.25rem auto; color: #7c3aed;">
            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polygon points="10 8 16 12 10 16 10 8"></polygon></svg>
        </div>
        <h3 id="modal-title" style="font-size: 1.25rem; font-weight: 800; color: #0f172a; margin-bottom: 0.5rem; font-family: 'Plus Jakarta Sans', sans-serif;">Mulai Kuis?</h3>
        <p id="modal-desc" style="font-size: 0.9rem; color: #64748b; line-height: 1.5; margin-bottom: 1.75rem; font-family: 'Plus Jakarta Sans', sans-serif;">Apakah Anda yakin ingin memulai kuis ini? Waktu akan mulai berjalan.</p>
        <div style="display: flex; gap: 0.75rem; justify-content: center;">
            <button id="modal-btn-cancel" style="flex: 1; padding: 0.65rem; border-radius: 10px; border: 1px solid #cbd5e1; background: #ffffff; color: #475569; font-weight: 700; cursor: pointer; font-size: 0.85rem; transition: all 0.2s; font-family: 'Plus Jakarta Sans', sans-serif;">Batal</button>
            <button id="modal-btn-confirm" style="flex: 1; padding: 0.65rem; border-radius: 10px; border: none; background: linear-gradient(135deg, #7c3aed 0%, #4f46e5 100%); color: #ffffff; font-weight: 700; cursor: pointer; font-size: 0.85rem; transition: all 0.2s; box-shadow: 0 4px 12px rgba(124, 58, 237, 0.2); font-family: 'Plus Jakarta Sans', sans-serif;">Mulai</button>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const playButtons = document.querySelectorAll('.btn-quiz-action');
    const modal = document.getElementById('confirm-start-modal');
    const modalTitle = document.getElementById('modal-title');
    const modalDesc = document.getElementById('modal-desc');
    const btnCancel = document.getElementById('modal-btn-cancel');
    const btnConfirm = document.getElementById('modal-btn-confirm');
    let targetUrl = '';

    if (playButtons.length > 0 && modal) {
        playButtons.forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                targetUrl = btn.getAttribute('href');
                const isPausedText = btn.textContent.trim().includes('Lanjutkan');
                
                if (isPausedText) {
                    modalTitle.textContent = 'Lanjutkan Kuis?';
                    modalDesc.textContent = 'Apakah Anda yakin ingin melanjutkan kuis ini? Sisa waktu Anda akan berjalan kembali.';
                    btnConfirm.textContent = 'Lanjutkan';
                } else {
                    modalTitle.textContent = 'Mulai Kuis?';
                    modalDesc.textContent = 'Apakah Anda yakin ingin memulai kuis ini? Waktu pengerjaan akan segera dimulai.';
                    btnConfirm.textContent = 'Mulai';
                }

                modal.style.display = 'flex';
                // Trigger reflow
                modal.offsetHeight;
                modal.style.opacity = '1';
                modal.querySelector('div').style.transform = 'scale(1)';
            });
        });

        const closeModal = () => {
            modal.style.opacity = '0';
            modal.querySelector('div').style.transform = 'scale(0.95)';
            setTimeout(() => {
                modal.style.display = 'none';
            }, 300);
        };

        btnCancel.addEventListener('click', closeModal);
        modal.addEventListener('click', (e) => {
            if (e.target === modal) closeModal();
        });

        btnConfirm.addEventListener('click', () => {
            if (targetUrl) {
                const loader = document.getElementById('page-loader');
                if (loader) {
                    loader.classList.remove('fade-out');
                }
                window.location.href = targetUrl;
            }
        });
    }
});
</script>

<?php require_once dirname(__DIR__) . '/templates/footer.php'; ?>