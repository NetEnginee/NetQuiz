<?php
$score = isset($score) ? (int) $score : 0;
$correct = isset($correct) ? (int) $correct : 0;
$total = isset($total) ? (int) $total : 0;
$quiz = $quiz ?? [
    'id' => 0,
    'title' => 'NetQuiz',
    'category' => 'MikroTik'
];

$isPassed = $score >= 70;

require_once dirname(__DIR__) . '/templates/header.php';
?>

<div style="max-width: 640px; margin: 0 auto;">
    <!-- Breadcrumb -->
    <nav class="admin-breadcrumb-nav" aria-label="Breadcrumb" style="margin-bottom: 1.5rem; justify-content: center;">
        <span class="breadcrumb-item">Kuis</span>
        <span class="breadcrumb-separator">/</span>
        <span class="breadcrumb-item"><?= htmlspecialchars($quiz['title'] ?? 'Ujian') ?></span>
        <span class="breadcrumb-separator">/</span>
        <span class="breadcrumb-active">Hasil Skor</span>
    </nav>

    <!-- Score Card Geist -->
    <div class="supabase-panel-card" style="padding: 2.5rem 2rem; text-align: center;">
        <span class="corner-crosshair corner-tl">+</span>
        <span class="corner-crosshair corner-tr">+</span>
        <span class="corner-crosshair corner-bl">+</span>
        <span class="corner-crosshair corner-br">+</span>

        <!-- Score Badge Circle -->
        <div style="width: 88px; height: 88px; border-radius: 50%; background-color: #18181B; color: #FFFFFF; display: flex; flex-direction: column; align-items: center; justify-content: center; margin: 0 auto 1.5rem; border: 4px solid #E5E7EB; box-shadow: 0 4px 12px rgba(0,0,0,0.06);">
            <span style="font-size: 1.85rem; font-weight: 800; font-family: var(--font-mono); line-height: 1;"><?= (int)$score ?></span>
            <span style="font-size: 0.65rem; color: #A1A1AA; font-weight: 600; text-transform: uppercase; margin-top: 2px;">Skor</span>
        </div>

        <h2 style="font-family: var(--font-heading); font-size: 1.4rem; font-weight: 800; color: #18181B; margin: 0 0 0.35rem 0;">
            <?= $score >= 80 ? 'Luar Biasa! 🎉' : ($score >= 60 ? 'Kerja Bagus! 👍' : 'Tetap Semangat! 💪') ?>
        </h2>
        <p style="font-size: 0.875rem; color: #71717A; margin: 0 0 2rem 0; line-height: 1.5;">
            <?= $score >= 80 ? 'Anda memiliki pemahaman konfigurasi RouterOS yang sangat baik.' : ($score >= 60 ? 'Dasar pemahaman Anda sudah baik. Pelajari kembali materi untuk memaksimalkan skor.' : 'Pelajari kembali panduan materi topik ini untuk memperdalam pemahaman.') ?>
        </p>

        <!-- Stats Breakdown Grid -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; margin-bottom: 2rem; text-align: left;">
            <div style="padding: 0.85rem 1rem; background-color: #FAFAFA; border: 1px solid #E5E7EB; border-radius: 6px;">
                <span style="font-size: 0.75rem; color: #71717A; display: block; margin-bottom: 0.2rem;">Kuis Diuji</span>
                <span style="font-size: 0.9rem; font-weight: 700; color: #18181B;"><?= htmlspecialchars($quiz['title'] ?? 'Kuis') ?></span>
            </div>
            <div style="padding: 0.85rem 1rem; background-color: #FAFAFA; border: 1px solid #E5E7EB; border-radius: 6px;">
                <span style="font-size: 0.75rem; color: #71717A; display: block; margin-bottom: 0.2rem;">Jawaban Benar</span>
                <span style="font-size: 0.9rem; font-weight: 700; color: #18181B;" class="font-mono"><?= (int)$correct ?> / <?= (int)$total ?> Soal</span>
            </div>
        </div>

        <!-- Action CTA Buttons -->
        <div style="display: flex; align-items: center; justify-content: center; gap: 0.75rem; flex-wrap: wrap;">
            <a href="<?= BASE_URL ?>/quiz" class="btn-secondary-outline" style="font-size: 0.85rem; padding: 0.5rem 1.15rem;">
                <i data-lucide="arrow-left" style="width: 14px; height: 14px;"></i>
                <span>Katalog Kuis</span>
            </a>
            <?php if (!empty($quiz['id'])): ?>
                <a href="<?= BASE_URL ?>/quiz/review/<?= (int)$quiz['id'] ?>" class="btn-primary-black" style="font-size: 0.85rem; padding: 0.5rem 1.25rem;">
                    <i data-lucide="eye" style="width: 14px; height: 14px;"></i>
                    <span>Review Jawaban</span>
                </a>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/templates/footer.php'; ?>
