<?php
$categorized = $categorized ?? [];
$activeDifficulty = $activeDifficulty ?? 'all';
$totalQuizzesCount = 0;
foreach ($categorized as $quizzesList) {
    $totalQuizzesCount += count($quizzesList);
}

require_once dirname(__DIR__) . '/templates/header.php';
?>

<div class="quiz-catalog-page-container">

    <!-- 1. Hero & Breadcrumb Header -->
    <section class="dashboard-hero-header">
        <div class="hero-brand-group">
            <div class="hero-title-area">
                <?= renderBreadcrumb([
                    ['label' => 'Student', 'url' => BASE_URL . '/'],
                    ['label' => 'Kuis']
                ]) ?>
            </div>
        </div>
    </section>

    <!-- 2. Filter Bar: Difficulty Switcher -->
    <div class="quiz-filter-bar">
        <div class="filter-tabs-group">
            <span class="filter-label font-mono">Tingkat Kesulitan:</span>
            <a href="<?= BASE_URL ?>/quiz?difficulty=all"
                class="quiz-segment-tab font-mono <?= $activeDifficulty === 'all' ? 'active' : '' ?>"
                onclick="window.playPixelSound && window.playPixelSound('click');">
                <span>Semua</span>
            </a>
            <a href="<?= BASE_URL ?>/quiz?difficulty=Mudah"
                class="quiz-segment-tab font-mono <?= $activeDifficulty === 'Mudah' ? 'active' : '' ?>"
                onclick="window.playPixelSound && window.playPixelSound('click');">
                <span>Mudah</span>
            </a>
            <a href="<?= BASE_URL ?>/quiz?difficulty=Sedang"
                class="quiz-segment-tab font-mono <?= $activeDifficulty === 'Sedang' ? 'active' : '' ?>"
                onclick="window.playPixelSound && window.playPixelSound('click');">
                <span>Sedang</span>
            </a>
            <a href="<?= BASE_URL ?>/quiz?difficulty=Sulit"
                class="quiz-segment-tab font-mono <?= $activeDifficulty === 'Sulit' ? 'active' : '' ?>"
                onclick="window.playPixelSound && window.playPixelSound('click');">
                <span>Sulit</span>
            </a>
        </div>

        <div class="font-mono text-xs text-zinc-500">
            <span>Total: <strong class="text-zinc-200"><?= $totalQuizzesCount ?></strong> Kuis Tersedia</span>
        </div>
    </div>

    <!-- 3. Categorized Quizzes Sections -->
    <?php if ($totalQuizzesCount === 0): ?>
        <div class="dashboard-panel-box text-center py-12">
            <span class="panel-crosshair corner-tl">+</span>
            <span class="panel-crosshair corner-tr">+</span>
            <span class="panel-crosshair corner-bl">+</span>
            <span class="panel-crosshair corner-br">+</span>

            <div class="w-12 h-12 rounded-lg bg-zinc-900 border border-zinc-800 flex items-center justify-center mx-auto mb-3 text-zinc-500">
                <svg class="w-6 h-6 pixelated" viewBox="0 0 16 16">
                    <use href="#pixel-router"></use>
                </svg>
            </div>
            <h3 class="text-base font-bold text-white mb-1">Tidak Ada Kuis Ditemukan</h3>
            <p class="font-mono text-xs text-zinc-500 max-w-md mx-auto">
                Belum ada paket soal yang cocok dengan filter tingkat kesulitan yang dipilih.
            </p>
            <div class="mt-4">
                <a href="<?= BASE_URL ?>/quiz?difficulty=all" class="btn-hero-primary font-mono text-xs" onclick="window.playPixelSound && window.playPixelSound('click');">
                    <span>Tampilkan Semua Kuis</span>
                </a>
            </div>
        </div>
    <?php else: ?>
        <div class="space-y-10">
            <?php foreach ($categorized as $categoryName => $quizzes): ?>
                <?php if (!empty($quizzes)):
                    $catThemeColor = match ($categoryName) {
                        'Routing' => '#38bdf8',
                        'Firewall & NAT' => '#fbbf24',
                        'Wireless' => '#f472b6',
                        'Network Management' => '#34d399',
                        default => '#0070f3'
                    };
                    $catIconSvg = match ($categoryName) {
                        'Routing' => '#pixel-router',
                        'Firewall & NAT' => '#pixel-robot',
                        'Wireless' => '#pixel-sparkle',
                        'Network Management' => '#pixel-computer',
                        default => '#pixel-book'
                    };
                ?>
                    <section class="quiz-category-section" aria-labelledby="cat-heading-<?= md5($categoryName) ?>">
                        <!-- Category Section Header -->
                        <div class="category-header-row">
                            <div class="category-title-wrap">
                                <div class="category-icon-box" style="border-color: <?= $catThemeColor ?>44; background-color: <?= $catThemeColor ?>14;">
                                    <svg class="w-5 h-5 pixelated" viewBox="0 0 16 16" style="color: <?= $catThemeColor ?>;">
                                        <use href="<?= $catIconSvg ?>"></use>
                                    </svg>
                                </div>
                                <h2 id="cat-heading-<?= md5($categoryName) ?>" class="category-title font-sans">
                                    <?= htmlspecialchars($categoryName) ?>
                                </h2>
                            </div>
                            <span class="category-count-badge font-mono">
                                <?= count($quizzes) ?> Paket Kuis
                            </span>
                        </div>

                        <!-- Quizzes Grid -->
                        <div class="quiz-catalog-grid">
                            <?php foreach ($quizzes as $q): ?>
                                <?php
                                $isFinished = !empty($q['is_completed']) || !empty($q['completed']);
                                $isPaused = !empty($q['is_paused']) || !empty($q['paused']);
                                $userScore = $q['score'] ?? null;
                                ?>
                                <div class="quiz-card-box" style="--card-hover-border: <?= $catThemeColor ?>; --card-glow: <?= $catThemeColor ?>33;">
                                    <span class="panel-crosshair corner-tl">+</span>
                                    <span class="panel-crosshair corner-tr">+</span>
                                    <span class="panel-crosshair corner-bl">+</span>
                                    <span class="panel-crosshair corner-br">+</span>

                                    <!-- Pixel Watermark SVG -->
                                    <svg class="quiz-watermark pixelated" viewBox="0 0 16 16">
                                        <use href="<?= $catIconSvg ?>"></use>
                                    </svg>

                                    <div class="quiz-card-content">
                                        <!-- Top Meta Badges -->
                                        <div class="quiz-card-top-row font-mono">
                                            <span class="quiz-diff-pill">
                                                <?= htmlspecialchars($q['difficulty'] ?? 'Mudah') ?>
                                            </span>

                                            <?php if ($isFinished): ?>
                                                <span class="quiz-status-tag status-finished">
                                                    <svg class="w-3 h-3 pixelated inline-block" viewBox="0 0 16 16">
                                                        <use href="#pixel-sparkle"></use>
                                                    </svg>
                                                    <span>Selesai (<?= (int)$userScore ?> pts)</span>
                                                </span>
                                            <?php elseif ($isPaused): ?>
                                                <span class="quiz-status-tag status-paused">
                                                    <span>⏸ Dijeda</span>
                                                </span>
                                            <?php else: ?>
                                                <span class="quiz-status-tag status-unattempted">
                                                    <span>Belum Dikerjakan</span>
                                                </span>
                                            <?php endif; ?>
                                        </div>

                                        <!-- Quiz Title & Description -->
                                        <h3 class="quiz-card-title">
                                            <?= htmlspecialchars($q['title']) ?>
                                        </h3>
                                        <p class="quiz-card-desc">
                                            <?= htmlspecialchars($q['description'] ?? 'Uji pengetahuan mendalam konfigurasi RouterOS MikroTik.') ?>
                                        </p>
                                    </div>

                                    <!-- Card Footer: Duration, Questions & CTA Button -->
                                    <div class="quiz-card-footer">
                                        <div class="quiz-card-meta font-mono">
                                            <span class="meta-item">
                                                <svg class="w-3.5 h-3.5 pixelated" viewBox="0 0 16 16">
                                                    <use href="#pixel-router"></use>
                                                </svg>
                                                <span><?= (int)($q['duration'] ?? 15) ?> mnt</span>
                                            </span>
                                            <span class="meta-separator">•</span>
                                            <span class="meta-item">
                                                <span><?= (int)($q['question_count'] ?? 10) ?> soal</span>
                                            </span>
                                        </div>

                                        <div class="quiz-card-actions">
                                            <?php if ($isFinished): ?>
                                                <a href="<?= BASE_URL ?>/quiz/review/<?= (int)$q['id'] ?>"
                                                    class="btn-hero-secondary font-mono text-xs"
                                                    title="Review Pembahasan"
                                                    onclick="window.playPixelSound && window.playPixelSound('click');">
                                                    <span>Review</span>
                                                </a>
                                                <a href="<?= BASE_URL ?>/quiz/play/<?= (int)$q['id'] ?>"
                                                    class="btn-hero-primary font-mono text-xs"
                                                    title="Ulangi Ujian"
                                                    onclick="window.playPixelSound && window.playPixelSound('click');">
                                                    <span>Ulangi</span>
                                                </a>
                                            <?php elseif ($isPaused): ?>
                                                <a href="<?= BASE_URL ?>/quiz/play/<?= (int)$q['id'] ?>"
                                                    class="btn-hero-primary font-mono text-xs"
                                                    onclick="window.playPixelSound && window.playPixelSound('click');">
                                                    <span>Lanjutkan ▶</span>
                                                </a>
                                            <?php else: ?>
                                                <a href="<?= BASE_URL ?>/quiz/play/<?= (int)$q['id'] ?>"
                                                    class="btn-hero-primary font-mono text-xs"
                                                    onclick="window.playPixelSound && window.playPixelSound('click');">
                                                    <span>⚡ Mulai Kuis</span>
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </section>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

</div>

<?php require_once dirname(__DIR__) . '/templates/footer.php'; ?>