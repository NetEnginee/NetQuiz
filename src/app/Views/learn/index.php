<?php
$groupedMaterials = $groupedMaterials ?? [];
$totalModules = 0;
foreach ($groupedMaterials as $items) {
    $totalModules += count($items);
}

// Category theme mappings
$categoryThemes = [
    'Routing' => [
        'icon' => 'pixel-router',
        'color' => '#60a5fa',
        'themeClass' => 'theme-routing',
        'pillClass' => 'cat-routing'
    ],
    'Firewall & NAT' => [
        'icon' => 'pixel-robot',
        'color' => '#fbbf24',
        'themeClass' => 'theme-firewall',
        'pillClass' => 'cat-firewall'
    ],
    'Wireless' => [
        'icon' => 'pixel-sparkle',
        'color' => '#f472b6',
        'themeClass' => 'theme-wireless',
        'pillClass' => 'cat-wireless'
    ],
    'Network Management' => [
        'icon' => 'pixel-computer',
        'color' => '#50e3c2',
        'themeClass' => 'theme-network',
        'pillClass' => 'cat-network'
    ]
];

require_once dirname(__DIR__) . '/templates/header.php';
?>

<!-- 1. Hero & Breadcrumb Header -->
<section class="dashboard-hero-header">
    <div class="hero-brand-group">
        <div class="hero-title-area">
            <?= renderBreadcrumb([
                ['label' => 'Student', 'url' => BASE_URL . '/'],
                ['label' => 'Materi', 'url' => BASE_URL . '/learn'],

            ]) ?>
        </div>
    </div>
</section>

<div class="learn-page-container pt-2">

    <!-- 1. Category Filter Toolbar (Without Search) -->
    <div class="learn-toolbar-panel">
        <span class="panel-crosshair corner-tl">+</span>
        <span class="panel-crosshair corner-tr">+</span>
        <span class="panel-crosshair corner-bl">+</span>
        <span class="panel-crosshair corner-br">+</span>

        <!-- Category Tabs -->
        <div class="learn-filter-tabs">
            <button type="button"
                class="learn-segment-tab font-mono active"
                data-category="all"
                onclick="window.playPixelSound && window.playPixelSound('click');">
                <span>Semua (<?= $totalModules ?>)</span>
            </button>
            <?php foreach ($groupedMaterials as $category => $items): ?>
                <?php
                $theme = $categoryThemes[$category] ?? [
                    'icon' => 'pixel-router',
                    'color' => '#ffffff',
                    'pillClass' => 'cat-routing'
                ];
                ?>
                <button type="button"
                    class="learn-segment-tab font-mono"
                    data-category="<?= htmlspecialchars($category) ?>"
                    onclick="window.playPixelSound && window.playPixelSound('click');">
                    <span class="w-1.5 h-1.5 rounded-full inline-block" style="background-color: <?= $theme['color'] ?>;"></span>
                    <span><?= htmlspecialchars($category) ?> (<?= count($items) ?>)</span>
                </button>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- 2. Materials Categorized Grid Sections -->
    <?php if (empty($groupedMaterials)): ?>
        <div class="pixel-empty-state empty-panel-full empty-theme-pink">
            <span class="panel-crosshair corner-tl">+</span>
            <span class="panel-crosshair corner-tr">+</span>
            <span class="panel-crosshair corner-bl">+</span>
            <span class="panel-crosshair corner-br">+</span>

            <div class="empty-scene">
                <div class="sparkle-orbit">
                    <span class="sparkle-dot"></span>
                    <span class="sparkle-dot"></span>
                    <span class="sparkle-dot"></span>
                    <span class="sparkle-dot"></span>
                </div>
                <div class="empty-main-icon">
                    <svg class="w-10 h-10 pixelated" viewBox="0 0 16 16">
                        <use href="#pixel-book"></use>
                    </svg>
                </div>
            </div>
            <h3 class="empty-headline">Belum Ada Materi Tersedia</h3>
            <p class="empty-subtext">Paket modul telah diambil oleh hacker. Tunggu beberapa saat sebelum admin mengiriml lagi!</p>
        </div>
    <?php else: ?>
        <div id="learn-catalog-container">
            <?php foreach ($groupedMaterials as $category => $items): ?>
                <?php
                $theme = $categoryThemes[$category] ?? [
                    'icon' => 'pixel-router',
                    'color' => '#ffffff',
                    'themeClass' => 'theme-routing',
                    'pillClass' => 'cat-routing'
                ];
                ?>
                <section class="learn-category-section" data-category="<?= htmlspecialchars($category) ?>">
                    <!-- Section Header -->
                    <div class="category-header-row">
                        <div class="category-title-wrap">
                            <div class="category-icon-box">
                                <svg class="w-5 h-5 pixelated" width="20" height="20" style="color: <?= $theme['color'] ?>;" viewBox="0 0 16 16">
                                    <use href="#<?= $theme['icon'] ?>"></use>
                                </svg>
                            </div>
                            <div>
                                <h2 class="category-title font-sans">
                                    <?= htmlspecialchars($category) ?>
                                </h2>
                            </div>
                        </div>
                        <span class="category-count-badge font-mono">
                            <?= count($items) ?> Modul
                        </span>
                    </div>

                    <!-- Clean Cards Grid for Category (Without Inner Card Icons) -->
                    <div class="learn-material-grid">
                        <?php foreach ($items as $material): ?>
                            <?php
                            $cleanText = strip_tags($material['content'] ?? '');
                            $wordCount = str_word_count($cleanText);
                            $readTime = max(1, (int)ceil($wordCount / 180));
                            $diff = strtolower($material['difficulty'] ?? 'mudah');
                            $diffClass = match ($diff) {
                                'sedang' => 'diff-sedang',
                                'sulit', 'hard' => 'diff-sulit',
                                default => 'diff-mudah'
                            };
                            ?>
                            <a href="<?= BASE_URL ?>/learn/<?= (int)$material['id'] ?>"
                                class="learn-material-card <?= $theme['themeClass'] ?>"
                                data-category="<?= htmlspecialchars($category) ?>"
                                data-title="<?= htmlspecialchars($material['title']) ?>"
                                onclick="window.playPixelSound && window.playPixelSound('click');">

                                <span class="panel-crosshair corner-tl">+</span>
                                <span class="panel-crosshair corner-tr">+</span>
                                <span class="panel-crosshair corner-bl">+</span>
                                <span class="panel-crosshair corner-br">+</span>

                                <!-- Card Content Body -->
                                <div class="material-card-body">
                                    <div class="material-card-top-meta">
                                        <span class="learn-diff-badge <?= $diffClass ?>">
                                            <?= htmlspecialchars($material['difficulty'] ?? 'Mudah') ?>
                                        </span>
                                        <span class="read-action-link font-mono text-xs">
                                            <span>Baca Modul</span>
                                            <span class="arrow-move">→</span>
                                        </span>
                                    </div>

                                    <h3 class="material-card-title font-sans">
                                        <?= htmlspecialchars($material['title']) ?>
                                    </h3>

                                    <p class="material-card-excerpt font-sans">
                                        <?= htmlspecialchars($cleanText) ?>
                                    </p>

                                    <div class="material-card-footer flex items-center justify-between pt-2.5 mt-2 border-t border-zinc-800/80">
                                        <span class="material-card-date font-mono text-xs text-zinc-500">
                                            <?= date('d M Y', strtotime($material['created_at'] ?? 'now')) ?>
                                        </span>
                                        <span class="font-mono text-[11px] text-zinc-400">
                                            ⏱ ~<?= $readTime ?> mnt baca
                                        </span>
                                    </div>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </section>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

</div>

<!-- Learning Engine JS Module -->
<script src="<?= function_exists('assetUrl') ? assetUrl('/js/learn-catalog.js') : (BASE_URL . '/js/learn-catalog.js') ?>"></script>

<?php require_once dirname(__DIR__) . '/templates/footer.php'; ?>