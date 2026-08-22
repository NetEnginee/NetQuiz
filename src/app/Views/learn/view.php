<?php
$material = $material ?? [
    'id' => 0,
    'title' => 'Materi',
    'category' => 'General',
    'difficulty' => 'Umum',
    'created_at' => date('Y-m-d H:i:s'),
    'content' => ''
];
$otherMaterials = $otherMaterials ?? [];
$cleanText = strip_tags($material['content'] ?? '');
$wordCount = str_word_count($cleanText);
$readTime = max(1, (int)ceil($wordCount / 180));

require_once dirname(__DIR__) . '/templates/header.php';
?>

<!-- Material View Container -->
<div style="margin-bottom: 1.5rem;">
    <?= renderBreadcrumb([
        ['label' => 'Siswa', 'url' => BASE_URL . '/'],
        ['label' => 'Materi', 'url' => BASE_URL . '/learn'],
        ['label' => $material['category']],
        ['label' => $material['title']]
    ]) ?>
</div>

<!-- 2-Column Article Layout -->
<div style="display: grid; grid-template-columns: 2.2fr 1fr; gap: 1.5rem; align-items: start;">
    <!-- Main Article Panel -->
    <article class="supabase-panel-card" style="padding: 2.5rem 2rem;">
        <span class="corner-crosshair corner-tl">+</span>
        <span class="corner-crosshair corner-tr">+</span>
        <span class="corner-crosshair corner-bl">+</span>
        <span class="corner-crosshair corner-br">+</span>

        <!-- Article Meta -->
        <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1rem; flex-wrap: wrap;">
            <span class="status-badge" style="background-color: #F4F4F5; font-size: 0.75rem;"><?= htmlspecialchars($material['category']) ?></span>
            <span class="status-badge" style="background-color: #F4F4F5; font-size: 0.75rem;"><?= htmlspecialchars($material['difficulty'] ?? 'Umum') ?></span>
            <span style="font-size: 0.75rem; color: #71717A;" class="font-mono">~<?= $readTime ?> menit baca</span>
            <span style="font-size: 0.75rem; color: #71717A;" class="font-mono">• <?= date('d M Y', strtotime($material['created_at'])) ?></span>
        </div>

        <h1 style="font-family: var(--font-heading); font-size: 1.75rem; font-weight: 800; color: #18181B; margin: 0 0 1.5rem 0; line-height: 1.3; letter-spacing: -0.02em;">
            <?= htmlspecialchars($material['title']) ?>
        </h1>

        <!-- Article Content Styled for Clean High Readability -->
        <div class="material-content-body" style="font-size: 1rem; line-height: 1.75; color: #18181B; text-rendering: optimizeLegibility; -webkit-font-smoothing: antialiased;">
            <style>
                .material-content-body h2 {
                    font-size: 1.35rem;
                    font-weight: 800;
                    color: #09090B;
                    margin: 2rem 0 0.75rem 0;
                    letter-spacing: -0.02em;
                    line-height: 1.3;
                }

                .material-content-body h3 {
                    font-size: 1.15rem;
                    font-weight: 700;
                    color: #09090B;
                    margin: 1.75rem 0 0.5rem 0;
                    letter-spacing: -0.015em;
                }

                .material-content-body p {
                    margin-bottom: 1.25rem;
                    color: #27272A;
                    font-size: 0.975rem;
                    line-height: 1.75;
                }

                .material-content-body pre {
                    background: #09090B;
                    color: #38BDF8;
                    padding: 1.15rem 1.35rem;
                    border-radius: 8px;
                    font-family: var(--font-mono);
                    font-size: 0.875rem;
                    line-height: 1.6;
                    overflow-x: auto;
                    margin: 1.5rem 0;
                    border: 1px solid #27272A;
                    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
                }

                .material-content-body code {
                    font-family: var(--font-mono);
                    font-size: 0.875em;
                }

                .material-content-body p code,
                .material-content-body li code {
                    background: #F4F4F5;
                    color: #09090B;
                    padding: 2px 6px;
                    border-radius: 4px;
                    border: 1px solid #E4E4E7;
                    font-weight: 600;
                }

                .material-content-body ul,
                .material-content-body ol {
                    margin: 0.75rem 0 1.25rem 1.5rem;
                    color: #27272A;
                    font-size: 0.975rem;
                    line-height: 1.7;
                }

                .material-content-body li {
                    margin-bottom: 0.45rem;
                }
            </style>
            <?= $material['content'] ?>
        </div>

        <div style="margin-top: 3rem; padding-top: 1.5rem; border-top: 1px solid #E5E7EB; display: flex; align-items: center">
            <a href="<?= BASE_URL ?>/learn" class="btn-secondary-outline" style="font-size: 0.85rem; padding: 0.45rem 0.85rem; text-decoration: none;">
                <i data-lucide="arrow-left" style="width: 14px; height: 14px;"></i>
                <span>Kembali ke Materi</span>
            </a>
        </div>
    </article>

    <!-- Sidebar: Related Materials -->
    <aside style="display: flex; flex-direction: column; gap: 1.5rem;">
        <div class="supabase-panel-card" style="padding: 1.5rem;">
            <span class="corner-crosshair corner-tl">+</span>
            <span class="corner-crosshair corner-tr">+</span>
            <span class="corner-crosshair corner-bl">+</span>
            <span class="corner-crosshair corner-br">+</span>

            <h3 style="font-family: var(--font-heading); font-size: 1rem; font-weight: 800; color: #18181B; margin: 0 0 0.2rem 0;">
                Materi Terkait
            </h3>
            <p style="font-size: 0.775rem; color: #71717A; margin: 0 0 1rem 0;">Topik lain dalam kategori <?= htmlspecialchars($material['category']) ?>.</p>

            <?php if (empty($otherMaterials)): ?>
                <p style="font-size: 0.825rem; color: #71717A; margin: 0;">Tidak ada materi terkait lainnya dalam kategori ini.</p>
            <?php else: ?>
                <div style="display: flex; flex-direction: column; gap: 0.65rem;">
                    <?php foreach ($otherMaterials as $om): ?>
                        <a href="<?= BASE_URL ?>/learn/<?= (int)$om['id'] ?>" style="display: block; padding: 0.75rem; background-color: #FAFAFA; border: 1px solid #E5E7EB; border-radius: 6px; text-decoration: none; transition: all 0.15s ease;" onmouseover="this.style.borderColor='#18181B';" onmouseout="this.style.borderColor='#E5E7EB';">
                            <h4 style="font-size: 0.85rem; font-weight: 700; color: #18181B; margin: 0 0 0.25rem 0; line-height: 1.35;">
                                <?= htmlspecialchars($om['title']) ?>
                            </h4>
                            <span class="font-mono" style="font-size: 0.725rem; color: #71717A;">Baca Panduan &rarr;</span>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </aside>
</div>

<?php require_once dirname(__DIR__) . '/templates/footer.php'; ?>