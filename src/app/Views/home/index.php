<?php
$stats = $stats ?? [
    'completed_quizzes' => 0,
    'completion_rate' => 0,
    'total_score' => 0,
    'average_score' => 0,
    'categories' => ['Routing' => 0, 'Firewall & NAT' => 0, 'Wireless' => 0, 'Network Management' => 0],
    'category_scores' => ['Routing' => 0, 'Firewall & NAT' => 0, 'Wireless' => 0, 'Network Management' => 0],
    'recent_activities' => [],
    'unlocked_badges' => [],
    'locked_achievements' => [],
    'next_badge' => null
];

$unlockedBadges = $unlockedBadges ?? ($stats['unlocked_badges'] ?? []);
$materials = $materials ?? [];

require_once dirname(__DIR__) . '/templates/header.php';
?>

<style>
    /* ==========================================================================
       STUDENT DASHBOARD RESPONSIVE STYLES
       ========================================================================== */
    .dashboard-header-bar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 0.75rem;
        margin-bottom: 1.25rem;
    }

    /* 4-Stat Box Responsive Grid */
    .student-stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    .student-stat-card {
        padding: 1.15rem 1.25rem;
        background-color: rgba(255, 255, 255, 0.72) !important;
        backdrop-filter: blur(14px);
        -webkit-backdrop-filter: blur(14px);
        border: 1.5px solid #E4E4E7;
        border-radius: 8px;
        position: relative;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        box-shadow: 0 3px 0 #E4E4E7, 0 6px 16px rgba(0, 0, 0, 0.05);
        transition: transform 0.15s ease, box-shadow 0.15s ease, border-color 0.15s ease;
        user-select: none;
    }

    .student-stat-card:hover {
        transform: translateY(-2px);
    }

    .student-stat-card:active {
        transform: translateY(1px);
    }

    .stat-pixel-dot {
        font-size: 8px;
        line-height: 1;
        display: inline-block;
        margin-right: 0.35rem;
        vertical-align: middle;
        user-select: none;
        transition: text-shadow 0.15s ease;
    }

    /* 1. Stat 1: Kuis Selesai (Emerald Green #22C55E) */
    .stat-card-quizzes {
        border-color: #86EFAC !important;
        box-shadow: 0 3px 0 #15803D, 0 8px 20px rgba(34, 197, 94, 0.14) !important;
    }

    .stat-card-quizzes .corner-crosshair {
        color: #22C55E;
    }

    .stat-card-quizzes .stat-pixel-dot {
        color: #22C55E;
        text-shadow: 0 0 6px #22C55E, 0 0 12px rgba(34, 197, 94, 0.6);
    }

    .stat-card-quizzes:hover {
        border-color: #22C55E !important;
        box-shadow: 0 4px 0 #15803D, 0 12px 26px rgba(34, 197, 94, 0.25) !important;
    }

    .stat-card-quizzes:active {
        box-shadow: 0 1.5px 0 #15803D !important;
    }

    /* 2. Stat 2: Rata-rata Skor (Cyber Sky Cyan #38BDF8) */
    .stat-card-score {
        border-color: #7DD3FC !important;
        box-shadow: 0 3px 0 #0284C7, 0 8px 20px rgba(56, 189, 248, 0.14) !important;
    }

    .stat-card-score .corner-crosshair {
        color: #38BDF8;
    }

    .stat-card-score .stat-pixel-dot {
        color: #38BDF8;
        text-shadow: 0 0 6px #38BDF8, 0 0 12px rgba(56, 189, 248, 0.6);
    }

    .stat-card-score:hover {
        border-color: #38BDF8 !important;
        box-shadow: 0 4px 0 #0284C7, 0 12px 26px rgba(56, 189, 248, 0.25) !important;
    }

    .stat-card-score:active {
        box-shadow: 0 1.5px 0 #0284C7 !important;
    }

    /* 3. Stat 3: Total Poin (Warm Amber Gold #F59E0B) */
    .stat-card-points {
        border-color: #FDE68A !important;
        box-shadow: 0 3px 0 #D97706, 0 8px 20px rgba(245, 158, 11, 0.14) !important;
    }

    .stat-card-points .corner-crosshair {
        color: #F59E0B;
    }

    .stat-card-points .stat-pixel-dot {
        color: #F59E0B;
        text-shadow: 0 0 6px #F59E0B, 0 0 12px rgba(245, 158, 11, 0.6);
    }

    .stat-card-points:hover {
        border-color: #F59E0B !important;
        box-shadow: 0 4px 0 #D97706, 0 12px 26px rgba(245, 158, 11, 0.25) !important;
    }

    .stat-card-points:active {
        box-shadow: 0 1.5px 0 #D97706 !important;
    }

    /* 4. Stat 4: Lencana Prestasi (Royal Purple #A855F7) */
    .stat-card-badges {
        border-color: #D8B4FE !important;
        box-shadow: 0 3px 0 #7E22CE, 0 8px 20px rgba(168, 85, 247, 0.14) !important;
    }

    .stat-card-badges .corner-crosshair {
        color: #A855F7;
    }

    .stat-card-badges .stat-pixel-dot {
        color: #A855F7;
        text-shadow: 0 0 6px #A855F7, 0 0 12px rgba(168, 85, 247, 0.6);
    }

    .stat-card-badges:hover {
        border-color: #A855F7 !important;
        box-shadow: 0 4px 0 #7E22CE, 0 12px 26px rgba(168, 85, 247, 0.25) !important;
    }

    .stat-card-badges:active {
        box-shadow: 0 1.5px 0 #7E22CE !important;
    }

    .stat-label-text {
        font-size: 0.725rem;
        font-weight: 700;
        color: #71717A;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 0.4rem;
        display: flex;
        align-items: center;
    }

    .stat-value-group {
        display: flex;
        align-items: baseline;
        gap: 0.45rem;
        flex-wrap: wrap;
    }

    .stat-main-number {
        font-size: 1.75rem;
        font-weight: 800;
        color: #18181B;
        font-family: var(--font-mono);
        line-height: 1.1;
    }

    .stat-unit-text {
        font-size: 0.775rem;
        color: #71717A;
        font-weight: 500;
    }

    /* 2-Column Main Dashboard Layout */
    .student-dashboard-grid {
        display: grid;
        grid-template-columns: 1.4fr 1fr;
        gap: 1.25rem;
        align-items: start;
    }

    .dashboard-panel {
        padding: 1.35rem 1.5rem;
    }

    .dashboard-panel-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1.15rem;
        padding-bottom: 0.75rem;
        border-bottom: 1px solid #E5E7EB;
        gap: 0.5rem;
    }

    .dashboard-panel-title {
        font-family: var(--font-heading);
        font-size: 1rem;
        font-weight: 800;
        color: #18181B;
        margin: 0;
        letter-spacing: -0.01em;
    }

    /* Recent Activity Item */
    .activity-row-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0.8rem 1rem;
        background-color: #FAFAFA;
        border: 1px solid #E5E7EB;
        border-radius: 8px;
        gap: 0.85rem;
        transition: none !important;
        transform: none !important;
    }

    .activity-row-item:hover {
        background-color: #FFFFFF;
        border-color: #000000;
        transform: none !important;
    }

    .activity-score-badge {
        width: 40px;
        height: 40px;
        border-radius: 6px;
        background-color: #18181B;
        color: #FFFFFF;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        border: 1px solid #27272A;
    }

    .activity-score-number {
        font-family: var(--font-mono);
        font-size: 0.9rem;
        font-weight: 800;
        line-height: 1;
    }

    .activity-info-box {
        min-width: 0;
        flex: 1;
    }

    .activity-quiz-title {
        font-size: 0.875rem;
        font-weight: 700;
        color: #18181B;
        margin: 0 0 0.25rem 0;
        line-height: 1.35;
        overflow: hidden;
        text-overflow: ellipsis;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
    }

    .activity-meta-row {
        display: flex;
        align-items: center;
        gap: 0.4rem;
        font-size: 0.725rem;
        color: #71717A;
        flex-wrap: wrap;
    }

    /* Learning Material Item */
    .material-link-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0.8rem 1rem;
        background-color: #FAFAFA;
        border: 1px solid #E5E7EB;
        border-radius: 8px;
        text-decoration: none;
        gap: 0.85rem;
        transition: none !important;
        transform: none !important;
    }

    .material-link-row:hover {
        background-color: #FFFFFF;
        border-color: #000000;
        transform: none !important;
    }

    .material-link-row:active {
        background-color: #F4F4F5;
        border-color: #000000;
        transform: none !important;
    }

    .material-title-text {
        font-size: 0.875rem;
        font-weight: 700;
        color: #18181B;
        margin: 0;
        line-height: 1.35;
        overflow: hidden;
        text-overflow: ellipsis;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
    }

    /* Topic Distribution Item */
    .topic-bar-group {
        display: flex;
        flex-direction: column;
        gap: 0.85rem;
    }

    .topic-row-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: 0.8rem;
        font-weight: 600;
        color: #18181B;
        margin-bottom: 0.3rem;
    }

    .topic-progress-track {
        width: 100%;
        height: 6px;
        background-color: #F4F4F5;
        border-radius: 9999px;
        overflow: hidden;
        border: 1px solid #E5E7EB;
    }

    .topic-progress-fill {
        height: 100%;
        background-color: #18181B;
        border-radius: 9999px;
    }

    /* ==========================================================================
       MEDIA QUERIES FOR TABLET & MOBILE VIEWPORTS
       ========================================================================== */
    @media (max-width: 960px) {
        .student-stats-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 0.85rem;
        }

        .student-dashboard-grid {
            grid-template-columns: 1fr;
            gap: 1.15rem;
        }
    }

    @media (max-width: 640px) {
        .dashboard-header-bar {
            margin-bottom: 1rem;
        }

        .student-stats-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 0.65rem;
            margin-bottom: 1.15rem;
        }

        .student-stat-card {
            padding: 0.85rem 0.95rem;
        }

        .stat-label-text {
            font-size: 0.675rem;
            margin-bottom: 0.25rem;
        }

        .stat-main-number {
            font-size: 1.45rem;
        }

        .stat-unit-text {
            font-size: 0.7rem;
        }

        .dashboard-panel {
            padding: 1.15rem 1rem !important;
        }

        .dashboard-panel-header {
            margin-bottom: 0.9rem;
            padding-bottom: 0.6rem;
        }

        .dashboard-panel-title {
            font-size: 0.925rem;
        }

        .activity-row-item {
            padding: 0.7rem 0.75rem;
            gap: 0.65rem;
        }

        .activity-score-badge {
            width: 36px;
            height: 36px;
        }

        .activity-score-number {
            font-size: 0.85rem;
        }

        .activity-quiz-title {
            font-size: 0.825rem;
        }

        .material-link-row {
            padding: 0.7rem 0.75rem;
            gap: 0.65rem;
        }

        .material-title-text {
            font-size: 0.825rem;
        }
    }

    @media (max-width: 360px) {
        .student-stats-grid {
            grid-template-columns: 1fr;
            gap: 0.5rem;
        }
    }
</style>

<!-- Breadcrumb & Header -->
<div class="dashboard-header-bar">
    <?= renderBreadcrumb([
        ['label' => 'Siswa', 'url' => BASE_URL . '/'],
        ['label' => 'Dashboard']
    ]) ?>
</div>

<!-- 4 STAT BOXES (RESPONSIVE HIGH DENSITY GRID WITH FROSTED GLASS 3D VOXEL) -->
<div class="student-stats-grid">
    <!-- Stat 1: Kuis Selesai (Emerald) -->
    <div class="supabase-panel-card student-stat-card stat-card-quizzes">
        <span class="corner-crosshair corner-tl">+</span>
        <span class="corner-crosshair corner-tr">+</span>
        <span class="corner-crosshair corner-bl">+</span>
        <span class="corner-crosshair corner-br">+</span>
        <div>
            <span class="stat-label-text"><span class="stat-pixel-dot" aria-hidden="true">■</span>Kuis Selesai</span>
        </div>
        <div class="stat-value-group">
            <span class="stat-main-number"><?= (int)($stats['completed_quizzes'] ?? 0) ?></span>
            <span class="stat-unit-text">Selesai</span>
        </div>
    </div>

    <!-- Stat 2: Rata-rata Skor (Cyan) -->
    <div class="supabase-panel-card student-stat-card stat-card-score">
        <span class="corner-crosshair corner-tl">+</span>
        <span class="corner-crosshair corner-tr">+</span>
        <span class="corner-crosshair corner-bl">+</span>
        <span class="corner-crosshair corner-br">+</span>
        <div>
            <span class="stat-label-text"><span class="stat-pixel-dot" aria-hidden="true">■</span>Rata-rata Skor</span>
        </div>
        <div class="stat-value-group">
            <span class="stat-main-number"><?= (int)($stats['average_score'] ?? 0) ?>%</span>
            <span class="stat-unit-text">Akurasi</span>
        </div>
    </div>

    <!-- Stat 3: Total Poin (Amber) -->
    <div class="supabase-panel-card student-stat-card stat-card-points">
        <span class="corner-crosshair corner-tl">+</span>
        <span class="corner-crosshair corner-tr">+</span>
        <span class="corner-crosshair corner-bl">+</span>
        <span class="corner-crosshair corner-br">+</span>
        <div>
            <span class="stat-label-text"><span class="stat-pixel-dot" aria-hidden="true">■</span>Total Poin</span>
        </div>
        <div class="stat-value-group">
            <span class="stat-main-number"><?= number_format((int)($stats['total_score'] ?? 0)) ?></span>
            <span class="stat-unit-text">Pts</span>
        </div>
    </div>

    <!-- Stat 4: Lencana Prestasi (Purple) -->
    <div class="supabase-panel-card student-stat-card stat-card-badges">
        <span class="corner-crosshair corner-tl">+</span>
        <span class="corner-crosshair corner-tr">+</span>
        <span class="corner-crosshair corner-bl">+</span>
        <span class="corner-crosshair corner-br">+</span>
        <div>
            <span class="stat-label-text"><span class="stat-pixel-dot" aria-hidden="true">■</span>Lencana Prestasi</span>
        </div>
        <div class="stat-value-group">
            <span class="stat-main-number"><?= count($unlockedBadges) ?></span>
            <span class="stat-unit-text">Terbuka</span>
        </div>
    </div>
</div>

<!-- MAIN 2-COLUMN DASHBOARD GRID -->
<div class="student-dashboard-grid">
    <!-- LEFT COLUMN: Recent Activity & Learning Modules -->
    <div style="display: flex; flex-direction: column; gap: 1.25rem;">
        <!-- Card: Riwayat Aktivitas Kuis -->
        <div class="supabase-panel-card dashboard-panel">
            <span class="corner-crosshair corner-tl">+</span>
            <span class="corner-crosshair corner-tr">+</span>
            <span class="corner-crosshair corner-bl">+</span>
            <span class="corner-crosshair corner-br">+</span>

            <div class="dashboard-panel-header">
                <h3 class="dashboard-panel-title">
                    Riwayat Aktivitas Ujian
                </h3>
            </div>

            <?php if (empty($stats['recent_activities'])): ?>
                <div style="padding: 2.25rem 1rem; text-align: center;">
                    <p style="font-size: 0.85rem; color: #71717A; margin: 0; font-weight: 500;">
                        Belum ada aktivitas ujian kuis yang tercatat.
                    </p>
                </div>
            <?php else: ?>
                <div style="display: flex; flex-direction: column; gap: 0.65rem;">
                    <?php foreach (array_slice($stats['recent_activities'], 0, 5) as $act): ?>
                        <div class="activity-row-item">
                            <div class="activity-score-badge">
                                <span class="activity-score-number"><?= (int)($act['score'] ?? 0) ?></span>
                            </div>
                            <div class="activity-info-box">
                                <h4 class="activity-quiz-title">
                                    <?= htmlspecialchars($act['quiz_title'] ?? $act['title'] ?? 'Ujian MikroTik') ?>
                                </h4>
                                <div class="activity-meta-row">
                                    <span class="status-badge" style="background-color: #FFFFFF; border: 1px solid #E5E7EB; font-size: 0.675rem; padding: 1px 6px; border-radius: 4px;"><?= htmlspecialchars($act['category'] ?? 'MikroTik') ?></span>
                                    <span>•</span>
                                    <span class="font-mono"><?= date('d M Y, H:i', strtotime($act['created_at'] ?? 'now')) ?></span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Card: Materi Pembelajaran Rekomendasi -->
        <div class="supabase-panel-card dashboard-panel">
            <span class="corner-crosshair corner-tl">+</span>
            <span class="corner-crosshair corner-tr">+</span>
            <span class="corner-crosshair corner-bl">+</span>
            <span class="corner-crosshair corner-br">+</span>

            <div class="dashboard-panel-header">
                <h3 class="dashboard-panel-title">
                    Materi Belajar Terkini
                </h3>
                <a href="<?= BASE_URL ?>/learn" class="btn-secondary-outline" style="font-size: 0.725rem; padding: 0.25rem 0.6rem;">
                    <span>Lihat Semua &rarr;</span>
                </a>
            </div>

            <?php if (empty($materials)): ?>
                <div style="padding: 2rem 1rem; text-align: center; color: #71717A; font-size: 0.85rem;">
                    Belum ada materi yang diterbitkan.
                </div>
            <?php else: ?>
                <div style="display: flex; flex-direction: column; gap: 0.65rem;">
                    <?php foreach (array_slice($materials, 0, 4) as $mat): ?>
                        <?php
                        $cleanText = strip_tags($mat['content'] ?? '');
                        $readTime = max(1, (int)ceil(str_word_count($cleanText) / 180));
                        ?>
                        <a href="<?= BASE_URL ?>/learn/<?= (int)$mat['id'] ?>" class="material-link-row">
                            <div style="min-width: 0; flex: 1;">
                                <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.2rem;">
                                    <span style="font-size: 0.7rem; color: #71717A;" class="font-mono">~<?= $readTime ?> menit baca</span>
                                </div>
                                <h4 class="material-title-text">
                                    <?= htmlspecialchars($mat['title']) ?>
                                </h4>
                            </div>
                            <div style="flex-shrink: 0; font-size: 0.775rem; font-weight: 600; color: #18181B; font-family: var(--font-mono); display: inline-flex; align-items: center; gap: 0.25rem;">
                                <span>Baca</span>
                                <span>&rarr;</span>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- RIGHT COLUMN: Topic Mastery & Next Badge Target -->
    <div style="display: flex; flex-direction: column; gap: 1.25rem;">
        <!-- Card: Penguasaan Topik MikroTik -->
        <div class="supabase-panel-card dashboard-panel">
            <span class="corner-crosshair corner-tl">+</span>
            <span class="corner-crosshair corner-tr">+</span>
            <span class="corner-crosshair corner-bl">+</span>
            <span class="corner-crosshair corner-br">+</span>

            <div class="dashboard-panel-header">
                <h3 class="dashboard-panel-title">
                    Distribusi Topik Ujian
                </h3>
            </div>

            <div class="topic-bar-group">
                <?php
                $cats = $stats['categories'] ?? ['Routing' => 0, 'Firewall & NAT' => 0, 'Wireless' => 0, 'Network Management' => 0];
                foreach ($cats as $catName => $count):
                ?>
                    <div>
                        <div class="topic-row-header">
                            <span><?= htmlspecialchars($catName) ?></span>
                            <span class="font-mono" style="color: #71717A; font-size: 0.75rem;"><?= (int)$count ?> Kuis</span>
                        </div>
                        <div class="topic-progress-track">
                            <div class="topic-progress-fill" style="width: <?= min(100, $count * 20) ?>%;"></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Card: Target Lencana Prestasi -->
        <div class="supabase-panel-card dashboard-panel">
            <span class="corner-crosshair corner-tl">+</span>
            <span class="corner-crosshair corner-tr">+</span>
            <span class="corner-crosshair corner-bl">+</span>
            <span class="corner-crosshair corner-br">+</span>

            <div class="dashboard-panel-header">
                <h3 class="dashboard-panel-title">
                    Lencana Terbuka
                </h3>
            </div>

            <?php if (!empty($stats['next_badge'])): $nb = $stats['next_badge']; ?>
                <div style="padding: 0.95rem; background-color: #FAFAFA; border: 1px solid #E5E7EB; border-radius: 8px;">
                    <div style="margin-bottom: 0.65rem;">
                        <h4 style="font-size: 0.875rem; font-weight: 800; color: #18181B; margin: 0 0 0.15rem 0;">
                            <?= htmlspecialchars($nb['title']) ?>
                        </h4>
                        <p style="font-size: 0.75rem; color: #71717A; margin: 0; line-height: 1.35;">
                            <?= htmlspecialchars($nb['description']) ?>
                        </p>
                    </div>
                    <div>
                        <div style="display: flex; align-items: center; justify-content: space-between; font-size: 0.725rem; color: #71717A; margin-bottom: 0.3rem;" class="font-mono">
                            <span>Progres</span>
                            <span><?= (int)($nb['progress'] ?? 0) ?> / <?= (int)($nb['max'] ?? 1) ?></span>
                        </div>
                        <div style="width: 100%; height: 6px; background-color: #E5E7EB; border-radius: 9999px; overflow: hidden;">
                            <div style="height: 100%; width: <?= min(100, (int)($nb['percent'] ?? 0)) ?>%; background-color: #18181B; border-radius: 9999px;"></div>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div style="padding: 1.25rem 1rem; text-align: center; background-color: #FAFAFA; border: 1px solid #E5E7EB; border-radius: 8px;">
                    <p style="font-size: 0.85rem; font-weight: 700; color: #18181B; margin: 0 0 0.25rem 0;">Belum Ada Lencana Terbuka</p>
                    <p style="font-size: 0.75rem; color: #71717A; margin: 0; line-height: 1.4;">Selesaikan minimal 1 kuis untuk membuka lencana prestasi.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/templates/footer.php'; ?>