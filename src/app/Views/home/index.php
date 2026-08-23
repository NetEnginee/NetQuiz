<?php
$stats = $stats ?? [
    "completed_quizzes" => 0,
    "completion_rate" => 0,
    "total_score" => 0,
    "average_score" => 0,
    "categories" => ["Routing" => 0, "Firewall & NAT" => 0, "Wireless" => 0, "Network Management" => 0],
    "category_scores" => ["Routing" => 0, "Firewall & NAT" => 0, "Wireless" => 0, "Network Management" => 0],
    "recent_activities" => [],
    "unlocked_badges" => [],
    "locked_achievements" => [],
    "next_badge" => null
];

$unlockedBadges = $unlockedBadges ?? ($stats["unlocked_badges"] ?? []);
$badges = $badges ?? [];
$materials = $materials ?? [];

$completedQuizzes = (int)($stats["completed_quizzes"] ?? 0);
$averageScore = (int)($stats["average_score"] ?? 0);
$totalScore = (int)($stats["total_score"] ?? 0);
$unlockedCount = count($unlockedBadges);
$recentActivities = $stats["recent_activities"] ?? [];
$categories = $stats["categories"] ?? ["Routing" => 0, "Firewall & NAT" => 0, "Wireless" => 0, "Network Management" => 0];

$totalCatAttempts = array_sum($categories);

require_once dirname(__DIR__) . "/templates/header.php";
?>

<div class="dashboard-viewport-wrapper">

    <!-- 1. Hero & Breadcrumb Bar -->
    <section class="dashboard-hero-header">
        <div class="hero-brand-group">
            <div class="hero-router-box">
                <svg class="w-7 h-7 pixelated" viewBox="0 0 16 16">
                    <use href="#pixel-router"></use>
                </svg>
                <span class="live-radar-dot"></span>
            </div>
            <div class="hero-title-area">
                <?= renderBreadcrumb([
                    ['label' => 'Student', 'url' => BASE_URL . '/'],
                    ['label' => 'Dashboard']
                ]) ?>
                <h1 class="hero-main-title">
                    <span>NetQuiz</span><span class="hero-cursor">_</span>
                    <span class="hero-pill-version">v6.0-PRO</span>
                    <span class="hero-pill-status">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                        <span>RouterOS v7.x Ready</span>
                    </span>
                </h1>
            </div>
        </div>

        <div class="hero-action-bar">
            <a href="<?= BASE_URL ?>/quiz" class="btn-hero-primary font-mono" onclick="window.playPixelSound && window.playPixelSound('click');">
                <span>⚡ Mulai Kuis</span>
            </a>
            <a href="<?= BASE_URL ?>/learn" class="btn-hero-secondary font-mono" onclick="window.playPixelSound && window.playPixelSound('click');">
                <span>📚 Materi</span>
            </a>
            <a href="<?= BASE_URL ?>/leaderboard" class="btn-hero-secondary font-mono" onclick="window.playPixelSound && window.playPixelSound('click');">
                <span>🏆 Leaderboard</span>
            </a>
        </div>
    </section>

    <!-- 2. Top 4 Metrics Grid (Vercel Dark Glow Cards with Watermarks) -->
    <section class="pixel-metrics-grid" aria-label="Ringkasan Statistik">

        <!-- Metric 1: Quizzes Completed (Blue Glow) -->
        <div class="pixel-metric-card glow-blue">
            <span class="card-crosshair corner-tl">+</span>
            <span class="card-crosshair corner-tr">+</span>
            <span class="card-crosshair corner-bl">+</span>
            <span class="card-crosshair corner-br">+</span>

            <svg class="metric-watermark pixelated" viewBox="0 0 16 16" aria-hidden="true">
                <use href="#pixel-router"></use>
            </svg>

            <div class="metric-header-row">
                <span class="metric-label">Quizzes Completed</span>
                <div class="metric-icon-badge icon-badge-blue">
                    <svg class="w-5 h-5 pixelated" viewBox="0 0 16 16">
                        <use href="#pixel-router"></use>
                    </svg>
                </div>
            </div>

            <div class="metric-value-row">
                <span class="metric-main-number"><?= $completedQuizzes ?></span>
                <span class="metric-unit-text">Completed</span>
            </div>

            <div class="metric-sub-note text-emerald-400">
                <span>↑ Active</span>
                <span class="text-zinc-500">learning track</span>
            </div>
        </div>

        <!-- Metric 2: Average Score (Cyan Glow) -->
        <div class="pixel-metric-card glow-emerald">
            <span class="card-crosshair corner-tl">+</span>
            <span class="card-crosshair corner-tr">+</span>
            <span class="card-crosshair corner-bl">+</span>
            <span class="card-crosshair corner-br">+</span>

            <svg class="metric-watermark pixelated" viewBox="0 0 16 16" aria-hidden="true">
                <use href="#pixel-computer"></use>
            </svg>

            <div class="metric-header-row">
                <span class="metric-label">Average Score</span>
                <div class="metric-icon-badge icon-badge-emerald">
                    <svg class="w-5 h-5 pixelated" viewBox="0 0 16 16">
                        <use href="#pixel-computer"></use>
                    </svg>
                </div>
            </div>

            <div class="metric-value-row">
                <span class="metric-main-number"><?= $averageScore ?>%</span>
                <span class="metric-unit-text">Accuracy</span>
            </div>

            <div class="metric-sub-note <?= $averageScore >= 70 ? 'text-emerald-400' : 'text-amber-400' ?>">
                <span><?= $averageScore >= 70 ? '↑ Passed' : '⚡ Keep Practicing' ?></span>
                <span class="text-zinc-500">overall grade</span>
            </div>
        </div>

        <!-- Metric 3: Total Points (Gold Glow) -->
        <div class="pixel-metric-card glow-gold">
            <span class="card-crosshair corner-tl">+</span>
            <span class="card-crosshair corner-tr">+</span>
            <span class="card-crosshair corner-bl">+</span>
            <span class="card-crosshair corner-br">+</span>

            <svg class="metric-watermark pixelated" viewBox="0 0 16 16" aria-hidden="true">
                <use href="#pixel-coin"></use>
            </svg>

            <div class="metric-header-row">
                <span class="metric-label">Total Points</span>
                <div class="metric-icon-badge icon-badge-gold">
                    <svg class="w-5 h-5 pixelated animate-float" viewBox="0 0 16 16">
                        <use href="#pixel-coin"></use>
                    </svg>
                </div>
            </div>

            <div class="metric-value-row">
                <span class="metric-main-number text-amber-400"><?= number_format($totalScore) ?></span>
                <span class="metric-unit-text">Pts</span>
            </div>

            <div class="metric-sub-note text-amber-400">
                <span>★ <?= $totalScore >= 500 ? 'Tier 3 Expert' : ($totalScore >= 200 ? 'Tier 2 Tech' : 'Tier 1 Operator') ?></span>
                <span class="text-zinc-500">standing</span>
            </div>
        </div>

        <!-- Metric 4: Achievements (Purple Glow) -->
        <div class="pixel-metric-card glow-purple">
            <span class="card-crosshair corner-tl">+</span>
            <span class="card-crosshair corner-tr">+</span>
            <span class="card-crosshair corner-bl">+</span>
            <span class="card-crosshair corner-br">+</span>

            <svg class="metric-watermark pixelated" viewBox="0 0 16 16" aria-hidden="true">
                <use href="#pixel-robot"></use>
            </svg>

            <div class="metric-header-row">
                <span class="metric-label">Achievements</span>
                <div class="metric-icon-badge icon-badge-purple">
                    <svg class="w-5 h-5 pixelated" viewBox="0 0 16 16">
                        <use href="#pixel-robot"></use>
                    </svg>
                </div>
            </div>

            <div class="metric-value-row">
                <span class="metric-main-number"><?= $unlockedCount ?></span>
                <span class="metric-unit-text">/ <?= count($badges) ?> Unlocked</span>
            </div>

            <div class="metric-sub-note text-purple-400">
                <span><?= count($badges) > 0 ? (int)round(($unlockedCount / max(1, count($badges))) * 100) : 0 ?>%</span>
                <span class="text-zinc-500">collection progress</span>
            </div>
        </div>

    </section>

    <!-- 4. Main 12-Column Grid Area (7 Left / 5 Right) -->
    <div class="dashboard-main-columns">

        <!-- Left Panel (7 Cols): Exam Activity History -->
        <section class="dashboard-panel-box left-activity-panel" aria-labelledby="activity-panel-title">
            <span class="panel-crosshair corner-tl">+</span>
            <span class="panel-crosshair corner-tr">+</span>
            <span class="panel-crosshair corner-bl">+</span>
            <span class="panel-crosshair corner-br">+</span>

            <div class="panel-header-row">
                <div class="panel-title-wrap">
                    <h2 id="activity-panel-title" class="panel-title-text">Exam Activity History</h2>
                </div>
            </div>

            <!-- Activity List Items -->
            <div class="activity-list-container">
                <?php if (!empty($recentActivities)): ?>
                    <?php foreach ($recentActivities as $act):
                        $score = (int)($act['score'] ?? 0);
                        $isPassed = $score >= 70;
                        $cat = $act['category'] ?? 'Routing';
                        $themeClass = match ($cat) {
                            'Firewall & NAT' => 'theme-amber',
                            'Wireless' => 'theme-purple',
                            'Network Management' => 'theme-cyan',
                            default => 'theme-emerald'
                        };
                        $attemptId = $act['quiz_id'] ?? $act['id'];
                    ?>
                        <div class="activity-row-item <?= $themeClass ?>">
                            <div class="activity-score-badge">
                                <span class="activity-score-number"><?= $score ?></span>
                            </div>

                            <div class="activity-info-box">
                                <h3 class="activity-quiz-title" title="<?= htmlspecialchars($act['quiz_title'] ?? 'Kuis RouterOS') ?>">
                                    <?= htmlspecialchars($act['quiz_title'] ?? 'Kuis RouterOS') ?>
                                </h3>
                                <div class="activity-meta-row font-mono">
                                    <span class="vercel-topic-tag">
                                        <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
                                        <span><?= htmlspecialchars($cat) ?></span>
                                    </span>
                                    <span>• <?= date('d M Y, H:i', strtotime($act['created_at'] ?? 'now')) ?></span>
                                </div>
                            </div>

                            <div class="activity-right-side font-mono">
                                <span class="activity-pts-pill">+<?= $score ?> pts</span>
                                <a href="<?= BASE_URL ?>/quiz/review/<?= $attemptId ?>" class="activity-action-pill" onclick="window.playPixelSound && window.playPixelSound('click');">
                                    <span><?= $isPassed ? 'Passed ★' : 'Review' ?></span>
                                    <span class="activity-action-arrow">→</span>
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <!-- Empty State -->
                    <div class="activity-empty-state font-mono">
                        <div class="empty-icon-box">
                            <svg class="w-10 h-10 pixelated" viewBox="0 0 16 16">
                                <use href="#pixel-computer"></use>
                            </svg>
                        </div>
                        <h4 class="empty-title">Belum Ada Riwayat Ujian</h4>
                        <p class="empty-desc">Selesaikan kuis pertama Anda untuk mulai mengumpulkan poin dan mencatat riwayat evaluasi.</p>
                        <a href="<?= BASE_URL ?>/quiz" class="btn-hero-primary font-mono text-xs mt-3" onclick="window.playPixelSound && window.playPixelSound('coin');">
                            <span>⚡ Ambil Kuis Sekarang</span>
                        </a>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Activity Footer -->
            <div class="activity-panel-footer font-mono">
                <span class="text-zinc-500">Menampilkan <?= count($recentActivities) ?> riwayat pengerjaan</span>
                <a href="<?= BASE_URL ?>/quiz" class="text-cyan-400 hover:text-cyan-300 transition flex items-center gap-1">
                    <span>Semua Kuis</span>
                    <span>→</span>
                </a>
            </div>
        </section>

        <!-- Right Panels (5 Cols) -->
        <div class="right-dashboard-column">

            <!-- Topic Distribution Panel -->
            <section class="dashboard-panel-box" aria-labelledby="topic-panel-title">
                <span class="panel-crosshair corner-tl">+</span>
                <span class="panel-crosshair corner-tr">+</span>
                <span class="panel-crosshair corner-bl">+</span>
                <span class="panel-crosshair corner-br">+</span>

                <div class="panel-header-row">
                    <div class="panel-title-wrap">
                        <div class="w-2.5 h-2.5 bg-blue-500 rounded-xs"></div>
                        <h2 id="topic-panel-title" class="panel-title-text">Exam Topic Distribution</h2>
                    </div>
                    <span class="font-mono text-xs text-zinc-500">4 Kategori</span>
                </div>

                <!-- Topic Progress Bars -->
                <div class="topic-progress-container">
                    <?php
                    $topicsConfig = [
                        'Routing' => ['color' => '#38bdf8', 'bg' => 'rgba(56, 189, 248, 0.2)'],
                        'Firewall & NAT' => ['color' => '#fbbf24', 'bg' => 'rgba(251, 191, 36, 0.2)'],
                        'Wireless' => ['color' => '#f472b6', 'bg' => 'rgba(244, 114, 182, 0.2)'],
                        'Network Management' => ['color' => '#34d399', 'bg' => 'rgba(52, 211, 153, 0.2)']
                    ];

                    foreach ($topicsConfig as $topName => $topConf):
                        $cCount = (int)($categories[$topName] ?? 0);
                        $cPercent = $totalCatAttempts > 0 ? (int)round(($cCount / $totalCatAttempts) * 100) : 0;
                    ?>
                        <div class="topic-progress-item">
                            <div class="topic-progress-header font-mono">
                                <span class="topic-name-wrap">
                                    <span class="topic-dot" style="background-color: <?= $topConf['color'] ?>;"></span>
                                    <span><?= htmlspecialchars($topName) ?></span>
                                </span>
                                <span class="topic-stats-text"><?= $cCount ?> Kuis (<?= $cPercent ?>%)</span>
                            </div>
                            <div class="topic-progress-track">
                                <div class="topic-progress-fill" style="width: <?= $cPercent ?>%; background-color: <?= $topConf['color'] ?>;"></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>

            <!-- Unlocked Badges Panel -->
            <section class="dashboard-panel-box mt-5" aria-labelledby="badges-panel-title">
                <span class="panel-crosshair corner-tl">+</span>
                <span class="panel-crosshair corner-tr">+</span>
                <span class="panel-crosshair corner-bl">+</span>
                <span class="panel-crosshair corner-br">+</span>

                <div class="panel-header-row">
                    <div class="panel-title-wrap">
                        <div class="w-2.5 h-2.5 bg-amber-400 rounded-xs"></div>
                        <h2 id="badges-panel-title" class="panel-title-text">Unlocked Badges</h2>
                    </div>
                    <span class="font-mono text-xs text-amber-400 font-bold"><?= $unlockedCount ?> Terbuka</span>
                </div>

                <!-- Badges Grid -->
                <div class="badges-grid-container">
                    <?php if (!empty($badges)): ?>
                        <?php foreach (array_slice($badges, 0, 8) as $b):
                            $isBUnlocked = !empty($b['unlocked']);
                            $bSvgId = match ($b['icon'] ?? '') {
                                'router' => '#pixel-router',
                                'computer' => '#pixel-computer',
                                'coin' => '#pixel-coin',
                                'book' => '#pixel-book',
                                default => '#pixel-robot'
                            };
                        ?>
                            <div class="pixel-badge-card <?= $isBUnlocked ? 'badge-unlocked' : 'badge-locked' ?>"
                                title="<?= htmlspecialchars($b['title']) ?>: <?= htmlspecialchars($b['description']) ?>"
                                onclick="window.playPixelSound && window.playPixelSound('<?= $isBUnlocked ? 'badge' : 'click' ?>');">
                                <div class="badge-icon-box">
                                    <svg class="w-7 h-7 pixelated" viewBox="0 0 16 16">
                                        <use href="<?= $bSvgId ?>"></use>
                                    </svg>
                                </div>
                                <span class="badge-card-title font-mono"><?= htmlspecialchars($b['title']) ?></span>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="font-mono text-xs text-zinc-500 py-3 text-center col-span-4">
                            Belum ada lencana terdaftar.
                        </div>
                    <?php endif; ?>
                </div>
            </section>

        </div>

    </div>

    <!-- 5. Latest Learning Materials Section -->
    <section class="dashboard-panel-box materials-dashboard-section mt-6" aria-labelledby="materials-panel-title">
        <span class="panel-crosshair corner-tl">+</span>
        <span class="panel-crosshair corner-tr">+</span>
        <span class="panel-crosshair corner-bl">+</span>
        <span class="panel-crosshair corner-br">+</span>

        <div class="panel-header-row">
            <div class="panel-title-wrap">
                <div>
                    <h2 id="materials-panel-title" class="panel-title-text">Newest Learning Materials</h2>
                </div>
            </div>
        </div>

        <!-- Materials Cards Grid -->
        <div class="materials-grid-container">
            <?php if (!empty($materials)): ?>
                <?php foreach ($materials as $mat):
                    $mCat = $mat['category'] ?? 'Routing';
                    $mDifficulty = $mat['difficulty'] ?? 'Mudah';
                    $mSnippet = !empty($mat['content']) ? mb_strimwidth(strip_tags($mat['content']), 0, 110, '...') : 'Panduan komprehensif konfigurasi RouterOS MikroTik.';
                    $themeCatClass = match ($mCat) {
                        'Firewall & NAT' => 'cat-theme-amber',
                        'Wireless' => 'cat-theme-pink',
                        'Network Management' => 'cat-theme-emerald',
                        default => 'cat-theme-cyan'
                    };
                ?>
                    <a href="<?= BASE_URL ?>/learn/<?= (int)$mat['id'] ?>" class="material-item-card <?= $themeCatClass ?>" onclick="window.playPixelSound && window.playPixelSound('click');">
                        <div class="material-card-body">
                            <div class="material-card-top-row font-mono">
                                <span class="material-cat-tag">
                                    <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
                                    <span><?= htmlspecialchars($mCat) ?></span>
                                </span>
                                <span class="material-read-time">~2 min read</span>
                            </div>

                            <h3 class="material-card-title"><?= htmlspecialchars($mat['title']) ?></h3>

                            <p class="material-card-snippet"><?= htmlspecialchars($mSnippet) ?></p>

                            <div class="material-card-footer font-mono">
                                <span class="material-diff-badge"><?= htmlspecialchars($mDifficulty) ?></span>
                                <span class="material-cta-text">
                                    <span>Baca Materi</span>
                                    <span class="cta-arrow">→</span>
                                </span>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="font-mono text-xs text-zinc-500 py-4 text-center col-span-2">
                    Belum ada materi pembelajaran yang dipublikasikan.
                </div>
            <?php endif; ?>
        </div>
    </section>

</div>

<?php
require_once dirname(__DIR__) . "/templates/footer.php";
?>