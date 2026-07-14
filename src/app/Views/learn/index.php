<?php require_once dirname(__DIR__) . '/templates/header.php'; ?>

<!-- Custom Styles for Learn Page -->
<link rel="stylesheet" href="<?= BASE_URL ?>/css/learn.css?v=<?= time() ?>">

<div class="learn-container">
    <!-- Breadcrumb -->
    <nav class="breadcrumb"
        style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 2rem; font-size: 0.85rem; font-weight: 500; color: #64748b; font-family: 'Plus Jakarta Sans', sans-serif;">
        <span style="color: #64748b;">Dashboard</span>
        <span style="color: #cbd5e1;">/</span>
        <span style="color: #0f172a; font-weight: 600;">Belajar</span>
    </nav>

    <!-- Header Section -->
    <div class="learn-greeting">
        <h1 class="learn-title">Materi Pembelajaran</h1>
        <p class="learn-subtitle">Tingkatkan pemahaman Anda sebelum memulai kuis RouterOS MikroTik.</p>
    </div>

    <!-- Empty State -->
    <?php if (empty($groupedMaterials)): ?>
        <div style="background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 3rem; text-align: center;">
            <p style="color: #64748b; font-size: 0.95rem; margin: 0;">Belum ada materi pembelajaran yang tersedia saat ini.</p>
        </div>
    <?php else: ?>
        <!-- Grouped Materials List -->
        <?php foreach ($groupedMaterials as $category => $items): ?>
            <div class="category-section">
                <div class="category-title-header">
                    <i data-lucide="folder" style="width: 1.25rem; height: 1.25rem; color: #7c3aed;"></i>
                    <?= htmlspecialchars($category) ?>
                    <span class="category-badge"><?= count($items) ?> Materi</span>
                </div>

                <div class="materials-grid">
                    <?php foreach ($items as $material): 
                        // Clean excerpt from HTML
                        $excerpt = strip_tags($material['content']);
                    ?>
                        <a href="<?= BASE_URL ?>/learn/<?= $material['id'] ?>" class="material-card">
                            <div>
                                <div class="material-card-header" style="justify-content: flex-end; margin-bottom: 0.5rem;">
                                    <i data-lucide="book-open" style="width: 1.1rem; height: 1.1rem; color: #94a3b8;"></i>
                                </div>
                                <h3 class="material-title"><?= htmlspecialchars($material['title']) ?></h3>
                                <p class="material-excerpt"><?= htmlspecialchars($excerpt) ?></p>
                            </div>
                            
                            <div class="material-footer">
                                <span class="read-btn">
                                    Mulai Belajar 
                                    <i data-lucide="arrow-right" style="width: 0.9rem; height: 0.9rem;"></i>
                                </span>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php require_once dirname(__DIR__) . '/templates/footer.php'; ?>
