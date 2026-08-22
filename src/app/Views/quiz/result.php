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

<div style="max-width: 600px; margin: 0 auto;">
    <div style="display: flex; justify-content: center; margin-bottom: 1.5rem;">
        <?= renderBreadcrumb([
            ['label' => 'Siswa', 'url' => BASE_URL . '/'],
            ['label' => 'Kuis', 'url' => BASE_URL . '/quiz'],
            ['label' => $quiz['title'] ?? 'Ujian'],
            ['label' => 'Hasil Skor']
        ]) ?>
    </div>

    <!-- Score Card Geist -->
    <div class="supabase-panel-card" style="padding: 2.5rem 2rem; text-align: center;">
        <span class="corner-crosshair corner-tl">+</span>
        <span class="corner-crosshair corner-tr">+</span>
        <span class="corner-crosshair corner-bl">+</span>
        <span class="corner-crosshair corner-br">+</span>

        <!-- Passing Status Badge -->
        <div style="margin-bottom: 1.25rem;">
            <?php if ($isPassed): ?>
                <span class="status-badge" style="background-color: #ECFDF5; border: 1px solid #10B981; color: #047857; font-size: 0.775rem; padding: 4px 10px; font-weight: 700;">
                    Kompeten (Lulus Passing Grade)
                </span>
            <?php else: ?>
                <span class="status-badge" style="background-color: #FEF2F2; border: 1px solid #EF4444; color: #B91C1C; font-size: 0.775rem; padding: 4px 10px; font-weight: 700;">
                    Perlu Evaluasi Ulang (&lt; 70%)
                </span>
            <?php endif; ?>
        </div>

        <!-- Score Badge Monospace Circle -->
        <div style="width: 96px; height: 96px; border-radius: 50%; background-color: #18181B; color: #FFFFFF; display: flex; flex-direction: column; align-items: center; justify-content: center; margin: 0 auto 1.5rem; border: 4px solid #E5E7EB; box-shadow: 0 4px 16px rgba(0,0,0,0.06);">
            <span style="font-size: 2rem; font-weight: 800; font-family: var(--font-mono); line-height: 1;"><?= (int)$score ?></span>
            <span style="font-size: 0.65rem; color: #A1A1AA; font-weight: 700; text-transform: uppercase; margin-top: 2px;">POIN</span>
        </div>

        <h2 style="font-family: var(--font-heading); font-size: 1.35rem; font-weight: 800; color: #18181B; margin: 0 0 0.35rem 0; letter-spacing: -0.02em;">
            <?= $isPassed ? 'Simulasi Ujian Selesai' : 'Evaluasi Simulasi Ujian Selesai' ?>
        </h2>
        <p style="font-size: 0.875rem; color: #71717A; margin: 0 0 2rem 0; line-height: 1.5;">
            <?= $isPassed ? 'Selamat, Anda telah menguasai kompetensi modul topik konfigurasi MikroTik RouterOS ini.' : 'Hasil ujian Anda telah dicatat. Silakan pelajari modul pembahasan untuk memperdalam materi.' ?>
        </p>

        <!-- Stats Breakdown Grid -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; margin-bottom: 2rem; text-align: left;">
            <div style="padding: 0.85rem 1rem; background-color: #FAFAFA; border: 1px solid #E5E7EB; border-radius: 8px;">
                <span style="font-size: 0.725rem; font-weight: 700; color: #71717A; text-transform: uppercase; display: block; margin-bottom: 0.25rem;">Topik Ujian</span>
                <span style="font-size: 0.875rem; font-weight: 700; color: #18181B; line-height: 1.35; display: block; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                    <?= htmlspecialchars($quiz['title'] ?? 'Kuis') ?>
                </span>
            </div>
            <div style="padding: 0.85rem 1rem; background-color: #FAFAFA; border: 1px solid #E5E7EB; border-radius: 8px;">
                <span style="font-size: 0.725rem; font-weight: 700; color: #71717A; text-transform: uppercase; display: block; margin-bottom: 0.25rem;">Akurasi Jawaban</span>
                <span style="font-size: 0.875rem; font-weight: 700; color: #18181B; font-family: var(--font-mono); line-height: 1.35; display: block;">
                    <?= (int)$correct ?> / <?= (int)$total ?> Soal (<?= (int)$score ?>%)
                </span>
            </div>
        </div>

        <!-- Action CTA Buttons -->
        <div style="display: flex; align-items: center; justify-content: center; gap: 0.75rem; flex-wrap: wrap;">
            <a href="<?= BASE_URL ?>/quiz" class="btn-secondary-outline" style="font-size: 0.85rem; padding: 0.5rem 1.15rem;">
                <i data-lucide="arrow-left" style="width: 14px; height: 14px;"></i>
                <span>Kembali ke Kuis</span>
            </a>
            <?php if (!empty($quiz['id'])): ?>
                <a href="<?= BASE_URL ?>/quiz/play/<?= (int)$quiz['id'] ?>" class="btn-secondary-outline" style="font-size: 0.85rem; padding: 0.5rem 1.15rem;">
                    <i data-lucide="rotate-ccw" style="width: 14px; height: 14px;"></i>
                    <span>Ulangi Kuis</span>
                </a>
                <a href="<?= BASE_URL ?>/quiz/review/<?= (int)$quiz['id'] ?>" class="btn-primary-black" style="font-size: 0.85rem; padding: 0.5rem 1.25rem;">
                    <i data-lucide="file-text" style="width: 14px; height: 14px;"></i>
                    <span>Review Pembahasan</span>
                </a>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/templates/footer.php'; ?>
