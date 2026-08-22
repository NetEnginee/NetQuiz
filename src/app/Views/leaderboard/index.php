<?php
$activeCategory = $activeCategory ?? 'all';
$currentUserStats = $currentUserStats ?? null;
$currentUserRank = $currentUserRank ?? 0;
$leaderboard = $leaderboard ?? [];
$currentUserId = (int)($_SESSION['user']['id'] ?? 0);

require_once dirname(__DIR__) . '/templates/header.php';
?>

<!-- Breadcrumb & Header -->
<div style="margin-bottom: 2rem;">
    <nav class="admin-breadcrumb-nav" aria-label="Breadcrumb" style="margin-bottom: 0.5rem;">
        <span class="breadcrumb-item">Siswa</span>
        <span class="breadcrumb-separator">/</span>
        <span class="breadcrumb-active">Leaderboard</span>
    </nav>
    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h1 style="font-family: var(--font-heading); font-size: 1.5rem; font-weight: 800; color: #18181B; margin: 0 0 0.25rem 0; letter-spacing: -0.02em;">
                Papan Peringkat Prestasi (Top 10)
            </h1>
            <p style="font-size: 0.875rem; color: #71717A; margin: 0;">
                Akumulasi skor dan pencapaian terbaik simulasi sertifikasi MikroTik RouterOS.
            </p>
        </div>
    </div>
</div>

<!-- Category Filter Switcher -->
<div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap; margin-bottom: 1.5rem; padding: 0.75rem 1rem; background-color: #FFFFFF; border: 1px solid #E5E7EB; border-radius: 8px;">
    <span style="font-size: 0.8rem; font-weight: 700; color: #71717A; text-transform: uppercase; margin-right: 0.25rem;">Kategori:</span>
    <a href="<?= BASE_URL ?>/leaderboard?category=all" class="quiz-segment-tab <?= $activeCategory === 'all' ? 'active' : '' ?>" style="text-decoration: none; font-size: 0.8rem; padding: 0.35rem 0.75rem;">
        Semua Topik
    </a>
    <a href="<?= BASE_URL ?>/leaderboard?category=Routing" class="quiz-segment-tab <?= $activeCategory === 'Routing' ? 'active' : '' ?>" style="text-decoration: none; font-size: 0.8rem; padding: 0.35rem 0.75rem;">
        Routing
    </a>
    <a href="<?= BASE_URL ?>/leaderboard?category=Firewall%20%26%20NAT" class="quiz-segment-tab <?= $activeCategory === 'Firewall & NAT' ? 'active' : '' ?>" style="text-decoration: none; font-size: 0.8rem; padding: 0.35rem 0.75rem;">
        Firewall & NAT
    </a>
    <a href="<?= BASE_URL ?>/leaderboard?category=Wireless" class="quiz-segment-tab <?= $activeCategory === 'Wireless' ? 'active' : '' ?>" style="text-decoration: none; font-size: 0.8rem; padding: 0.35rem 0.75rem;">
        Wireless
    </a>
    <a href="<?= BASE_URL ?>/leaderboard?category=Network%20Management" class="quiz-segment-tab <?= $activeCategory === 'Network Management' ? 'active' : '' ?>" style="text-decoration: none; font-size: 0.8rem; padding: 0.35rem 0.75rem;">
        Network Management
    </a>
</div>

<!-- CURRENT USER POSITION CARD -->
<?php if ($currentUserStats): ?>
    <div class="supabase-panel-card" style="padding: 1.25rem 1.5rem; margin-bottom: 1.5rem; background-color: #18181B; color: #FFFFFF;">
        <span class="corner-crosshair corner-tl">+</span>
        <span class="corner-crosshair corner-tr">+</span>
        <span class="corner-crosshair corner-bl">+</span>
        <span class="corner-crosshair corner-br">+</span>

        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
            <div style="display: flex; align-items: center; gap: 1rem;">
                <div style="width: 44px; height: 44px; border-radius: 8px; background-color: #27272A; border: 1px solid #3F3F46; display: flex; align-items: center; justify-content: center; font-size: 1.25rem; font-weight: 800; font-family: var(--font-mono); color: #38BDF8;">
                    #<?= $currentUserRank > 0 ? (int)$currentUserRank : '-' ?>
                </div>
                <div>
                    <span style="font-size: 0.75rem; color: #A1A1AA; text-transform: uppercase; letter-spacing: 0.05em; display: block; font-weight: 600;">Posisi Peringkat Anda</span>
                    <h3 style="font-family: var(--font-heading); font-size: 1.05rem; font-weight: 800; color: #FFFFFF; margin: 0;">
                        <?= htmlspecialchars($_SESSION['user']['name'] ?? 'Anda') ?>
                    </h3>
                </div>
            </div>

            <div style="display: flex; align-items: center; gap: 1.5rem;" class="font-mono">
                <div>
                    <span style="font-size: 0.75rem; color: #A1A1AA; display: block;">Kuis Selesai</span>
                    <span style="font-size: 1.1rem; font-weight: 800; color: #FFFFFF;"><?= (int)($currentUserStats['completed_quizzes'] ?? 0) ?></span>
                </div>
                <div style="width: 1px; height: 28px; background-color: #3F3F46;"></div>
                <div>
                    <span style="font-size: 0.75rem; color: #A1A1AA; display: block;">Total Skor</span>
                    <span style="font-size: 1.1rem; font-weight: 800; color: #38BDF8;"><?= number_format((int)($currentUserStats['total_score'] ?? 0)) ?> Pts</span>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<!-- TOP 10 RANKINGS TABLE CARD -->
<div class="supabase-panel-card" style="padding: 0; overflow: hidden;">
    <span class="corner-crosshair corner-tl">+</span>
    <span class="corner-crosshair corner-tr">+</span>
    <span class="corner-crosshair corner-bl">+</span>
    <span class="corner-crosshair corner-br">+</span>

    <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid #E5E7EB; display: flex; align-items: center; justify-content: space-between;">
        <h3 style="font-family: var(--font-heading); font-size: 1.05rem; font-weight: 800; color: #18181B; margin: 0;">
            10 Besar Peserta Terbaik
        </h3>
        <span class="font-mono" style="font-size: 0.75rem; color: #71717A;">Kategori: <?= htmlspecialchars($activeCategory === 'all' ? 'Semua Topik' : $activeCategory) ?></span>
    </div>

    <?php if (empty($leaderboard)): ?>
        <div style="padding: 3rem 1.5rem; text-align: center; color: #71717A; font-size: 0.875rem;">
            Belum ada aktivitas ujian kuis yang tercatat pada kategori ini.
        </div>
    <?php else: ?>
        <div style="overflow-x: auto;">
            <table class="geist-data-table" style="width: 100%; border-collapse: collapse; text-align: left;">
                <thead>
                    <tr style="background-color: #FAFAFA; border-bottom: 1px solid #E5E7EB;">
                        <th style="padding: 0.85rem 1.5rem; font-size: 0.75rem; font-weight: 700; color: #71717A; text-transform: uppercase; width: 80px;">Rank</th>
                        <th style="padding: 0.85rem 1.5rem; font-size: 0.75rem; font-weight: 700; color: #71717A; text-transform: uppercase;">Nama Siswa</th>
                        <th style="padding: 0.85rem 1.5rem; font-size: 0.75rem; font-weight: 700; color: #71717A; text-transform: uppercase; text-align: right;">Kuis Selesai</th>
                        <th style="padding: 0.85rem 1.5rem; font-size: 0.75rem; font-weight: 700; color: #71717A; text-transform: uppercase; text-align: right;">Total Akumulasi Skor</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $rankNum = 1;
                    foreach ($leaderboard as $user):
                        $isCurrent = ((int)$user['id'] === $currentUserId);
                    ?>
                        <tr style="border-bottom: 1px solid #E5E7EB; background-color: <?= $isCurrent ? '#F4F4F5' : '#FFFFFF' ?>;">
                            <td style="padding: 1rem 1.5rem;">
                                <?php if ($rankNum === 1): ?>
                                    <span style="display: inline-flex; align-items: center; justify-content: center; width: 28px; height: 28px; border-radius: 6px; background-color: #FEF08A; color: #854D0E; font-weight: 800; font-family: var(--font-mono); font-size: 0.8rem;">🥇 1</span>
                                <?php elseif ($rankNum === 2): ?>
                                    <span style="display: inline-flex; align-items: center; justify-content: center; width: 28px; height: 28px; border-radius: 6px; background-color: #E2E8F0; color: #334155; font-weight: 800; font-family: var(--font-mono); font-size: 0.8rem;">🥈 2</span>
                                <?php elseif ($rankNum === 3): ?>
                                    <span style="display: inline-flex; align-items: center; justify-content: center; width: 28px; height: 28px; border-radius: 6px; background-color: #FFEDD5; color: #9A3412; font-weight: 800; font-family: var(--font-mono); font-size: 0.8rem;">🥉 3</span>
                                <?php else: ?>
                                    <span class="font-mono" style="font-size: 0.85rem; font-weight: 700; color: #71717A; padding-left: 0.4rem;">#<?= $rankNum ?></span>
                                <?php endif; ?>
                            </td>
                            <td style="padding: 1rem 1.5rem;">
                                <div style="display: flex; align-items: center; gap: 0.65rem;">
                                    <div style="width: 28px; height: 28px; border-radius: 50%; background-color: #18181B; color: #FFFFFF; display: flex; align-items: center; justify-content: center; font-size: 0.75rem; font-weight: 700;">
                                        <?= strtoupper(substr(htmlspecialchars($user['username']), 0, 1)) ?>
                                    </div>
                                    <span style="font-size: 0.9rem; font-weight: <?= $isCurrent ? '800' : '600' ?>; color: #18181B;">
                                        <?= htmlspecialchars($user['username']) ?>
                                    </span>
                                    <?php if ($isCurrent): ?>
                                        <span class="status-badge status-active" style="font-size: 0.675rem; padding: 2px 6px;">Anda</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td style="padding: 1rem 1.5rem; text-align: right;" class="font-mono" style="font-size: 0.85rem; color: #52525B;">
                                <?= (int)($user['completed_quizzes'] ?? 0) ?>
                            </td>
                            <td style="padding: 1rem 1.5rem; text-align: right;" class="font-mono">
                                <span style="font-size: 0.95rem; font-weight: 800; color: #18181B;">
                                    <?= number_format((int)$user['total_score']) ?>
                                </span>
                                <span style="font-size: 0.75rem; color: #71717A;">Pts</span>
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

<?php require_once dirname(__DIR__) . '/templates/footer.php'; ?>
