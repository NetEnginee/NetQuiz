<?php require_once dirname(__DIR__) . '/templates/header.php'; ?>

<!-- Custom Styles for Dashboard -->
<link rel="stylesheet" href="<?= BASE_URL ?>/css/dashboard.css?v=<?= time() ?>">

<!-- Breadcrumb Navigation -->
<nav class="breadcrumb"
    style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 1.5rem; font-size: 0.85rem; font-weight: 500; color: #64748b; font-family: 'Plus Jakarta Sans', sans-serif;">
    <span style="color: #0f172a; font-weight: 600;">Dashboard</span>
</nav>

<h2 class="dashboard-section-title">
    <i data-lucide="bar-chart-2"></i>
    Ringkasan Aktivitas
</h2>

<div class="stats-grid">
    <!-- Card 1: Quiz Diselesaikan -->
    <div class="stat-card stat-card-violet">
        <div class="stat-card-header">
            <span>Quiz Diselesaikan</span>
        </div>
        <div class="stat-card-value"><?= htmlspecialchars($stats['completed_quizzes']) ?></div>
        <div class="stat-card-subtext">
            Tingkat Penyelesaian: <strong><?= htmlspecialchars($stats['completion_rate']) ?>%</strong>
        </div>

        <!-- Category Details List (Always Visible) -->
        <div class="details-panel"
            style="display: block; border-top: 1px solid #f1f5f9; margin-top: 0.5rem; padding-top: 0.75rem;">
            <div class="category-list">
                <?php foreach ($stats['categories'] as $categoryName => $count): ?>
                    <div class="category-item">
                        <span class="category-name"><?= htmlspecialchars($categoryName) ?></span>
                        <span class="category-count"><?= htmlspecialchars($count) ?> Quiz</span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Card 2: Total Skor -->
    <div class="stat-card stat-card-teal">
        <div class="stat-card-header">
            <span>Total Skor Akumulasi</span>
        </div>
        <div class="stat-card-value"><?= number_format($stats['total_score']) ?></div>
        <div class="stat-card-subtext">
            Rata-rata: <strong><?= htmlspecialchars($stats['average_score']) ?> poin/quiz</strong>
        </div>

        <!-- Category Scores List (Always Visible) -->
        <div class="details-panel"
            style="display: block; border-top: 1px solid #f1f5f9; margin-top: 0.5rem; padding-top: 0.75rem;">
            <div class="category-list">
                <?php foreach ($stats['category_scores'] as $categoryName => $score): ?>
                    <div class="category-item">
                        <span class="category-name"><?= htmlspecialchars($categoryName) ?></span>
                        <span class="category-count"><?= number_format($score) ?> Poin</span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<!-- ========================================= -->
<!-- Komponen Aktivitas Terbaru (Recent Activity) -->
<!-- ========================================= -->
<div class="stats-grid">
    <div class="dashboard-recent-activity">
        <div class="activity-header">
            <h2 class="dashboard-section-title" style="margin: 0; margin-bottom: 1rem;">
                <i data-lucide="history"></i>
                Aktivitas Terbaru
            </h2>
        </div>

        <div class="activity-card">
            <div class="activity-list">
                <?php
                $recentActivities = $stats['recent_activities'] ?? [];
                if (empty($recentActivities)): ?>
                    <div style="padding: 2.5rem; text-align: center; color: #64748b;">
                        Belum ada aktivitas kuis terbaru.
                    </div>
                <?php else: ?>
                    <?php foreach ($recentActivities as $activity):
                        $category = $activity['category'];
                        $title = "Quiz " . $category;
                        $time = date('d M Y, H:i', strtotime($activity['created_at']));

                        $isFinished = ($activity['status'] === 'finished');
                        $statusText = $isFinished ? 'Selesai' : 'Jeda';
                        ?>

                        <div class="activity-item"
                            style="display: flex; justify-content: space-between; align-items: center; padding: 0.75rem 1rem; border-bottom: 1px solid #f1f5f9; width: 100%; box-sizing: border-box;">
                            <!-- Detail Quiz (Judul & Waktu) -->
                            <div style="display: flex; flex-direction: column; align-items: flex-start; text-align: left;">
                                <span class="activity-title"
                                    style="font-weight: 600; font-size: 0.85rem; color: #0f172a; margin-bottom: 0.25rem; line-height: 1.2;"><?= htmlspecialchars($title) ?></span>
                                <span class="activity-time" style="font-size: 0.7rem; color: #64748b; line-height: 1.2;">
                                    <?= htmlspecialchars($time) ?>
                                </span>
                            </div>

                            <!-- Status -->
                            <div class="activity-status">
                                <?php if ($isFinished): ?>
                                    <span
                                        style="display: inline-block; padding: 4px 12px; border-radius: 20px; font-size: 0.8rem; font-weight: 600; background-color: #dcfce7; color: #166534;">
                                        <?= $statusText ?>
                                    </span>
                                <?php else: ?>
                                    <?php 
                                    $playId = (isset($activity['quiz_id']) && method_exists('\App\Core\Security', 'encryptUrlId')) 
                                        ? \App\Core\Security::encryptUrlId($activity['quiz_id']) 
                                        : ($activity['quiz_id'] ?? '');
                                    ?>
                                    <a href="<?= BASE_URL ?>/quiz/play/<?= $playId ?>"
                                        style="display: inline-flex; align-items: center; gap: 4px; padding: 4px 12px; border-radius: 20px; font-size: 0.8rem; font-weight: 600; background-color: #fef08a; color: #854d0e; text-decoration: none; cursor: pointer; transition: all 0.2s ease; box-shadow: 0 1px 2px rgba(0,0,0,0.05);"
                                        onmouseover="this.style.backgroundColor='#fde047'"
                                        onmouseout="this.style.backgroundColor='#fef08a'">
                                        <?= $statusText ?> <i data-lucide="play-circle" style="width: 14px; height: 14px;"></i>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>

                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/templates/footer.php'; ?>