<?php
$quiz = $quiz ?? [
    'id' => 0,
    'title' => 'Kuis',
    'category' => 'MikroTik',
    'questions' => []
];
$userAnswers = $userAnswers ?? [];
$score = isset($score) ? (int) $score : 0;
$questions = $quiz['questions'] ?? [];

require_once dirname(__DIR__) . '/templates/header.php';
?>

<!-- Quiz Review Container (Max Width 860px) -->
<div style="max-width: 860px; margin: 0 auto;">
    <!-- Breadcrumb Header -->
    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.5rem;">
        <div>
            <nav class="admin-breadcrumb-nav" aria-label="Breadcrumb" style="margin-bottom: 0.25rem;">
                <span class="breadcrumb-item">Katalog</span>
                <span class="breadcrumb-separator">/</span>
                <span class="breadcrumb-item"><?= htmlspecialchars($quiz['title']) ?></span>
                <span class="breadcrumb-separator">/</span>
                <span class="breadcrumb-active">Review Pembahasan</span>
            </nav>
            <h1 style="font-family: var(--font-heading); font-size: 1.35rem; font-weight: 800; color: #18181B; margin: 0;">
                Pembahasan Soal & Jawaban
            </h1>
        </div>

        <div style="display: flex; align-items: center; gap: 0.75rem;">
            <div style="display: inline-flex; align-items: center; gap: 0.45rem; padding: 0.35rem 0.75rem; background-color: #18181B; color: #FFFFFF; border-radius: 6px; font-family: var(--font-mono); font-size: 0.85rem; font-weight: 700;">
                <span>Skor: <?= (int)$score ?>%</span>
            </div>
            <a href="<?= BASE_URL ?>/quiz" class="btn-secondary-outline" style="font-size: 0.8rem; padding: 0.4rem 0.75rem;">
                <i data-lucide="arrow-left" style="width: 13px; height: 13px;"></i>
                <span>Kembali</span>
            </a>
        </div>
    </div>

    <!-- MAIN REVIEW CARD -->
    <div class="supabase-panel-card" style="padding: 2rem;">
        <span class="corner-crosshair corner-tl">+</span>
        <span class="corner-crosshair corner-tr">+</span>
        <span class="corner-crosshair corner-bl">+</span>
        <span class="corner-crosshair corner-br">+</span>

        <!-- QUESTIONS CAROUSEL STACK -->
        <div id="quiz-review-stack">
            <?php foreach ($questions as $index => $q): ?>
                <?php
                $qNum = $index + 1;
                $userAns = strtoupper((string)($userAnswers[$index] ?? ''));
                $correctAns = strtoupper((string)($q['correct'] ?? ''));
                $isCorrect = ($userAns !== '' && $userAns === $correctAns);
                $options = $q['options'] ?? [
                    'A' => $q['option_a'] ?? '',
                    'B' => $q['option_b'] ?? '',
                    'C' => $q['option_c'] ?? '',
                    'D' => $q['option_d'] ?? ''
                ];
                ?>
                <div class="question-block <?= $index === 0 ? 'active' : '' ?>" data-index="<?= $index ?>" style="<?= $index === 0 ? 'display: block;' : 'display: none;' ?>">
                    <!-- Header -->
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.25rem; padding-bottom: 0.75rem; border-bottom: 1px solid #E5E7EB;">
                        <span style="font-size: 0.8rem; font-weight: 800; color: #71717A; text-transform: uppercase; letter-spacing: 0.05em;">
                            Pertanyaan <?= $qNum ?> dari <?= count($questions) ?>
                        </span>
                        <?php if ($isCorrect): ?>
                            <span class="status-badge status-active" style="font-size: 0.75rem;">
                                <i data-lucide="check" style="width: 12px; height: 12px; margin-right: 3px;"></i>
                                Jawaban Anda Benar
                            </span>
                        <?php else: ?>
                            <span class="status-badge" style="background-color: #FEE2E2; color: #991B1B; font-size: 0.75rem;">
                                <i data-lucide="x" style="width: 12px; height: 12px; margin-right: 3px;"></i>
                                <?= $userAns !== '' ? "Jawaban Anda Salah (Pilihan {$userAns})" : "Tidak Dijawab" ?>
                            </span>
                        <?php endif; ?>
                    </div>

                    <!-- Statement -->
                    <div style="font-size: 1.05rem; font-weight: 700; color: #18181B; margin-bottom: 1.5rem; line-height: 1.5;">
                        <?= nl2br(htmlspecialchars($q['question'])) ?>
                    </div>

                    <?php if (!empty($q['image_path'])): ?>
                        <div style="margin-bottom: 1.5rem; border: 1px solid #E5E7EB; border-radius: 6px; overflow: hidden; max-height: 300px; display: flex; align-items: center; justify-content: center; background-color: #FAFAFA;">
                            <img src="<?= BASE_URL ?>/<?= htmlspecialchars($q['image_path']) ?>" alt="Soal Gambar" style="max-width: 100%; max-height: 300px; object-fit: contain;">
                        </div>
                    <?php endif; ?>

                    <!-- Options List -->
                    <div style="display: flex; flex-direction: column; gap: 0.75rem; margin-bottom: 1.75rem;">
                        <?php foreach (['A', 'B', 'C', 'D'] as $optKey): ?>
                            <?php
                            $optText = $options[$optKey] ?? '';
                            $isThisCorrect = ($optKey === $correctAns);
                            $isThisUserChoice = ($optKey === $userAns);

                            $bgColor = '#FAFAFA';
                            $borderColor = '#E5E7EB';
                            $badgeBg = '#FFFFFF';
                            $badgeColor = '#18181B';
                            $badgeBorder = '#D4D4D8';

                            if ($isThisCorrect) {
                                $bgColor = '#F0FDF4';
                                $borderColor = '#86EFAC';
                                $badgeBg = '#16A34A';
                                $badgeColor = '#FFFFFF';
                                $badgeBorder = '#16A34A';
                            } elseif ($isThisUserChoice && !$isCorrect) {
                                $bgColor = '#FEF2F2';
                                $borderColor = '#FCA5A5';
                                $badgeBg = '#DC2626';
                                $badgeColor = '#FFFFFF';
                                $badgeBorder = '#DC2626';
                            }
                            ?>
                            <div style="display: flex; align-items: center; gap: 1rem; padding: 0.85rem 1.15rem; background-color: <?= $bgColor ?>; border: 1px solid <?= $borderColor ?>; border-radius: 8px;">
                                <div style="display: flex; align-items: center; justify-content: center; width: 26px; height: 26px; border-radius: 6px; border: 1px solid <?= $badgeBorder ?>; background-color: <?= $badgeBg ?>; color: <?= $badgeColor ?>; font-weight: 800; font-size: 0.8rem; font-family: var(--font-mono); flex-shrink: 0;">
                                    <?= $optKey ?>
                                </div>
                                <span style="font-size: 0.9rem; font-weight: 500; color: #18181B; line-height: 1.4; flex: 1;">
                                    <?= htmlspecialchars($optText) ?>
                                </span>
                                <?php if ($isThisCorrect): ?>
                                    <span style="font-size: 0.725rem; font-weight: 700; color: #16A34A; text-transform: uppercase;">Kunci Benar</span>
                                <?php elseif ($isThisUserChoice): ?>
                                    <span style="font-size: 0.725rem; font-weight: 700; color: #DC2626; text-transform: uppercase;">Pilihan Anda</span>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Explanation Box -->
                    <div style="padding: 1rem 1.25rem; background-color: #F4F4F5; border: 1px solid #E5E7EB; border-radius: 8px;">
                        <div style="display: flex; align-items: center; gap: 0.45rem; margin-bottom: 0.35rem;">
                            <i data-lucide="info" style="width: 14px; height: 14px; color: #18181B;"></i>
                            <span style="font-size: 0.8rem; font-weight: 800; color: #18181B; text-transform: uppercase;">Penjelasan Jawaban:</span>
                        </div>
                        <p style="font-size: 0.85rem; color: #52525B; margin: 0; line-height: 1.5;">
                            <?= !empty($q['explanation']) ? nl2br(htmlspecialchars($q['explanation'])) : 'Tidak ada penjelasan tambahan untuk butir soal ini.' ?>
                        </p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- NAVIGATION CONTROLS -->
        <div style="display: flex; align-items: center; justify-content: space-between; padding-top: 1.25rem; margin-top: 1.5rem; border-top: 1px solid #E5E7EB;">
            <button type="button" id="btn-prev" class="btn-secondary-outline" style="font-size: 0.85rem; padding: 0.5rem 1rem;">
                <i data-lucide="arrow-left" style="width: 14px; height: 14px;"></i>
                <span>Sebelumnya</span>
            </button>
            <button type="button" id="btn-next" class="btn-primary-black" style="font-size: 0.85rem; padding: 0.5rem 1.15rem;">
                <span>Selanjutnya</span>
                <i data-lucide="arrow-right" style="width: 14px; height: 14px;"></i>
            </button>
        </div>
    </div>

    <!-- BOTTOM PAGINATION NUMBERS -->
    <div class="supabase-panel-card" style="margin-top: 1.5rem; padding: 1.25rem;">
        <span class="corner-crosshair corner-tl">+</span>
        <span class="corner-crosshair corner-tr">+</span>
        <span class="corner-crosshair corner-bl">+</span>
        <span class="corner-crosshair corner-br">+</span>

        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.85rem;">
            <span style="font-size: 0.775rem; font-weight: 700; color: #71717A; text-transform: uppercase;">Daftar Soal</span>
            <div style="display: flex; align-items: center; gap: 1rem; font-size: 0.75rem; color: #71717A;">
                <span style="display: inline-flex; align-items: center; gap: 0.35rem;">
                    <span style="width: 10px; height: 10px; border-radius: 2px; background-color: #16A34A; display: inline-block;"></span>
                    <span>Benar</span>
                </span>
                <span style="display: inline-flex; align-items: center; gap: 0.35rem;">
                    <span style="width: 10px; height: 10px; border-radius: 2px; background-color: #DC2626; display: inline-block;"></span>
                    <span>Salah</span>
                </span>
            </div>
        </div>

        <div style="display: flex; flex-wrap: wrap; gap: 0.5rem;">
            <?php foreach ($questions as $index => $q): ?>
                <?php
                $userAns = strtoupper((string)($userAnswers[$index] ?? ''));
                $correctAns = strtoupper((string)($q['correct'] ?? ''));
                $isCorrect = ($userAns !== '' && $userAns === $correctAns);
                $btnColor = $isCorrect ? '#16A34A' : '#DC2626';
                ?>
                <button type="button" class="page-number font-mono <?= $index === 0 ? 'current' : '' ?>" data-index="<?= $index ?>" style="width: 36px; height: 36px; border-radius: 6px; border: 1px solid <?= $btnColor ?>; background-color: <?= $btnColor ?>; color: #FFFFFF; font-weight: 700; font-size: 0.8rem; cursor: pointer; transition: all 0.15s ease;">
                    <?= $index + 1 ?>
                </button>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Reviewer JS Module -->
<script src="<?= BASE_URL ?>/js/quiz-review.js?v=<?= time() ?>"></script>

<?php require_once dirname(__DIR__) . '/templates/footer.php'; ?>
