<?php
$groupedMaterials = $groupedMaterials ?? [];
require_once dirname(__DIR__) . '/templates/header.php';
?>

<!-- Breadcrumb & Header -->
<div style="margin-bottom: 2rem;">
    <nav class="admin-breadcrumb-nav" aria-label="Breadcrumb" style="margin-bottom: 0.5rem;">
        <span class="breadcrumb-item">Siswa</span>
        <span class="breadcrumb-separator">/</span>
        <span class="breadcrumb-active">Materi Belajar</span>
    </nav>
    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h1 style="font-family: var(--font-heading); font-size: 1.5rem; font-weight: 800; color: #18181B; margin: 0 0 0.25rem 0; letter-spacing: -0.02em;">
                Pusat Materi Pembelajaran MikroTik
            </h1>
            <p style="font-size: 0.875rem; color: #71717A; margin: 0;">
                Kumpulan modul referensi, teori jaringan, dan panduan sintaks RouterOS CLI.
            </p>
        </div>
    </div>
</div>

<?php if (empty($groupedMaterials)): ?>
    <div class="supabase-panel-card" style="padding: 3rem 1.5rem; text-align: center;">
        <span class="corner-crosshair corner-tl">+</span>
        <span class="corner-crosshair corner-tr">+</span>
        <span class="corner-crosshair corner-bl">+</span>
        <span class="corner-crosshair corner-br">+</span>
        <div style="width: 44px; height: 44px; border-radius: 50%; background-color: #F4F4F5; margin: 0 auto 0.75rem; display: flex; align-items: center; justify-content: center; color: #71717A;">
            <i data-lucide="book-open" style="width: 22px; height: 22px;"></i>
        </div>
        <h3 style="font-size: 1rem; font-weight: 800; color: #18181B; margin: 0 0 0.25rem 0;">Belum Ada Materi Tersedia</h3>
        <p style="font-size: 0.85rem; color: #71717A; margin: 0;">Materi pembelajaran sedang disiapkan oleh administrator.</p>
    </div>
<?php else: ?>
    <div style="display: flex; flex-direction: column; gap: 2.5rem;">
        <?php foreach ($groupedMaterials as $category => $items): ?>
            <div>
                <!-- Category Heading -->
                <div style="display: flex; align-items: center; gap: 0.65rem; margin-bottom: 1rem; padding-bottom: 0.5rem; border-bottom: 1px solid #E5E7EB;">
                    <i data-lucide="folder" style="width: 18px; height: 18px; color: #18181B;"></i>
                    <h2 style="font-family: var(--font-heading); font-size: 1.15rem; font-weight: 800; color: #18181B; margin: 0;">
                        <?= htmlspecialchars($category) ?>
                    </h2>
                    <span class="font-mono" style="font-size: 0.75rem; color: #71717A;">(<?= count($items) ?> Modul)</span>
                </div>

                <!-- Materials Grid -->
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 1.25rem;">
                    <?php foreach ($items as $material): ?>
                        <?php
                        $cleanText = strip_tags($material['content'] ?? '');
                        $wordCount = str_word_count($cleanText);
                        $readTime = max(1, (int)ceil($wordCount / 180));
                        ?>
                        <div class="supabase-panel-card" style="padding: 1.25rem; display: flex; flex-direction: column; justify-content: space-between;">
                            <span class="corner-crosshair corner-tl">+</span>
                            <span class="corner-crosshair corner-tr">+</span>
                            <span class="corner-crosshair corner-bl">+</span>
                            <span class="corner-crosshair corner-br">+</span>

                            <div>
                                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.75rem;">
                                    <span class="status-badge" style="background-color: #F4F4F5; font-size: 0.7rem;">
                                        <?= htmlspecialchars($material['difficulty'] ?? 'Umum') ?>
                                    </span>
                                    <span class="font-mono" style="font-size: 0.725rem; color: #71717A;">
                                        ~<?= $readTime ?> menit baca
                                    </span>
                                </div>

                                <h3 style="font-family: var(--font-heading); font-size: 1rem; font-weight: 800; color: #18181B; margin: 0 0 0.4rem 0; line-height: 1.35;">
                                    <?= htmlspecialchars($material['title']) ?>
                                </h3>
                                <p style="font-size: 0.825rem; color: #52525B; margin: 0 0 1rem 0; line-height: 1.45; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;">
                                    <?= htmlspecialchars($cleanText) ?>
                                </p>
                            </div>

                            <div style="padding-top: 0.75rem; border-top: 1px solid #E5E7EB; display: flex; align-items: center; justify-content: space-between;">
                                <span class="font-mono" style="font-size: 0.75rem; color: #71717A;">
                                    <?= date('d M Y', strtotime($material['created_at'] ?? 'now')) ?>
                                </span>
                                <a href="<?= BASE_URL ?>/learn/<?= (int)$material['id'] ?>" class="btn-primary-black" style="font-size: 0.775rem; padding: 0.35rem 0.75rem;">
                                    <span>Baca Modul</span>
                                    <i data-lucide="arrow-right" style="width: 12px; height: 12px;"></i>
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php require_once dirname(__DIR__) . '/templates/footer.php'; ?>
