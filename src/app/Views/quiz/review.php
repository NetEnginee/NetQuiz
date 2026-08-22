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
$totalQuestions = count($questions);

$correctCount = 0;
$wrongCount = 0;

foreach ($questions as $idx => $q) {
    $uAns = strtoupper((string)($userAnswers[$idx] ?? ''));
    $cAns = strtoupper((string)($q['correct'] ?? ''));
    if ($uAns !== '' && $uAns === $cAns) {
        $correctCount++;
    } else {
        $wrongCount++;
    }
}

require_once dirname(__DIR__) . '/templates/header.php';
?>

<!-- Dynamic Breadcrumb & Top Bar -->
<div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.5rem;">
    <div>
        <?= renderBreadcrumb([
            ['label' => 'Siswa', 'url' => BASE_URL . '/'],
            ['label' => 'Kuis', 'url' => BASE_URL . '/quiz'],
            ['label' => $quiz['title']],
            ['label' => 'Review Pembahasan']
        ]) ?>
        <div style="display: flex; align-items: center; gap: 0.5rem; margin-top: 0.25rem;">
            <span class="status-badge" style="background-color: #F4F4F5; font-size: 0.725rem; font-weight: 700;"><?= htmlspecialchars($quiz['category']) ?></span>
            <span class="status-badge" style="background-color: #18181B; color: #FFFFFF; font-size: 0.725rem; font-weight: 700;">Skor: <?= (int)$score ?>%</span>
        </div>
    </div>

    <div>
        <a href="<?= BASE_URL ?>/quiz" class="btn-secondary-outline" style="font-size: 0.8rem; padding: 0.4rem 0.85rem;">
            <i data-lucide="arrow-left" style="width: 14px; height: 14px;"></i>
            <span>Katalog Kuis</span>
        </a>
    </div>
</div>

<style>
    .quiz-review-layout {
        display: grid;
        grid-template-columns: 1.7fr 1fr;
        gap: 1.5rem;
        align-items: start;
    }

    @media (max-width: 900px) {
        .quiz-review-layout {
            grid-template-columns: 1fr;
        }
    }

    .review-palette-btn {
        width: 38px;
        height: 38px;
        border-radius: 6px;
        font-weight: 700;
        font-size: 0.825rem;
        cursor: pointer;
        transition: all 0.15s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 1px solid transparent;
    }

    .review-palette-btn.current {
        outline: 2px solid #18181B;
        outline-offset: 2px;
    }

    .review-palette-btn.is-correct {
        background-color: #16A34A;
        border-color: #16A34A;
        color: #FFFFFF;
    }

    .review-palette-btn.is-wrong {
        background-color: #DC2626;
        border-color: #DC2626;
        color: #FFFFFF;
    }
</style>

<!-- MAIN 2-COLUMN REVIEW LAYOUT -->
<div class="quiz-review-layout">
    <!-- LEFT COLUMN: Question Carousel & Explanation -->
    <div>
        <!-- Filter Tabs Switcher -->
        <div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap; margin-bottom: 1rem; padding: 0.6rem 0.85rem; background-color: #FFFFFF; border: 1px solid #E5E7EB; border-radius: 8px;">
            <span style="font-size: 0.75rem; font-weight: 700; color: #71717A; text-transform: uppercase; margin-right: 0.25rem;">Filter Soal:</span>
            <button type="button" class="review-filter-btn quiz-segment-tab active" data-filter="all" style="font-size: 0.775rem; padding: 0.3rem 0.65rem; border: none; cursor: pointer;">
                Semua Soal (<?= $totalQuestions ?>)
            </button>
            <button type="button" class="review-filter-btn quiz-segment-tab" data-filter="correct" style="font-size: 0.775rem; padding: 0.3rem 0.65rem; border: none; cursor: pointer;">
                Benar (<?= $correctCount ?>)
            </button>
            <button type="button" class="review-filter-btn quiz-segment-tab" data-filter="wrong" style="font-size: 0.775rem; padding: 0.3rem 0.65rem; border: none; cursor: pointer;">
                Salah (<?= $wrongCount ?>)
            </button>
        </div>

        <!-- Review Question Card -->
        <div class="supabase-panel-card" style="padding: 1.75rem;">
            <span class="corner-crosshair corner-tl">+</span>
            <span class="corner-crosshair corner-tr">+</span>
            <span class="corner-crosshair corner-bl">+</span>
            <span class="corner-crosshair corner-br">+</span>

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
                    <div class="review-question-block" data-index="<?= $index ?>" data-correct="<?= $isCorrect ? '1' : '0' ?>" style="<?= $index === 0 ? 'display: block;' : 'display: none;' ?>">
                        <!-- Header Status -->
                        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.25rem; padding-bottom: 0.75rem; border-bottom: 1px solid #E5E7EB;">
                            <span style="font-family: var(--font-heading); font-size: 0.85rem; font-weight: 800; color: #18181B; text-transform: uppercase; letter-spacing: 0.05em;">
                                Pertanyaan <?= $qNum ?> dari <?= $totalQuestions ?>
                            </span>
                            <?php if ($isCorrect): ?>
                                <span class="status-badge status-active" style="font-size: 0.725rem; padding: 3px 8px;">
                                    <i data-lucide="check" style="width: 11px; height: 11px; margin-right: 3px;"></i>
                                    Benar
                                </span>
                            <?php else: ?>
                                <span class="status-badge" style="background-color: #FEF2F2; color: #DC2626; border: 1px solid #FCA5A5; font-size: 0.725rem; padding: 3px 8px;">
                                    <i data-lucide="x" style="width: 11px; height: 11px; margin-right: 3px;"></i>
                                    Salah
                                </span>
                            <?php endif; ?>
                        </div>

                        <!-- Question Text -->
                        <div style="font-size: 1.05rem; font-weight: 700; color: #18181B; margin-bottom: 1.5rem; line-height: 1.55;">
                            <?= nl2br(htmlspecialchars($q['question'])) ?>
                        </div>

                        <!-- Question Image Attachment -->
                        <?php if (!empty($q['image_path'])): ?>
                            <div style="margin-bottom: 1.5rem; border: 1px solid #E5E7EB; border-radius: 8px; overflow: hidden; max-height: 300px; display: flex; align-items: center; justify-content: center; background-color: #FAFAFA;">
                                <img src="<?= BASE_URL ?>/<?= htmlspecialchars($q['image_path']) ?>" alt="Lampiran Gambar Soal" style="max-width: 100%; max-height: 300px; object-fit: contain;">
                            </div>
                        <?php endif; ?>

                        <!-- 4 Evaluated Options -->
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
                                $tagLabel = '';

                                if ($isThisCorrect) {
                                    $bgColor = '#F0FDF4';
                                    $borderColor = '#86EFAC';
                                    $badgeBg = '#16A34A';
                                    $badgeColor = '#FFFFFF';
                                    $badgeBorder = '#16A34A';
                                    if ($isThisUserChoice) {
                                        $tagLabel = '<span class="status-badge" style="background-color: #DCFCE7; color: #166534; font-size: 0.7rem; font-weight: 700; display: inline-flex; align-items: center; gap: 3px; flex-shrink: 0;"><i data-lucide="check" style="width: 10px; height: 10px;"></i> Jawaban Anda (Benar)</span>';
                                    } else {
                                        $tagLabel = '<span class="status-badge" style="background-color: #DCFCE7; color: #166534; font-size: 0.7rem; font-weight: 700; display: inline-flex; align-items: center; gap: 3px; flex-shrink: 0;"><i data-lucide="check" style="width: 10px; height: 10px;"></i> Kunci Jawaban</span>';
                                    }
                                } elseif ($isThisUserChoice && !$isCorrect) {
                                    $bgColor = '#FEF2F2';
                                    $borderColor = '#FCA5A5';
                                    $badgeBg = '#DC2626';
                                    $badgeColor = '#FFFFFF';
                                    $badgeBorder = '#DC2626';
                                    $tagLabel = '<span class="status-badge" style="background-color: #FEE2E2; color: #991B1B; font-size: 0.7rem; font-weight: 700; display: inline-flex; align-items: center; gap: 3px; flex-shrink: 0;"><i data-lucide="x" style="width: 10px; height: 10px;"></i> Jawaban Anda (Salah)</span>';
                                }
                                ?>
                                <div style="display: flex; align-items: center; justify-content: space-between; gap: 1rem; padding: 0.85rem 1.15rem; background-color: <?= $bgColor ?>; border: 1px solid <?= $borderColor ?>; border-radius: 8px;">
                                    <div style="display: flex; align-items: center; gap: 0.85rem; flex: 1;">
                                        <div class="font-mono" style="display: flex; align-items: center; justify-content: center; width: 28px; height: 28px; border-radius: 6px; border: 1px solid <?= $badgeBorder ?>; background-color: <?= $badgeBg ?>; color: <?= $badgeColor ?>; font-weight: 800; font-size: 0.825rem; flex-shrink: 0;">
                                            <?= $optKey ?>
                                        </div>
                                        <span style="font-size: 0.9rem; font-weight: 500; color: #18181B; line-height: 1.45;">
                                            <?= htmlspecialchars($optText) ?>
                                        </span>
                                    </div>
                                    <?php if (!empty($tagLabel)): ?>
                                        <?= $tagLabel ?>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- Technical RouterOS Explanation Box -->
                        <div style="padding: 1.15rem 1.25rem; background-color: #FAFAFA; border: 1px solid #E5E7EB; border-radius: 8px;">
                            <div style="display: flex; align-items: center; gap: 0.45rem; margin-bottom: 0.4rem;">
                                <i data-lucide="book-open" style="width: 15px; height: 15px; color: #18181B;"></i>
                                <span style="font-size: 0.8rem; font-weight: 800; color: #18181B; text-transform: uppercase; letter-spacing: 0.05em;">
                                    Pembahasan Teknis MikroTik RouterOS
                                </span>
                            </div>
                            <p style="font-size: 0.85rem; color: #52525B; margin: 0; line-height: 1.55;">
                                <?= !empty($q['explanation']) ? nl2br(htmlspecialchars($q['explanation'])) : 'Tidak ada catatan penjelasan teknis tambahan untuk butir soal ini.' ?>
                            </p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- CAROUSEL CONTROLS -->
            <div style="display: flex; align-items: center; justify-content: space-between; padding-top: 1.25rem; margin-top: 1.5rem; border-top: 1px solid #E5E7EB;">
                <button type="button" id="btn-prev-review" class="btn-secondary-outline" style="font-size: 0.825rem; padding: 0.45rem 0.95rem;">
                    <i data-lucide="arrow-left" style="width: 14px; height: 14px;"></i>
                    <span>Sebelumnya</span>
                </button>
                <button type="button" id="btn-next-review" class="btn-primary-black" style="font-size: 0.825rem; padding: 0.45rem 1rem;">
                    <span>Selanjutnya</span>
                    <i data-lucide="arrow-right" style="width: 14px; height: 14px;"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- RIGHT COLUMN: Evaluation Summary & Palette -->
    <div style="display: flex; flex-direction: column; gap: 1.25rem;">
        <!-- Card 1: Score & Statistics -->
        <div class="supabase-panel-card" style="padding: 1.25rem;">
            <span class="corner-crosshair corner-tl">+</span>
            <span class="corner-crosshair corner-tr">+</span>
            <span class="corner-crosshair corner-bl">+</span>
            <span class="corner-crosshair corner-br">+</span>

            <div style="margin-bottom: 0.75rem;">
                <span style="font-size: 0.75rem; font-weight: 700; color: #71717A; text-transform: uppercase; letter-spacing: 0.05em;">Ringkasan Hasil</span>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem; margin-bottom: 1rem;">
                <div style="padding: 0.75rem; background-color: #F0FDF4; border: 1px solid #86EFAC; border-radius: 6px; text-align: center;">
                    <span style="font-size: 1.4rem; font-weight: 800; color: #16A34A; font-family: var(--font-mono); display: block; line-height: 1;"><?= $correctCount ?></span>
                    <span style="font-size: 0.675rem; font-weight: 700; color: #15803D; text-transform: uppercase;">Benar</span>
                </div>
                <div style="padding: 0.75rem; background-color: #FEF2F2; border: 1px solid #FCA5A5; border-radius: 6px; text-align: center;">
                    <span style="font-size: 1.4rem; font-weight: 800; color: #DC2626; font-family: var(--font-mono); display: block; line-height: 1;"><?= $wrongCount ?></span>
                    <span style="font-size: 0.675rem; font-weight: 700; color: #B91C1C; text-transform: uppercase;">Salah</span>
                </div>
            </div>

            <div style="display: flex; align-items: center; justify-content: space-between; font-size: 0.8rem; padding: 0.5rem 0.75rem; background-color: #FAFAFA; border: 1px solid #E5E7EB; border-radius: 6px;">
                <span style="color: #71717A;">Total Akurasi:</span>
                <span class="font-mono" style="font-weight: 800; color: #18181B;"><?= (int)$score ?>%</span>
            </div>
        </div>

        <!-- Card 2: Question Review Palette Grid -->
        <div class="supabase-panel-card" style="padding: 1.25rem;">
            <span class="corner-crosshair corner-tl">+</span>
            <span class="corner-crosshair corner-tr">+</span>
            <span class="corner-crosshair corner-bl">+</span>
            <span class="corner-crosshair corner-br">+</span>

            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem; padding-bottom: 0.5rem; border-bottom: 1px solid #E5E7EB;">
                <h4 style="font-family: var(--font-heading); font-size: 0.85rem; font-weight: 800; color: #18181B; margin: 0; text-transform: uppercase; letter-spacing: 0.05em;">
                    Palet Soal Evaluasi
                </h4>
            </div>

            <!-- Palette Grid -->
            <div id="review-palette-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(38px, 1fr)); gap: 0.5rem; margin-bottom: 1.25rem;">
                <?php foreach ($questions as $index => $q): ?>
                    <?php
                    $userAns = strtoupper((string)($userAnswers[$index] ?? ''));
                    $correctAns = strtoupper((string)($q['correct'] ?? ''));
                    $isCorrect = ($userAns !== '' && $userAns === $correctAns);
                    ?>
                    <button type="button" class="review-palette-btn font-mono <?= $index === 0 ? 'current' : '' ?> <?= $isCorrect ? 'is-correct' : 'is-wrong' ?>" data-index="<?= $index ?>" data-correct="<?= $isCorrect ? '1' : '0' ?>">
                        <?= $index + 1 ?>
                    </button>
                <?php endforeach; ?>
            </div>

            <!-- Palette Legend -->
            <div style="display: flex; align-items: center; justify-content: space-between; font-size: 0.725rem; color: #71717A; padding-top: 0.75rem; border-top: 1px solid #E5E7EB;">
                <span style="display: inline-flex; align-items: center; gap: 0.35rem;">
                    <span style="width: 10px; height: 10px; border-radius: 2px; background-color: #16A34A; display: inline-block;"></span>
                    <span>Benar</span>
                </span>
                <span style="display: inline-flex; align-items: center; gap: 0.35rem;">
                    <span style="width: 10px; height: 10px; border-radius: 2px; background-color: #DC2626; display: inline-block;"></span>
                    <span>Salah</span>
                </span>
                <span style="display: inline-flex; align-items: center; gap: 0.35rem;">
                    <span style="width: 10px; height: 10px; border-radius: 2px; outline: 1.5px solid #18181B; display: inline-block;"></span>
                    <span>Aktif</span>
                </span>
            </div>
        </div>
    </div>
</div>

<!-- Reviewer JS Module -->
<script src="<?= BASE_URL ?>/js/quiz-review.js?v=<?= time() ?>"></script>

<?php require_once dirname(__DIR__) . '/templates/footer.php'; ?>