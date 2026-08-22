<?php
$categorized = $categorized ?? [];
$activeDifficulty = $activeDifficulty ?? 'all';

require_once dirname(__DIR__) . '/templates/header.php';
?>

<!-- Header & Breadcrumb -->
<div style="margin-bottom: 2rem;">
    <?= renderBreadcrumb([
        ['label' => 'Siswa', 'url' => BASE_URL . '/'],
        ['label' => 'Katalog Kuis']
    ]) ?>
</div>

<!-- Filter Bar: Difficulty Switcher -->
<div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem; margin-bottom: 2rem; padding: 0.75rem 1rem; background-color: #FFFFFF; border: 1px solid #E5E7EB; border-radius: 8px;">
    <div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
        <span style="font-size: 0.8rem; font-weight: 700; color: #71717A; text-transform: uppercase; margin-right: 0.25rem;">Tingkat Kesulitan:</span>
        <a href="<?= BASE_URL ?>/quiz?difficulty=all" class="quiz-segment-tab <?= $activeDifficulty === 'all' ? 'active' : '' ?>" style="text-decoration: none; font-size: 0.8rem; padding: 0.35rem 0.75rem;">
            Semua
        </a>
        <a href="<?= BASE_URL ?>/quiz?difficulty=Mudah" class="quiz-segment-tab <?= $activeDifficulty === 'Mudah' ? 'active' : '' ?>" style="text-decoration: none; font-size: 0.8rem; padding: 0.35rem 0.75rem;">
            Mudah
        </a>
        <a href="<?= BASE_URL ?>/quiz?difficulty=Sedang" class="quiz-segment-tab <?= $activeDifficulty === 'Sedang' ? 'active' : '' ?>" style="text-decoration: none; font-size: 0.8rem; padding: 0.35rem 0.75rem;">
            Sedang
        </a>
        <a href="<?= BASE_URL ?>/quiz?difficulty=Sulit" class="quiz-segment-tab <?= $activeDifficulty === 'Sulit' ? 'active' : '' ?>" style="text-decoration: none; font-size: 0.8rem; padding: 0.35rem 0.75rem;">
            Sulit
        </a>
    </div>
</div>

<!-- Categorized Quizzes Sections -->
<?php if (empty($categorized)): ?>
    <div class="supabase-panel-card" style="padding: 3rem 1.5rem; text-align: center;">
        <span class="corner-crosshair corner-tl">+</span>
        <span class="corner-crosshair corner-tr">+</span>
        <span class="corner-crosshair corner-bl">+</span>
        <span class="corner-crosshair corner-br">+</span>
        <div style="width: 44px; height: 44px; border-radius: 50%; background-color: #F4F4F5; margin: 0 auto 0.75rem; display: flex; align-items: center; justify-content: center; color: #71717A;">
            <i data-lucide="file-question" style="width: 22px; height: 22px;"></i>
        </div>
        <h3 style="font-size: 1rem; font-weight: 800; color: #18181B; margin: 0 0 0.25rem 0;">Tidak Ada Kuis Tersedia</h3>
        <p style="font-size: 0.85rem; color: #71717A; margin: 0;">Belum ada kuis yang sesuai dengan filter tingkat kesulitan yang dipilih.</p>
    </div>
<?php else: ?>
    <div style="display: flex; flex-direction: column; gap: 2.5rem;">
        <?php foreach ($categorized as $categoryName => $quizzes): ?>
            <?php if (!empty($quizzes)): ?>
                <div>
                    <!-- Category Section Header -->
                    <div style="display: flex; align-items: center; gap: 0.65rem; margin-bottom: 1rem; padding-bottom: 0.5rem; border-bottom: 1px solid #E5E7EB;">
                        <i data-lucide="folder" style="width: 18px; height: 18px; color: #18181B;"></i>
                        <h2 style="font-family: var(--font-heading); font-size: 1.15rem; font-weight: 800; color: #18181B; margin: 0;">
                            <?= htmlspecialchars($categoryName) ?>
                        </h2>
                        <span class="font-mono" style="font-size: 0.75rem; color: #71717A;">(<?= count($quizzes) ?> Kuis)</span>
                    </div>

                    <!-- Quizzes Grid -->
                    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 1.25rem;">
                        <?php foreach ($quizzes as $q): ?>
                            <?php
                            $isFinished = !empty($q['completed']);
                            $isPaused = !empty($q['paused']);
                            $userScore = $q['score'] ?? null;
                            ?>
                            <div class="supabase-panel-card" style="padding: 1.25rem; display: flex; flex-direction: column; justify-content: space-between;">
                                <span class="corner-crosshair corner-tl">+</span>
                                <span class="corner-crosshair corner-tr">+</span>
                                <span class="corner-crosshair corner-bl">+</span>
                                <span class="corner-crosshair corner-br">+</span>

                                <div>
                                    <!-- Top Meta Badges -->
                                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.75rem;">
                                        <span class="status-badge" style="background-color: #F4F4F5; font-size: 0.7rem;">
                                            <?= htmlspecialchars($q['difficulty'] ?? 'Mudah') ?>
                                        </span>
                                        <?php if ($isFinished): ?>
                                            <span class="status-badge status-active" style="font-size: 0.7rem;">
                                                <i data-lucide="check" style="width: 11px; height: 11px; margin-right: 2px;"></i>
                                                Selesai (Skor: <?= (int)$userScore ?>)
                                            </span>
                                        <?php elseif ($isPaused): ?>
                                            <span class="status-badge" style="background-color: #FEF3C7; color: #92400E; font-size: 0.7rem;">
                                                <i data-lucide="pause" style="width: 10px; height: 10px; margin-right: 2px;"></i>
                                                Dijeda
                                            </span>
                                        <?php endif; ?>
                                    </div>

                                    <!-- Quiz Title & Description -->
                                    <h3 style="font-family: var(--font-heading); font-size: 1rem; font-weight: 800; color: #18181B; margin: 0 0 0.4rem 0; line-height: 1.35;">
                                        <?= htmlspecialchars($q['title']) ?>
                                    </h3>
                                    <p style="font-size: 0.825rem; color: #52525B; margin: 0 0 1rem 0; line-height: 1.45; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 2; line-clamp: 2; -webkit-box-orient: vertical;">
                                        <?= htmlspecialchars($q['description'] ?? '') ?>
                                    </p>
                                </div>

                                <!-- Card Footer: Duration, Questions & CTA Button -->
                                <div style="padding-top: 0.75rem; border-top: 1px solid #E5E7EB; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.5rem;">
                                    <div style="display: flex; align-items: center; gap: 0.75rem; font-size: 0.75rem; color: #71717A;" class="font-mono">
                                        <span style="display: inline-flex; align-items: center; gap: 0.25rem;">
                                            <i data-lucide="clock" style="width: 12px; height: 12px;"></i>
                                            <?= (int)($q['duration'] ?? 15) ?> mnt
                                        </span>
                                        <span>•</span>
                                        <span style="display: inline-flex; align-items: center; gap: 0.25rem;">
                                            <i data-lucide="help-circle" style="width: 12px; height: 12px;"></i>
                                            <?= (int)($q['question_count'] ?? 1) ?> soal
                                        </span>
                                    </div>

                                    <div>
                                        <?php if ($isFinished): ?>
                                            <a href="<?= BASE_URL ?>/quiz/review/<?= (int)$q['id'] ?>" class="btn-secondary-outline" style="font-size: 0.775rem; padding: 0.35rem 0.75rem;">
                                                <i data-lucide="eye" style="width: 12px; height: 12px;"></i>
                                                <span>Review</span>
                                            </a>
                                        <?php elseif ($isPaused): ?>
                                            <a href="<?= BASE_URL ?>/quiz/play/<?= (int)$q['id'] ?>" class="btn-primary-black" style="font-size: 0.775rem; padding: 0.35rem 0.75rem;">
                                                <i data-lucide="play" style="width: 12px; height: 12px;"></i>
                                                <span>Lanjutkan</span>
                                            </a>
                                        <?php else: ?>
                                            <a href="<?= BASE_URL ?>/quiz/play/<?= (int)$q['id'] ?>" class="btn-primary-black" style="font-size: 0.775rem; padding: 0.35rem 0.75rem;">
                                                <i data-lucide="play" style="width: 12px; height: 12px;"></i>
                                                <span>Mulai Kuis</span>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php require_once dirname(__DIR__) . '/templates/footer.php'; ?>