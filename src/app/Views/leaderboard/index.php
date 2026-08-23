<?php
$activeCategory = $activeCategory ?? 'all';
$currentUserStats = $currentUserStats ?? null;
$currentUserRank = $currentUserRank ?? 0;
$leaderboard = $leaderboard ?? [];
$currentUserId = (int)($_SESSION['user']['id'] ?? 0);

$categoryThemes = [
    'Routing' => ['color' => '#60a5fa'],
    'Firewall & NAT' => ['color' => '#fbbf24'],
    'Wireless' => ['color' => '#f472b6'],
    'Network Management' => ['color' => '#50e3c2']
];

$categoryDisplayNames = [
    'all' => 'Semua Topik',
    'Routing' => 'Routing',
    'Firewall & NAT' => 'Firewall & NAT',
    'Wireless' => 'Wireless',
    'Network Management' => 'Network Management'
];

$activeCategoryLabel = $categoryDisplayNames[$activeCategory] ?? 'Semua Topik';

// Extract Top 3 for Podium Stage Gimmick
$rank1 = $leaderboard[0] ?? null;
$rank2 = $leaderboard[1] ?? null;
$rank3 = $leaderboard[2] ?? null;

require_once dirname(__DIR__) . '/templates/header.php';
?>

<div class="leaderboard-page-container pt-2">

    <!-- 1. Breadcrumb Navigation -->
    <div class="mb-4 max-w-full overflow-hidden">
        <?= renderBreadcrumb([
            ['label' => 'Student', 'url' => BASE_URL . '/'],
            ['label' => 'Leaderboard', 'url' => BASE_URL . '/leaderboard'],
            ['label' => $activeCategoryLabel]
        ]) ?>
    </div>

    <!-- 2. Category Filter Switcher Toolbar -->
    <div class="leaderboard-toolbar-panel">
        <span class="panel-crosshair corner-tl">+</span>
        <span class="panel-crosshair corner-tr">+</span>
        <span class="panel-crosshair corner-bl">+</span>
        <span class="panel-crosshair corner-br">+</span>

        <div class="leaderboard-filter-tabs">
            <a href="<?= BASE_URL ?>/leaderboard?category=all"
                class="leaderboard-segment-tab font-mono <?= $activeCategory === 'all' ? 'active' : '' ?>"
                onclick="window.playPixelSound && window.playPixelSound('click');">
                <span>Semua Topik</span>
            </a>
            <a href="<?= BASE_URL ?>/leaderboard?category=Routing"
                class="leaderboard-segment-tab font-mono <?= $activeCategory === 'Routing' ? 'active' : '' ?>"
                onclick="window.playPixelSound && window.playPixelSound('click');">
                <span class="w-1.5 h-1.5 rounded-full inline-block" style="background-color: #60a5fa;"></span>
                <span>Routing</span>
            </a>
            <a href="<?= BASE_URL ?>/leaderboard?category=Firewall%20%26%20NAT"
                class="leaderboard-segment-tab font-mono <?= $activeCategory === 'Firewall & NAT' ? 'active' : '' ?>"
                onclick="window.playPixelSound && window.playPixelSound('click');">
                <span class="w-1.5 h-1.5 rounded-full inline-block" style="background-color: #fbbf24;"></span>
                <span>Firewall & NAT</span>
            </a>
            <a href="<?= BASE_URL ?>/leaderboard?category=Wireless"
                class="leaderboard-segment-tab font-mono <?= $activeCategory === 'Wireless' ? 'active' : '' ?>"
                onclick="window.playPixelSound && window.playPixelSound('click');">
                <span class="w-1.5 h-1.5 rounded-full inline-block" style="background-color: #f472b6;"></span>
                <span>Wireless</span>
            </a>
            <a href="<?= BASE_URL ?>/leaderboard?category=Network%20Management"
                class="leaderboard-segment-tab font-mono <?= $activeCategory === 'Network Management' ? 'active' : '' ?>"
                onclick="window.playPixelSound && window.playPixelSound('click');">
                <span class="w-1.5 h-1.5 rounded-full inline-block" style="background-color: #50e3c2;"></span>
                <span>Network Management</span>
            </a>
        </div>
    </div>

    <!-- 3. TOP 3 PODIUM STAGE GIMMICK (PANGGUNG KEJUARAAN) -->
    <section class="podium-stage-panel">
        <span class="panel-crosshair corner-tl">+</span>
        <span class="panel-crosshair corner-tr">+</span>
        <span class="panel-crosshair corner-bl">+</span>
        <span class="panel-crosshair corner-br">+</span>

        <!-- Podium Header -->
        <div class="podium-stage-header">
            <div class="podium-header-title-wrap">
                <div class="podium-trophy-box">
                    <svg class="w-5 h-5 pixelated text-amber-400" width="20" height="20" viewBox="0 0 16 16">
                        <use href="#pixel-coin"></use>
                    </svg>
                </div>
                <div>
                    <h2 class="podium-stage-title font-sans">
                        Panggung Juara Terbaik
                    </h2>
                </div>
            </div>
            <span class="podium-category-badge">
                <?= htmlspecialchars($activeCategoryLabel) ?>
            </span>
        </div>

        <?php if (empty($leaderboard)): ?>
            <div class="podium-empty-state font-mono text-xs">
                <svg class="w-10 h-10 pixelated text-zinc-700 mx-auto mb-2" width="40" height="40" viewBox="0 0 16 16">
                    <use href="#pixel-sparkle"></use>
                </svg>
                <p class="text-zinc-400">Belum ada aktivitas ujian kuis yang tercatat pada kategori ini.</p>
            </div>
        <?php else: ?>
            <!-- 3-Column Podium Stage -->
            <div class="podium-columns-container">

                <!-- 1. Silver / Juara 2 (Left Column) -->
                <div class="podium-column podium-rank-2">
                    <?php if ($rank2): ?>
                        <div class="podium-player-card">
                            <div class="podium-avatar-wrap">
                                <div class="podium-avatar">
                                    <?= strtoupper(substr(htmlspecialchars($rank2['username']), 0, 1)) ?>
                                </div>
                                <span class="podium-rank-badge">2nd</span>
                            </div>
                            <div class="podium-username" title="<?= htmlspecialchars($rank2['username']) ?>">
                                <?= htmlspecialchars($rank2['username']) ?>
                            </div>
                            <div class="podium-score">
                                <?= number_format((int)$rank2['total_score']) ?> <span class="text-[11px] font-normal text-zinc-400">Pts</span>
                            </div>
                            <div class="podium-quiz-count">
                                <?= (int)$rank2['completed_quizzes'] ?> Kuis Selesai
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="podium-player-card opacity-30">
                            <div class="podium-avatar-wrap">
                                <div class="podium-avatar border-zinc-700 text-zinc-600">-</div>
                                <span class="podium-rank-badge bg-zinc-800 text-zinc-400">2nd</span>
                            </div>
                            <div class="podium-username text-zinc-500">Kosong</div>
                        </div>
                    <?php endif; ?>
                    <div class="podium-pedestal-block pedestal-2">
                        <span class="podium-pedestal-number">2</span>
                    </div>
                </div>

                <!-- 2. Gold / Juara 1 (Center Column - Highest) -->
                <div class="podium-column podium-rank-1">
                    <?php if ($rank1): ?>
                        <div class="podium-player-card">
                            <div class="podium-avatar-wrap">
                                <div class="podium-crown-icon">
                                    <svg class="w-6 h-6 pixelated" width="24" height="24" viewBox="0 0 16 16">
                                        <use href="#pixel-coin"></use>
                                    </svg>
                                </div>
                                <div class="podium-avatar">
                                    <?= strtoupper(substr(htmlspecialchars($rank1['username']), 0, 1)) ?>
                                </div>
                                <span class="podium-rank-badge">1st</span>
                            </div>
                            <div class="podium-username" title="<?= htmlspecialchars($rank1['username']) ?>">
                                <?= htmlspecialchars($rank1['username']) ?>
                            </div>
                            <div class="podium-score">
                                <?= number_format((int)$rank1['total_score']) ?> <span class="text-[11px] font-normal text-amber-200/70">Pts</span>
                            </div>
                            <div class="podium-quiz-count">
                                <?= (int)$rank1['completed_quizzes'] ?> Kuis Selesai
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="podium-player-card opacity-30">
                            <div class="podium-avatar-wrap">
                                <div class="podium-avatar border-zinc-700 text-zinc-600">-</div>
                                <span class="podium-rank-badge bg-zinc-800 text-zinc-400">1st</span>
                            </div>
                            <div class="podium-username text-zinc-500">Kosong</div>
                        </div>
                    <?php endif; ?>
                    <div class="podium-pedestal-block pedestal-1">
                        <span class="podium-pedestal-number">1</span>
                    </div>
                </div>

                <!-- 3. Bronze / Juara 3 (Right Column) -->
                <div class="podium-column podium-rank-3">
                    <?php if ($rank3): ?>
                        <div class="podium-player-card">
                            <div class="podium-avatar-wrap">
                                <div class="podium-avatar">
                                    <?= strtoupper(substr(htmlspecialchars($rank3['username']), 0, 1)) ?>
                                </div>
                                <span class="podium-rank-badge">3rd</span>
                            </div>
                            <div class="podium-username" title="<?= htmlspecialchars($rank3['username']) ?>">
                                <?= htmlspecialchars($rank3['username']) ?>
                            </div>
                            <div class="podium-score">
                                <?= number_format((int)$rank3['total_score']) ?> <span class="text-[11px] font-normal text-zinc-400">Pts</span>
                            </div>
                            <div class="podium-quiz-count">
                                <?= (int)$rank3['completed_quizzes'] ?> Kuis Selesai
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="podium-player-card opacity-30">
                            <div class="podium-avatar-wrap">
                                <div class="podium-avatar border-zinc-700 text-zinc-600">-</div>
                                <span class="podium-rank-badge bg-zinc-800 text-zinc-400">3rd</span>
                            </div>
                            <div class="podium-username text-zinc-500">Kosong</div>
                        </div>
                    <?php endif; ?>
                    <div class="podium-pedestal-block pedestal-3">
                        <span class="podium-pedestal-number">3</span>
                    </div>
                </div>

            </div>
        <?php endif; ?>
    </section>

    <!-- 4. PERSONAL USER RANK CARD BANNER -->
    <div class="user-rank-status-card">
        <span class="panel-crosshair corner-tl">+</span>
        <span class="panel-crosshair corner-tr">+</span>
        <span class="panel-crosshair corner-bl">+</span>
        <span class="panel-crosshair corner-br">+</span>

        <div class="user-rank-left">
            <div class="user-rank-avatar font-mono">
                <?= strtoupper(substr(htmlspecialchars($_SESSION['user']['name'] ?? 'U'), 0, 1)) ?>
            </div>
            <div class="user-rank-info">
                <div class="user-rank-tagline">
                    <span>Posisi Anda Saat Ini</span>
                </div>
                <div class="user-rank-name">
                    <?= htmlspecialchars($_SESSION['user']['name'] ?? 'Siswa') ?>
                </div>
            </div>
        </div>

        <div class="user-rank-stats-group">
            <div class="user-stat-box">
                <span class="user-stat-label">Peringkat</span>
                <span class="user-stat-val cyan">
                    <?= $currentUserRank > 0 ? ('#' . $currentUserRank) : 'Unranked' ?>
                </span>
            </div>
            <div class="user-stat-box">
                <span class="user-stat-label">Total Akumulasi</span>
                <span class="user-stat-val gold">
                    <?= number_format((int)($currentUserStats['total_score'] ?? 0)) ?> <span class="text-xs font-normal text-zinc-400">Pts</span>
                </span>
            </div>
            <div class="user-stat-box">
                <span class="user-stat-label">Kuis Selesai</span>
                <span class="user-stat-val">
                    <?= (int)($currentUserStats['completed_quizzes'] ?? 0) ?>
                </span>
            </div>
        </div>
    </div>

    <!-- 5. RANKINGS TABLE CARD (Rank 4 onwards) -->
    <?php
    $tableLeaderboard = array_slice($leaderboard, 3);
    ?>
    <div class="leaderboard-table-panel">
        <span class="panel-crosshair corner-tl">+</span>
        <span class="panel-crosshair corner-tr">+</span>
        <span class="panel-crosshair corner-bl">+</span>
        <span class="panel-crosshair corner-br">+</span>

        <div class="table-panel-header">
            <h3 class="table-panel-title font-sans">
                Daftar Peringkat Peserta (#4 - #10)
            </h3>
            <span class="table-panel-meta">
                Kategori: <?= htmlspecialchars($activeCategoryLabel) ?>
            </span>
        </div>

        <?php if (empty($tableLeaderboard)): ?>
            <div class="p-8 text-center text-zinc-500 font-mono text-xs">
                Belum ada peserta tambahan di luar Top 3 untuk kategori ini.
            </div>
        <?php else: ?>
            <div class="leaderboard-table-wrap">
                <table class="leaderboard-data-table font-sans">
                    <thead>
                        <tr>
                            <th style="width: 75px; text-align: center;">Rank</th>
                            <th>Nama Siswa</th>
                            <th style="text-align: right; width: 140px;">Kuis Selesai</th>
                            <th style="text-align: right; width: 180px;">Total Akumulasi Skor</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $rankNum = 4;
                        foreach ($tableLeaderboard as $user):
                            $isCurrent = ((int)$user['id'] === $currentUserId);
                        ?>
                            <tr class="<?= $isCurrent ? 'current-user-row' : '' ?>">
                                <td style="text-align: center;">
                                    <span class="table-rank-badge rank-other">
                                        #<?= $rankNum ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="table-user-cell">
                                        <div class="table-avatar-circle font-mono">
                                            <?= strtoupper(substr(htmlspecialchars($user['username']), 0, 1)) ?>
                                        </div>
                                        <span class="table-username">
                                            <?= htmlspecialchars($user['username']) ?>
                                        </span>
                                        <?php if ($isCurrent): ?>
                                            <span class="badge-you-tag">Anda</span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td style="text-align: right;" class="font-mono text-zinc-400 text-xs">
                                    <?= (int)($user['completed_quizzes'] ?? 0) ?>
                                </td>
                                <td style="text-align: right;">
                                    <span class="table-score-cell">
                                        <?= number_format((int)$user['total_score']) ?>
                                    </span>
                                    <span class="table-pts-suffix font-mono">Pts</span>
                                </td>
                            </tr>
                        <?php
                            $rankNum++;
                        endforeach;
                        ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

</div>

<?php require_once dirname(__DIR__) . '/templates/footer.php'; ?>