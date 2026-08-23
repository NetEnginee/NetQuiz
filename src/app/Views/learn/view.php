<?php
$material = $material ?? [
    'id' => 0,
    'title' => 'Materi',
    'category' => 'Routing',
    'difficulty' => 'Mudah',
    'created_at' => date('Y-m-d H:i:s'),
    'content' => ''
];
$otherMaterials = $otherMaterials ?? [];
$cleanText = strip_tags($material['content'] ?? '');
$wordCount = str_word_count($cleanText);
$readTime = max(1, (int)ceil($wordCount / 180));

$diff = strtolower($material['difficulty'] ?? 'mudah');
$diffClass = match ($diff) {
    'sedang' => 'diff-sedang',
    'sulit', 'hard' => 'diff-sulit',
    default => 'diff-mudah'
};

$categoryThemes = [
    'Routing' => ['color' => '#60a5fa', 'pillClass' => 'cat-routing', 'icon' => 'pixel-router'],
    'Firewall & NAT' => ['color' => '#fbbf24', 'pillClass' => 'cat-firewall', 'icon' => 'pixel-robot'],
    'Wireless' => ['color' => '#f472b6', 'pillClass' => 'cat-wireless', 'icon' => 'pixel-sparkle'],
    'Network Management' => ['color' => '#50e3c2', 'pillClass' => 'cat-network', 'icon' => 'pixel-computer']
];

$theme = $categoryThemes[$material['category']] ?? [
    'color' => '#50e3c2',
    'pillClass' => 'cat-network',
    'icon' => 'pixel-book'
];

require_once dirname(__DIR__) . '/templates/header.php';
?>

<div class="learn-page-container">

    <!-- 1. Breadcrumb Navigation -->
    <div class="mb-4">
        <?= renderBreadcrumb([
            ['label' => 'Student', 'url' => BASE_URL . '/'],
            ['label' => 'Materi Pembelajaran', 'url' => BASE_URL . '/learn'],
            ['label' => $material['category']],
            ['label' => $material['title']]
        ]) ?>
    </div>

    <!-- 2. Main 2-Column Reader Layout -->
    <div class="learn-reader-layout">

        <!-- LEFT COLUMN: Main Article Content -->
        <article class="learn-article-panel">
            <span class="panel-crosshair corner-tl">+</span>
            <span class="panel-crosshair corner-tr">+</span>
            <span class="panel-crosshair corner-bl">+</span>
            <span class="panel-crosshair corner-br">+</span>

            <!-- Article Header Meta -->
            <div class="article-header-meta">
                <span class="learn-category-pill <?= $theme['pillClass'] ?> font-mono">
                    <?= htmlspecialchars($material['category']) ?>
                </span>
                <span class="learn-diff-badge <?= $diffClass ?>">
                    Level: <?= htmlspecialchars($material['difficulty'] ?? 'Mudah') ?>
                </span>
                <span class="font-mono text-xs text-zinc-400">
                    ⏱ ~<?= $readTime ?> menit baca
                </span>
                <span class="font-mono text-xs text-zinc-500">
                    • Dipublikasikan <?= date('d M Y', strtotime($material['created_at'])) ?>
                </span>
            </div>

            <!-- Article Title -->
            <h1 class="article-main-title font-sans">
                <?= htmlspecialchars($material['title']) ?>
            </h1>

            <!-- Article Body Content -->
            <div class="material-content-body font-sans">
                <?= $material['content'] ?>
            </div>

            <!-- Reader Bottom Actions -->
            <div class="reader-bottom-bar font-mono">
                <a href="<?= BASE_URL ?>/learn" class="btn-hero-secondary text-xs" onclick="window.playPixelSound && window.playPixelSound('click');">
                    <span>← Kembali ke Materi</span>
                </a>
            </div>
        </article>

        <!-- RIGHT COLUMN: Related Modules Only -->
        <aside class="learn-sidebar-column">

            <!-- Card 1: Related Materials in same category -->
            <div class="learn-sidebar-card">
                <span class="panel-crosshair corner-tl">+</span>
                <span class="panel-crosshair corner-tr">+</span>
                <span class="panel-crosshair corner-bl">+</span>
                <span class="panel-crosshair corner-br">+</span>

                <div class="sidebar-card-header">
                    <h3 class="sidebar-card-title font-sans">
                        Materi Terkait
                    </h3>
                    <span class="font-mono text-[11px] text-zinc-500">
                        <?= htmlspecialchars($material['category']) ?>
                    </span>
                </div>

                <?php if (empty($otherMaterials)): ?>
                    <p class="font-mono text-xs text-zinc-500 m-0 py-2">
                        Belum ada modul terkait lainnya dalam kategori ini.
                    </p>
                <?php else: ?>
                    <div class="related-modules-list font-sans">
                        <?php foreach (array_slice($otherMaterials, 0, 4) as $om): ?>
                            <?php
                            $omDiff = strtolower($om['difficulty'] ?? 'mudah');
                            $omDiffClass = match ($omDiff) {
                                'sedang' => 'diff-sedang',
                                'sulit', 'hard' => 'diff-sulit',
                                default => 'diff-mudah'
                            };
                            ?>
                            <a href="<?= BASE_URL ?>/learn/<?= (int)$om['id'] ?>"
                                class="related-module-item"
                                onclick="window.playPixelSound && window.playPixelSound('click');">
                                <h4 class="related-item-title">
                                    <?= htmlspecialchars($om['title']) ?>
                                </h4>
                                <div class="related-item-meta font-mono">
                                    <span class="learn-diff-badge <?= $omDiffClass ?>" style="padding: 0.1rem 0.35rem; font-size: 0.625rem;">
                                        <?= htmlspecialchars($om['difficulty'] ?? 'Mudah') ?>
                                    </span>
                                    <span><?= htmlspecialchars($om['category'] ?? '') ?></span>
                                    <span class="ml-auto text-zinc-500">→</span>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

        </aside>

    </div>

</div>

<!-- Learning Engine JS Module -->
<script src="<?= function_exists('assetUrl') ? assetUrl('/js/learn-catalog.js') : (BASE_URL . '/js/learn-catalog.js') ?>"></script>

<?php require_once dirname(__DIR__) . '/templates/footer.php'; ?>