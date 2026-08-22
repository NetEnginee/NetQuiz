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

    /* ==========================================================================
       MINIMALIST FROSTED GLASS STAT CARDS WITH CUTE PIXEL ACCENTS
       ========================================================================== */
    .student-stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    .student-stat-card {
        padding: 1.1rem 1.25rem;
        background-color: rgba(255, 255, 255, 0.85) !important;
        backdrop-filter: blur(14px);
        -webkit-backdrop-filter: blur(14px);
        border: 1.5px solid #18181B !important;
        border-radius: 6px;
        position: relative;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        box-shadow: 0 2.5px 0 #18181B, 0 4px 12px rgba(0, 0, 0, 0.04) !important;
        transition: transform 0.15s ease, box-shadow 0.15s ease, border-color 0.15s ease;
        user-select: none;
    }

    .student-stat-card .corner-crosshair {
        color: #22C55E;
        font-family: var(--font-mono);
        font-size: 10px;
        font-weight: 800;
        opacity: 0.85;
    }

    .student-stat-card:hover {
        transform: translateY(-2px);
        border-color: #22C55E !important;
        box-shadow: 0 3.5px 0 #15803D, 0 8px 18px rgba(34, 197, 94, 0.15) !important;
    }

    .student-stat-card:active {
        transform: translateY(2px);
        box-shadow: none !important;
    }

    .stat-card-top-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 0.5rem;
    }

    .stat-pixel-dot {
        font-size: 7.5px;
        line-height: 1;
        display: inline-block;
        margin-right: 0.35rem;
        vertical-align: middle;
        user-select: none;
        color: #22C55E;
        text-shadow: 0 0 6px #22C55E;
    }

    .stat-label-text {
        font-size: 0.725rem;
        font-weight: 700;
        color: #71717A;
        font-family: var(--font-mono);
        text-transform: uppercase;
        letter-spacing: 0.04em;
        display: flex;
        align-items: center;
    }

    .stat-pixel-badge-icon {
        width: 24px;
        height: 24px;
        background-color: #FAFAFA;
        border: 1px solid #E4E4E7;
        border-radius: 4px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 1px 0 #D4D4D8;
        image-rendering: pixelated;
        flex-shrink: 0;
        transition: all 0.15s ease;
    }

    .student-stat-card:hover .stat-pixel-badge-icon {
        background-color: #F0FDF4;
        border-color: #86EFAC;
        box-shadow: 0 1px 0 #22C55E;
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
        letter-spacing: -0.02em;
    }

    .stat-unit-text {
        font-size: 0.75rem;
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

<div class="student-stats-grid">
    <!-- Stat 1: Kuis Selesai -->
    <div class="supabase-panel-card student-stat-card">
        <span class="corner-crosshair corner-tl">+</span>
        <span class="corner-crosshair corner-tr">+</span>
        <span class="corner-crosshair corner-bl">+</span>
        <span class="corner-crosshair corner-br">+</span>
        <div class="stat-card-top-row">
            <span class="stat-label-text"><span class="stat-pixel-dot" aria-hidden="true">■</span>Kuis Selesai</span>
            <div class="stat-pixel-badge-icon" aria-hidden="true">
                <svg viewBox="0 0 16 16" width="14" height="14" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect x="2" y="5" width="12" height="7" fill="#15803D" rx="1" />
                    <rect x="3" y="6" width="10" height="5" fill="#22C55E" />
                    <rect x="4" y="8" width="3" height="1" fill="#FFFFFF" />
                    <rect x="5" y="7" width="1" height="3" fill="#FFFFFF" />
                    <rect x="10" y="8" width="1.5" height="1.5" fill="#DCFCE7" />
                    <rect x="12" y="8" width="1.5" height="1.5" fill="#DCFCE7" />
                </svg>
            </div>
        </div>
        <div class="stat-value-group">
            <span class="stat-main-number"><?= (int)($stats['completed_quizzes'] ?? 0) ?></span>
            <span class="stat-unit-text">Selesai</span>
        </div>
    </div>

    <!-- Stat 2: Rata-rata Skor -->
    <div class="supabase-panel-card student-stat-card">
        <span class="corner-crosshair corner-tl">+</span>
        <span class="corner-crosshair corner-tr">+</span>
        <span class="corner-crosshair corner-bl">+</span>
        <span class="corner-crosshair corner-br">+</span>
        <div class="stat-card-top-row">
            <span class="stat-label-text"><span class="stat-pixel-dot" aria-hidden="true">■</span>Rata-rata Skor</span>
            <div class="stat-pixel-badge-icon" aria-hidden="true">
                <svg viewBox="0 0 16 16" width="14" height="14" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect x="6" y="2" width="4" height="12" fill="#22C55E" />
                    <rect x="2" y="6" width="12" height="4" fill="#22C55E" />
                    <rect x="5" y="5" width="6" height="6" fill="#15803D" />
                    <rect x="6" y="6" width="4" height="4" fill="#DCFCE7" />
                    <rect x="7" y="7" width="2" height="2" fill="#15803D" />
                </svg>
            </div>
        </div>
        <div class="stat-value-group">
            <span class="stat-main-number"><?= (int)($stats['average_score'] ?? 0) ?>%</span>
            <span class="stat-unit-text">Akurasi</span>
        </div>
    </div>

    <!-- Stat 3: Total Poin -->
    <div class="supabase-panel-card student-stat-card">
        <span class="corner-crosshair corner-tl">+</span>
        <span class="corner-crosshair corner-tr">+</span>
        <span class="corner-crosshair corner-bl">+</span>
        <span class="corner-crosshair corner-br">+</span>
        <div class="stat-card-top-row">
            <span class="stat-label-text"><span class="stat-pixel-dot" aria-hidden="true">■</span>Total Poin</span>
            <div class="stat-pixel-badge-icon" aria-hidden="true">
                <svg viewBox="0 0 16 16" width="14" height="14" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect x="4" y="2" width="8" height="12" fill="#15803D" />
                    <rect x="2" y="4" width="12" height="8" fill="#15803D" />
                    <rect x="4" y="4" width="8" height="8" fill="#22C55E" />
                    <rect x="6" y="6" width="4" height="4" fill="#DCFCE7" />
                    <rect x="7" y="7" width="2" height="2" fill="#FFFFFF" />
                </svg>
            </div>
        </div>
        <div class="stat-value-group">
            <span class="stat-main-number"><?= number_format((int)($stats['total_score'] ?? 0)) ?></span>
            <span class="stat-unit-text">Pts</span>
        </div>
    </div>

    <!-- Stat 4: Lencana Prestasi -->
    <div class="supabase-panel-card student-stat-card">
        <span class="corner-crosshair corner-tl">+</span>
        <span class="corner-crosshair corner-tr">+</span>
        <span class="corner-crosshair corner-bl">+</span>
        <span class="corner-crosshair corner-br">+</span>
        <div class="stat-card-top-row">
            <span class="stat-label-text"><span class="stat-pixel-dot" aria-hidden="true">■</span>Lencana</span>
            <div class="stat-pixel-badge-icon" aria-hidden="true">
                <svg viewBox="0 0 16 16" width="14" height="14" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect x="4" y="2" width="8" height="6" fill="#22C55E" stroke="#15803D" stroke-width="1" />
                    <rect x="2" y="3" width="2" height="3" fill="#86EFAC" />
                    <rect x="12" y="3" width="2" height="3" fill="#86EFAC" />
                    <rect x="7" y="8" width="2" height="3" fill="#15803D" />
                    <rect x="4" y="11" width="8" height="2" fill="#15803D" />
                    <rect x="5" y="12" width="6" height="1" fill="#22C55E" />
                </svg>
            </div>
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