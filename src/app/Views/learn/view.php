<?php require_once dirname(__DIR__) . '/templates/header.php'; ?>

<!-- Custom Styles for Learn Page -->
<link rel="stylesheet" href="<?= BASE_URL ?>/css/learn.css?v=<?= time() ?>">

<div class="learn-container">
    <!-- Breadcrumb -->
    <nav class="breadcrumb"
        style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 2rem; font-size: 0.85rem; font-weight: 500; color: #64748b; font-family: 'Plus Jakarta Sans', sans-serif;">
        <a href="<?= BASE_URL ?>/learn" style="color: #64748b; text-decoration: none; transition: color 0.15s;" onmouseover="this.style.color='#7c3aed'" onmouseout="this.style.color='#64748b'">Belajar</a>
        <span style="color: #cbd5e1;">/</span>
        <span style="color: #64748b; font-weight: 500;"><?= htmlspecialchars($material['category']) ?></span>
        <span style="color: #cbd5e1;">/</span>
        <span style="color: #0f172a; font-weight: 600; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; max-width: 250px; display: inline-block; vertical-align: bottom;"><?= htmlspecialchars($material['title']) ?></span>
    </nav>

    <div class="material-view-layout">
        <!-- Main Content Area -->
        <main class="material-main-content">
            <div class="material-meta">
                <span class="meta-item">
                    <i data-lucide="tag" style="width: 1rem; height: 1rem;"></i>
                    <?= htmlspecialchars($material['category']) ?>
                </span>
                <span class="meta-item">
                    <i data-lucide="calendar" style="width: 1rem; height: 1rem;"></i>
                    <?= date('d M Y', strtotime($material['created_at'])) ?>
                </span>
            </div>

            <h1 style="font-size: 2rem; font-weight: 800; color: #0f172a; margin-top: 0; margin-bottom: 1.5rem; font-family: 'Plus Jakarta Sans', sans-serif; line-height: 1.25; letter-spacing: -0.02em;">
                <?= htmlspecialchars($material['title']) ?>
            </h1>

            <div class="material-body">
                <?= $material['content'] ?>
            </div>
            
            <!-- Action to go back -->
            <div style="margin-top: 3rem; padding-top: 1.5rem; border-top: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center;">
                <a href="<?= BASE_URL ?>/learn" style="text-decoration: none; display: inline-flex; align-items: center; gap: 0.5rem; font-size: 0.9rem; font-weight: 600; color: #64748b; font-family: 'Plus Jakarta Sans', sans-serif; transition: color 0.15s;" onmouseover="this.style.color='#7c3aed'" onmouseout="this.style.color='#64748b'">
                    <i data-lucide="chevron-left" style="width: 1.1rem; height: 1.1rem;"></i> Kembali ke Daftar Materi
                </a>
            </div>
        </main>

        <!-- Sidebar Area -->
        <aside class="material-sidebar">
            <div class="sidebar-card">
                <h3 class="sidebar-title">Materi Terkait</h3>
                
                <?php if (empty($otherMaterials)): ?>
                    <p style="font-size: 0.85rem; color: #64748b; margin: 0;">Tidak ada materi lain dalam kategori ini.</p>
                <?php else: ?>
                    <div class="sidebar-list">
                        <?php foreach ($otherMaterials as $other): ?>
                            <a href="<?= BASE_URL ?>/learn/<?= $other['id'] ?>" class="sidebar-link">
                                <span class="sidebar-link-title"><?= htmlspecialchars($other['title']) ?></span>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </aside>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/templates/footer.php'; ?>
