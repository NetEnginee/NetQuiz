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
            <button type="button"
                class="quiz-segment-tab font-mono <?= $activeDifficulty === 'all' ? 'active' : '' ?>"
                data-difficulty="all">
                <span>Semua</span>
            </button>
            <button type="button"
                class="quiz-segment-tab font-mono <?= $activeDifficulty === 'Mudah' ? 'active' : '' ?>"
                data-difficulty="Mudah">
                <span>Mudah</span>
            </button>
            <button type="button"
                class="quiz-segment-tab font-mono <?= $activeDifficulty === 'Sedang' ? 'active' : '' ?>"
                data-difficulty="Sedang">
                <span>Sedang</span>
            </button>
            <button type="button"
                class="quiz-segment-tab font-mono <?= $activeDifficulty === 'Sulit' ? 'active' : '' ?>"
                data-difficulty="Sulit">
                <span>Sulit</span>
            </button>
        </div>
    </div>

    <!-- 3. Categorized Quizzes Sections -->
    <?php if ($totalQuizzesCount === 0): ?>
        <div class="pixel-empty-state empty-panel-full empty-theme-cyan">
            <span class="panel-crosshair corner-tl">+</span>
            <span class="panel-crosshair corner-tr">+</span>
            <span class="panel-crosshair corner-bl">+</span>
            <span class="panel-crosshair corner-br">+</span>

            <div class="empty-scene">
                <div class="sparkle-orbit">
                    <span class="sparkle-dot"></span>
                    <span class="sparkle-dot"></span>
                    <span class="sparkle-dot"></span>
                    <span class="sparkle-dot"></span>
                </div>
                <div class="empty-main-icon">
                    <svg class="w-10 h-10 pixelated" viewBox="0 0 16 16">
                        <use href="#pixel-router"></use>
                    </svg>
                </div>
            </div>
            <h3 class="empty-headline">Belum Ada Kuis</h3>
            <p class="empty-subtext">Paket kuis sedang dalam Layer 4, tunggu beberapa saat sampai tiba di Layer 7!</p>
            <button type="button" class="empty-cta-link" id="btn-reset-quiz-filter" style="cursor: pointer; border: none; background: transparent;">
                <span>⚡ Tampilkan Semua Kuis</span>
                <span class="cta-arrow">→</span>
            </button>
        </div>
    <?php else: ?>
        <!-- Dynamic Empty State for Realtime Filter (No matches) -->
        <div id="quiz-filter-empty-state" class="pixel-empty-state empty-panel-full empty-theme-cyan" style="display: none;">
            <span class="panel-crosshair corner-tl">+</span>
            <span class="panel-crosshair corner-tr">+</span>
            <span class="panel-crosshair corner-bl">+</span>
            <span class="panel-crosshair corner-br">+</span>

            <div class="empty-scene">
                <div class="empty-main-icon">
                    <svg class="w-10 h-10 pixelated" viewBox="0 0 16 16">
                        <use href="#pixel-router"></use>
                    </svg>
                </div>
            </div>
            <h3 class="empty-headline">Tidak Ada Kuis Ditemukan</h3>
            <p class="empty-subtext">Belum ada paket kuis dengan tingkat kesulitan ini.</p>
            <button type="button" class="empty-cta-link" id="btn-reset-quiz-filter" style="cursor: pointer; border: none; background: transparent;">
                <span>⚡ Tampilkan Semua Kuis</span>
                <span class="cta-arrow">→</span>
            </button>
        </div>

        <div class="space-y-10" id="quiz-sections-container">
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
                    <section class="quiz-category-section" data-category="<?= htmlspecialchars($categoryName) ?>" aria-labelledby="cat-heading-<?= md5($categoryName) ?>">
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
                                $quizDifficulty = strtolower($q['difficulty'] ?? 'mudah');
                                ?>
                                <div class="quiz-card-box" data-difficulty="<?= htmlspecialchars($quizDifficulty) ?>" style="--card-hover-border: <?= $catThemeColor ?>; --card-glow: <?= $catThemeColor ?>33;">
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
                                                    <span>Mulai Kuis</span>
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

<!-- Quiz Catalog Realtime Filter Module -->
<script src="<?= function_exists('assetUrl') ? assetUrl('/js/quiz-catalog.js') : (BASE_URL . '/js/quiz-catalog.js') ?>"></script>

<?php require_once dirname(__DIR__) . '/templates/footer.php'; ?>