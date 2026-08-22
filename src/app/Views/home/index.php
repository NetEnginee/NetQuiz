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
$userName = $_SESSION['user']['name'] ?? 'Siswa';

require_once dirname(__DIR__) . '/templates/header.php';
?>

<!-- Page Header & Welcome Breadcrumb -->
<div style="margin-bottom: 2rem;">
    <nav class="admin-breadcrumb-nav" aria-label="Breadcrumb" style="margin-bottom: 0.5rem;">
        <span class="breadcrumb-item">Siswa</span>
        <span class="breadcrumb-separator">/</span>
        <span class="breadcrumb-active">Dashboard</span>
    </nav>
    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h1 style="font-family: var(--font-heading); font-size: 1.5rem; font-weight: 800; color: #18181B; margin: 0 0 0.25rem 0; letter-spacing: -0.02em;">
                Selamat Datang, <?= htmlspecialchars($userName) ?> 👋
            </h1>
            <p style="font-size: 0.875rem; color: #71717A; margin: 0;">
                Pantau progres evaluasi pemahaman konfigurasi MikroTik RouterOS Anda.
            </p>
        </div>
        <div style="display: flex; align-items: center; gap: 0.5rem;">
            <a href="<?= BASE_URL ?>/quiz" class="btn-primary-black" style="font-size: 0.85rem; padding: 0.5rem 1rem;">
                <i data-lucide="play" style="width: 14px; height: 14px;"></i>
                <span>Mulai Kuis</span>
            </a>
            <a href="<?= BASE_URL ?>/learn" class="btn-secondary-outline" style="font-size: 0.85rem; padding: 0.5rem 1rem;">
                <i data-lucide="book-open" style="width: 14px; height: 14px;"></i>
                <span>Baca Materi</span>
            </a>
        </div>
    </div>
</div>

<!-- 1. 4 STAT CARDS GEIST -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem; margin-bottom: 2rem;">
    <!-- Stat 1: Kuis Diselesaikan -->
    <div class="supabase-panel-card" style="padding: 1.25rem;">
        <span class="corner-crosshair corner-tl">+</span>
        <span class="corner-crosshair corner-tr">+</span>
        <span class="corner-crosshair corner-bl">+</span>
        <span class="corner-crosshair corner-br">+</span>
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.75rem;">
            <span style="font-size: 0.75rem; font-weight: 700; color: #71717A; text-transform: uppercase; letter-spacing: 0.05em;">Kuis Selesai</span>
            <div style="width: 28px; height: 28px; border-radius: 6px; background-color: #F4F4F5; display: flex; align-items: center; justify-content: center; color: #18181B;">
                <i data-lucide="check-circle-2" style="width: 15px; height: 15px;"></i>
            </div>
        </div>
        <div style="display: flex; align-items: baseline; gap: 0.5rem;">
            <span style="font-size: 1.75rem; font-weight: 800; color: #18181B; font-family: var(--font-mono);"><?= (int)($stats['completed_quizzes'] ?? 0) ?></span>
            <span style="font-size: 0.8rem; color: #71717A;">Selesai</span>
        </div>
    </div>

    <!-- Stat 2: Nilai Rata-rata -->
    <div class="supabase-panel-card" style="padding: 1.25rem;">
        <span class="corner-crosshair corner-tl">+</span>
        <span class="corner-crosshair corner-tr">+</span>
        <span class="corner-crosshair corner-bl">+</span>
        <span class="corner-crosshair corner-br">+</span>
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.75rem;">
            <span style="font-size: 0.75rem; font-weight: 700; color: #71717A; text-transform: uppercase; letter-spacing: 0.05em;">Rata-rata Skor</span>
            <div style="width: 28px; height: 28px; border-radius: 6px; background-color: #F4F4F5; display: flex; align-items: center; justify-content: center; color: #18181B;">
                <i data-lucide="percent" style="width: 15px; height: 15px;"></i>
            </div>
        </div>
        <div style="display: flex; align-items: baseline; gap: 0.5rem;">
            <span style="font-size: 1.75rem; font-weight: 800; color: #18181B; font-family: var(--font-mono);"><?= (int)($stats['average_score'] ?? 0) ?>%</span>
            <span style="font-size: 0.8rem; color: #71717A;">Akurasi</span>
        </div>
    </div>

    <!-- Stat 3: Akumulasi Poin -->
    <div class="supabase-panel-card" style="padding: 1.25rem;">
        <span class="corner-crosshair corner-tl">+</span>
        <span class="corner-crosshair corner-tr">+</span>
        <span class="corner-crosshair corner-bl">+</span>
        <span class="corner-crosshair corner-br">+</span>
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.75rem;">
            <span style="font-size: 0.75rem; font-weight: 700; color: #71717A; text-transform: uppercase; letter-spacing: 0.05em;">Total Poin</span>
            <div style="width: 28px; height: 28px; border-radius: 6px; background-color: #F4F4F5; display: flex; align-items: center; justify-content: center; color: #18181B;">
                <i data-lucide="zap" style="width: 15px; height: 15px;"></i>
            </div>
        </div>
        <div style="display: flex; align-items: baseline; gap: 0.5rem;">
            <span style="font-size: 1.75rem; font-weight: 800; color: #18181B; font-family: var(--font-mono);"><?= number_format((int)($stats['total_score'] ?? 0)) ?></span>
            <span style="font-size: 0.8rem; color: #71717A;">Pts</span>
        </div>
    </div>

    <!-- Stat 4: Lencana Terkumpul -->
    <div class="supabase-panel-card" style="padding: 1.25rem;">
        <span class="corner-crosshair corner-tl">+</span>
        <span class="corner-crosshair corner-tr">+</span>
        <span class="corner-crosshair corner-bl">+</span>
        <span class="corner-crosshair corner-br">+</span>
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.75rem;">
            <span style="font-size: 0.75rem; font-weight: 700; color: #71717A; text-transform: uppercase; letter-spacing: 0.05em;">Lencana Prestasi</span>
            <div style="width: 28px; height: 28px; border-radius: 6px; background-color: #F4F4F5; display: flex; align-items: center; justify-content: center; color: #18181B;">
                <i data-lucide="award" style="width: 15px; height: 15px;"></i>
            </div>
        </div>
        <div style="display: flex; align-items: baseline; gap: 0.5rem;">
            <span style="font-size: 1.75rem; font-weight: 800; color: #18181B; font-family: var(--font-mono);"><?= count($unlockedBadges) ?></span>
            <span style="font-size: 0.8rem; color: #71717A;">Terbuka</span>
        </div>
    </div>
</div>

<!-- 2. MAIN 2-COLUMN DASHBOARD GRID -->
<div style="display: grid; grid-template-columns: 1.4fr 1fr; gap: 1.5rem; align-items: start;">
    <!-- LEFT COLUMN: Recent Activity & Learning Modules -->
    <div style="display: flex; flex-direction: column; gap: 1.5rem;">
        <!-- Card: Riwayat Aktivitas Kuis -->
        <div class="supabase-panel-card" style="padding: 1.5rem;">
            <span class="corner-crosshair corner-tl">+</span>
            <span class="corner-crosshair corner-tr">+</span>
            <span class="corner-crosshair corner-bl">+</span>
            <span class="corner-crosshair corner-br">+</span>

            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.25rem; padding-bottom: 0.75rem; border-bottom: 1px solid #E5E7EB;">
                <div>
                    <h3 style="font-family: var(--font-heading); font-size: 1.05rem; font-weight: 800; color: #18181B; margin: 0;">
                        Riwayat Aktivitas Kuis
                    </h3>
                    <p style="font-size: 0.8rem; color: #71717A; margin-top: 0.2rem;">Evaluasi kuis yang baru saja Anda selesaikan.</p>
                </div>
                <a href="<?= BASE_URL ?>/quiz" class="btn-secondary-outline" style="font-size: 0.775rem; padding: 0.35rem 0.7rem;">
                    <span>Lihat Semua</span>
                    <i data-lucide="arrow-right" style="width: 12px; height: 12px;"></i>
                </a>
            </div>

            <?php if (empty($stats['recent_activities'])): ?>
                <div style="padding: 2.5rem 1rem; text-align: center;">
                    <div style="width: 40px; height: 40px; border-radius: 50%; background-color: #F4F4F5; margin: 0 auto 0.75rem; display: flex; align-items: center; justify-content: center; color: #71717A;">
                        <i data-lucide="file-text" style="width: 20px; height: 20px;"></i>
                    </div>
                    <p style="font-size: 0.875rem; color: #52525B; margin-bottom: 1rem; font-weight: 500;">
                        Belum ada aktivitas kuis. Uji kemampuan MikroTik Anda sekarang!
                    </p>
                    <a href="<?= BASE_URL ?>/quiz" class="btn-primary-black" style="font-size: 0.825rem; padding: 0.45rem 1rem;">
                        <i data-lucide="play" style="width: 13px; height: 13px;"></i>
                        <span>Mulai Kuis Pertama</span>
                    </a>
                </div>
            <?php else: ?>
                <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                    <?php foreach (array_slice($stats['recent_activities'], 0, 5) as $act): ?>
                        <div style="display: flex; align-items: center; justify-content: space-between; padding: 0.85rem 1rem; background-color: #FAFAFA; border: 1px solid #E5E7EB; border-radius: 6px; gap: 1rem; flex-wrap: wrap;">
                            <div style="display: flex; align-items: center; gap: 0.85rem; min-width: 0;">
                                <div style="width: 34px; height: 34px; border-radius: 6px; background-color: #18181B; color: #FFFFFF; display: flex; align-items: center; justify-content: center; font-family: var(--font-mono); font-size: 0.8rem; font-weight: 800; flex-shrink: 0;">
                                    <?= (int)($act['score'] ?? 0) ?>
                                </div>
                                <div style="min-width: 0;">
                                    <h4 style="font-size: 0.9rem; font-weight: 700; color: #18181B; margin: 0 0 0.15rem 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                        <?= htmlspecialchars($act['title'] ?? 'Kuis') ?>
                                    </h4>
                                    <div style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.75rem; color: #71717A;">
                                        <span class="status-badge" style="background-color: #F4F4F5; font-size: 0.7rem;"><?= htmlspecialchars($act['category'] ?? 'MikroTik') ?></span>
                                        <span>•</span>
                                        <span class="font-mono"><?= date('d M Y, H:i', strtotime($act['created_at'] ?? 'now')) ?></span>
                                    </div>
                                </div>
                            </div>
                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                <?php if (!empty($act['quiz_id'])): ?>
                                    <a href="<?= BASE_URL ?>/quiz/review/<?= (int)$act['quiz_id'] ?>" class="btn-secondary-outline" style="font-size: 0.75rem; padding: 0.35rem 0.75rem;" title="Review Jawaban">
                                        <i data-lucide="eye" style="width: 12px; height: 12px;"></i>
                                        <span>Review</span>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Card: Materi Pembelajaran Rekomendasi -->
        <div class="supabase-panel-card" style="padding: 1.5rem;">
            <span class="corner-crosshair corner-tl">+</span>
            <span class="corner-crosshair corner-tr">+</span>
            <span class="corner-crosshair corner-bl">+</span>
            <span class="corner-crosshair corner-br">+</span>

            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.25rem; padding-bottom: 0.75rem; border-bottom: 1px solid #E5E7EB;">
                <div>
                    <h3 style="font-family: var(--font-heading); font-size: 1.05rem; font-weight: 800; color: #18181B; margin: 0;">
                        Materi Belajar Terkini
                    </h3>
                    <p style="font-size: 0.8rem; color: #71717A; margin-top: 0.2rem;">Pelajari teori dan panduan CLI konfigurasi RouterOS.</p>
                </div>
                <a href="<?= BASE_URL ?>/learn" class="btn-secondary-outline" style="font-size: 0.775rem; padding: 0.35rem 0.7rem;">
                    <span>Lihat Modul</span>
                    <i data-lucide="arrow-right" style="width: 12px; height: 12px;"></i>
                </a>
            </div>

            <?php if (empty($materials)): ?>
                <div style="padding: 1.5rem; text-align: center; color: #71717A; font-size: 0.85rem;">
                    Belum ada materi yang diterbitkan.
                </div>
            <?php else: ?>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                    <?php foreach (array_slice($materials, 0, 4) as $mat): ?>
                        <a href="<?= BASE_URL ?>/learn/<?= (int)$mat['id'] ?>" style="display: block; padding: 0.85rem; background-color: #FAFAFA; border: 1px solid #E5E7EB; border-radius: 6px; text-decoration: none; transition: all 0.15s ease;" onmouseover="this.style.borderColor='#18181B';" onmouseout="this.style.borderColor='#E5E7EB';">
                            <span class="status-badge" style="background-color: #F4F4F5; font-size: 0.7rem; margin-bottom: 0.4rem;"><?= htmlspecialchars($mat['category'] ?? 'Umum') ?></span>
                            <h4 style="font-size: 0.875rem; font-weight: 700; color: #18181B; margin: 0 0 0.3rem 0; line-height: 1.35; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;>
                                <?= htmlspecialchars($mat['title']) ?>
                            </h4>
                            <div style=" display: flex; align-items: center; justify-content: space-between; font-size: 0.75rem; color: #71717A;">
                                <span><?= htmlspecialchars($mat['difficulty'] ?? 'Mudah') ?></span>
                                <span class="font-mono">Baca Modul &rarr;</span>
                </div>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    </div>
</div>

<!-- RIGHT COLUMN: Topic Mastery & Next Badge Target -->
<div style="display: flex; flex-direction: column; gap: 1.5rem;">
    <!-- Card: Penguasaan Topik MikroTik -->
    <div class="supabase-panel-card" style="padding: 1.5rem;">
        <span class="corner-crosshair corner-tl">+</span>
        <span class="corner-crosshair corner-tr">+</span>
        <span class="corner-crosshair corner-bl">+</span>
        <span class="corner-crosshair corner-br">+</span>

        <h3 style="font-family: var(--font-heading); font-size: 1.05rem; font-weight: 800; color: #18181B; margin: 0 0 0.2rem 0;">
            Distribusi Topik Ujian
        </h3>
        <p style="font-size: 0.8rem; color: #71717A; margin: 0 0 1.25rem 0;">Jumlah kuis yang telah diselesaikan per kategori.</p>

        <div style="display: flex; flex-direction: column; gap: 1rem;">
            <?php
            $catIcons = [
                'Routing' => 'route',
                'Firewall & NAT' => 'shield',
                'Wireless' => 'wifi',
                'Network Management' => 'network'
            ];
            $cats = $stats['categories'] ?? ['Routing' => 0, 'Firewall & NAT' => 0, 'Wireless' => 0, 'Network Management' => 0];
            foreach ($cats as $catName => $count):
                $icon = $catIcons[$catName] ?? 'folder';
            ?>
                <div>
                    <div style="display: flex; align-items: center; justify-content: space-between; font-size: 0.825rem; font-weight: 600; color: #18181B; margin-bottom: 0.35rem;">
                        <div style="display: flex; align-items: center; gap: 0.45rem;">
                            <i data-lucide="<?= $icon ?>" style="width: 14px; height: 14px; color: #71717A;"></i>
                            <span><?= htmlspecialchars($catName) ?></span>
                        </div>
                        <span class="font-mono" style="color: #71717A;"><?= (int)$count ?> Kuis</span>
                    </div>
                    <div style="width: 100%; height: 6px; background-color: #F4F4F5; border-radius: 9999px; overflow: hidden; border: 1px solid #E5E7EB;">
                        <div style="height: 100%; width: <?= min(100, $count * 20) ?>%; background-color: #18181B; border-radius: 9999px;"></div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Card: Target Lencana Prestasi -->
    <div class="supabase-panel-card" style="padding: 1.5rem;">
        <span class="corner-crosshair corner-tl">+</span>
        <span class="corner-crosshair corner-tr">+</span>
        <span class="corner-crosshair corner-bl">+</span>
        <span class="corner-crosshair corner-br">+</span>

        <div style="margin-bottom: 1rem;">
            <h3 style="font-family: var(--font-heading); font-size: 1.05rem; font-weight: 800; color: #18181B; margin: 0;">
                Lencana Terdekat
            </h3>
        </div>

        <?php if (!empty($stats['next_badge'])): $nb = $stats['next_badge']; ?>
            <div style="padding: 1rem; background-color: #FAFAFA; border: 1px solid #E5E7EB; border-radius: 8px;">
                <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.75rem;">
                    <div style="width: 38px; height: 38px; border-radius: 8px; background-color: #18181B; color: #FFFFFF; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <i data-lucide="<?= htmlspecialchars($nb['icon'] ?? 'award') ?>" style="width: 18px; height: 18px;"></i>
                    </div>
                    <div>
                        <h4 style="font-size: 0.9rem; font-weight: 800; color: #18181B; margin: 0 0 0.15rem 0;">
                            <?= htmlspecialchars($nb['title']) ?>
                        </h4>
                        <p style="font-size: 0.775rem; color: #71717A; margin: 0; line-height: 1.35;">
                            <?= htmlspecialchars($nb['description']) ?>
                        </p>
                    </div>
                </div>
                <div>
                    <div style="display: flex; align-items: center; justify-content: space-between; font-size: 0.75rem; color: #71717A; margin-bottom: 0.35rem;" class="font-mono">
                        <span>Progres</span>
                        <span><?= (int)($nb['progress'] ?? 0) ?> / <?= (int)($nb['max'] ?? 1) ?></span>
                    </div>
                    <div style="width: 100%; height: 6px; background-color: #E5E7EB; border-radius: 9999px; overflow: hidden;">
                        <div style="height: 100%; width: <?= min(100, (int)($nb['percent'] ?? 0)) ?>%; background-color: #18181B; border-radius: 9999px;"></div>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div style="padding: 1.25rem; text-align: center; background-color: #FAFAFA; border: 1px solid #E5E7EB; border-radius: 8px;">
                <i data-lucide="trophy" style="width: 28px; height: 28px; color: #18181B; margin-bottom: 0.5rem;"></i>
                <p style="font-size: 0.85rem; font-weight: 700; color: #18181B; margin: 0 0 0.2rem 0;">Semua Lencana Terbuka!</p>
                <p style="font-size: 0.775rem; color: #71717A; margin: 0;">Anda telah menguasai seluruh pencapaian kuis MikroTik.</p>
            </div>
        <?php endif; ?>
    </div>
</div>
</div>

<?php require_once dirname(__DIR__) . '/templates/footer.php'; ?>