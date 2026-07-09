<?php require_once dirname(__DIR__) . '/templates/header.php'; ?>

<!-- Custom Styles for Leaderboard -->
<link rel="stylesheet" href="<?= BASE_URL ?>/css/leaderboard.css?v=<?= time() ?>">

<div class="leaderboard-container">
    <nav class="breadcrumb"
        style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 2rem; font-size: 0.85rem; font-weight: 500; color: #64748b; font-family: 'Plus Jakarta Sans', sans-serif;">
        <span style="color: #64748b;">Dashboard</span>
        <span style="color: #cbd5e1;">/</span>
        <span style="color: #0f172a; font-weight: 600;">Leaderboard</span>
    </nav>

    <!-- Category Filter Pills -->
    <div class="categories-filter-wrapper">
        <div class="categories-filter-pills">
            <a href="<?= BASE_URL ?>/leaderboard?category=all"
                class="category-pill <?= $activeCategory === 'all' ? 'active' : '' ?>">Semua Kategori</a>
            <a href="<?= BASE_URL ?>/leaderboard?category=Routing"
                class="category-pill <?= $activeCategory === 'Routing' ? 'active' : '' ?>">Routing</a>
            <a href="<?= BASE_URL ?>/leaderboard?category=Firewall%20%26%20NAT"
                class="category-pill <?= $activeCategory === 'Firewall & NAT' ? 'active' : '' ?>">Firewall & NAT</a>
            <a href="<?= BASE_URL ?>/leaderboard?category=Wireless"
                class="category-pill <?= $activeCategory === 'Wireless' ? 'active' : '' ?>">Wireless</a>
            <a href="<?= BASE_URL ?>/leaderboard?category=Network%20Management"
                class="category-pill <?= $activeCategory === 'Network Management' ? 'active' : '' ?>">Network
                Management</a>
        </div>
    </div>

    <!-- Current User Position Widget -->
    <?php if ($currentUserStats): ?>
        <div class="user-position-card">
            <div class="user-pos-left">
                <div class="user-pos-rank">
                    <span class="user-pos-label">Posisi Anda</span>
                    <span class="user-pos-value">#<?= $currentUserRank ?></span>
                </div>

            </div>
            <div class="user-pos-right">
                <div class="user-pos-stat">
                    <span class="user-pos-stat-label">Kuis Selesai</span>
                    <span class="user-pos-stat-value">
                        <?= htmlspecialchars($currentUserStats['completed_quizzes']) ?>
                    </span>
                </div>
                <div class="user-pos-stat">
                    <span class="user-pos-stat-label">Total Skor</span>
                    <span class="user-pos-stat-value"><?= number_format($currentUserStats['total_score']) ?> Pts</span>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Top 10 Rankings List -->
    <?php if (empty($leaderboard)): ?>
        <div class="empty-state-box">
            <p class="empty-text">Belum ada aktivitas skor kuis untuk kategori ini.</p>
        </div>
    <?php else: ?>
        <div class="rankings-card">
            <?php
            $rankNum = 1;
            foreach ($leaderboard as $user):
                $isCurrentUser = ($user['id'] == $_SESSION['user']['id']);
                $initial = strtoupper(substr(htmlspecialchars($user['username']), 0, 1));
                ?>
                <div class="ranking-item <?= $isCurrentUser ? 'ranking-item-current' : '' ?>">
                    <div class="ranking-left">
                        <div class="ranking-badge rank-<?= $rankNum ?>">
                            #<?= $rankNum ?>
                        </div>
                        <div class="ranking-user" style="display: flex; align-items: center; gap: 0.5rem; min-width: 0;">
                            <span class="ranking-username"
                                style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                <?= htmlspecialchars($user['username']) ?>
                            </span>
                            <?php if ($isCurrentUser): ?>
                                <span class="ranking-you" style="flex-shrink: 0;">Anda</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="ranking-right">
                        <div class="ranking-score"><?= number_format($user['total_score']) ?> <span class="pts-label">Pts</span>
                        </div>
                    </div>
                </div>
                <?php
                $rankNum++;
            endforeach;
            ?>
        </div>
    <?php endif; ?>
</div>

<?php require_once dirname(__DIR__) . '/templates/footer.php'; ?>