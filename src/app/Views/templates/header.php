<?php
$isAdmin = isset($_SESSION['user']['email']) && (strcasecmp(trim($_SESSION['user']['email']), 'admin@routerosquiz.academy') === 0);
$userName = $_SESSION['user']['name'] ?? 'Siswa';
$userEmail = $_SESSION['user']['email'] ?? '';
$userInitial = strtoupper(substr(htmlspecialchars($userName), 0, 1));
$currentUri = $_SERVER['REQUEST_URI'] ?? '/';
$currentPath = parse_url($currentUri, PHP_URL_PATH) ?? '/';
$isQuizPlay = (bool)preg_match('#^/quiz/[0-9]+/play#', $currentPath) || str_contains($currentPath, '/play');

// Active tab helper
function isStudentNavActive(string $path, string $currentPath): bool
{
    if ($path === '/' && ($currentPath === '/' || $currentPath === '/dashboard')) {
        return true;
    }
    if ($path !== '/' && str_starts_with($currentPath, $path)) {
        return true;
    }
    return false;
}

/**
 * Dynamic Breadcrumb Generator Helper
 * @param array<array{label: string, url?: string}> $items
 */
function renderBreadcrumb(array $items): string
{
    if (empty($items)) return '';

    $html = '<nav class="hero-breadcrumb" aria-label="Breadcrumb">';
    $total = count($items);
    foreach ($items as $i => $item) {
        $isLast = ($i === $total - 1);
        $label = htmlspecialchars($item['label'] ?? '');
        $url = $item['url'] ?? null;

        if ($isLast) {
            $html .= '<span class="active-tag">' . $label . '</span>';
        } else {
            if (!empty($url)) {
                $html .= '<a href="' . htmlspecialchars($url) . '" class="breadcrumb-link">' . $label . '</a>';
            } else {
                $html .= '<span class="breadcrumb-link">' . $label . '</span>';
            }
            $html .= '<span class="breadcrumb-sep">/</span>';
        }
    }
    $html .= '</nav>';

    return $html;
}
?>
<!DOCTYPE html>
<html lang="id" class="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? APP_NAME) ?> | NetQuiz</title>
    <meta name="description"
        content="Platform simulasi ujian, evaluasi kompetensi, dan materi pembelajaran MikroTik RouterOS berbasis standar sertifikasi internasional.">
    <meta name="robots" content="index, follow">
    <link rel="canonical"
        href="<?= (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] ?>">

    <!-- Fonts: Inter (UI), JetBrains Mono (Code/Metadata), Press Start 2P (Retro Accents) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=JetBrains+Mono:ital,wght@0,400;0,500;0,600;0,700;1,400&family=Press+Start+2P&display=swap" rel="stylesheet">

    <!-- Global Vercel Dark & Pixel Dashboard Stylesheet -->
    <link rel="stylesheet" href="<?= function_exists('assetUrl') ? assetUrl('/css/dashboard.css') : (BASE_URL . '/css/dashboard.css') ?>">
    <?php if (str_contains($_SERVER['REQUEST_URI'] ?? '', '/quiz')): ?>
        <link rel="stylesheet" href="<?= function_exists('assetUrl') ? assetUrl('/css/quiz.css') : (BASE_URL . '/css/quiz.css') ?>">
    <?php elseif (str_contains($_SERVER['REQUEST_URI'] ?? '', '/learn')): ?>
        <link rel="stylesheet" href="<?= function_exists('assetUrl') ? assetUrl('/css/learn.css') : (BASE_URL . '/css/learn.css') ?>">
    <?php elseif (str_contains($_SERVER['REQUEST_URI'] ?? '', '/leaderboard')): ?>
        <link rel="stylesheet" href="<?= function_exists('assetUrl') ? assetUrl('/css/leaderboard.css') : (BASE_URL . '/css/leaderboard.css') ?>">
    <?php endif; ?>

    <!-- Lucide Icons CDN -->
    <script src="https://unpkg.com/lucide@latest" defer></script>

    <!-- Top Slim Loading Engine -->
    <script src="<?= function_exists('assetUrl') ? assetUrl('/js/page-loader.js') : (BASE_URL . '/js/page-loader.js') ?>"></script>

    <!-- Global App State -->
    <script>
        window.BASE_URL = "<?= BASE_URL ?>";
        window.CSRF_TOKEN = "<?= \App\Core\Security::generateCsrfToken() ?>";
    </script>
</head>

<body class="bg-vercel-bg text-zinc-100 font-sans antialiased min-h-screen relative overflow-x-hidden selection:bg-vercel-accent selection:text-white <?= $isQuizPlay ? 'quiz-play-mode' : '' ?>">

    <!-- Network Data Animation Canvas Background -->
    <canvas id="networkCanvas" class="network-canvas pointer-events-none fixed inset-0 z-0 opacity-40" aria-hidden="true"></canvas>

    <!-- Inline SVG Pixel Art Sprite Sheet -->
    <svg class="hidden" style="display: none;" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
        <!-- 16-bit Gold Coin -->
        <g id="pixel-coin">
            <path fill="#F5A623" d="M3,1 h6 v1 h2 v2 h1 v6 h-1 v2 h-2 v1 h-6 v-1 h-2 v-2 h-1 v-6 h1 v-2 h2 z" />
            <path fill="#FFE17D" d="M4,2 h4 v1 h-4 z M3,3 h2 v2 h-2 z M3,5 h1 v3 h-1 z" />
            <path fill="#D48806" d="M4,9 h5 v1 h-5 z M9,3 h1 v6 h-1 z M7,10 h2 v1 h-2 z" />
            <path fill="#FFF" d="M5,4 h2 v4 h-2 z" />
        </g>

        <!-- 16-bit MikroTik Router -->
        <g id="pixel-router">
            <path fill="#0070F3" d="M2,8 h12 v6 h-12 z" />
            <path fill="#50E3C2" d="M4,2 h1 v6 h-1 z M11,2 h1 v6 h-1 z" />
            <path fill="#7928CA" d="M3,9 h2 v2 h-2 z M7,9 h2 v2 h-2 z M11,9 h2 v2 h-2 z" />
            <path fill="#00FF66" d="M3,12 h1 v1 h-1 z M5,12 h1 v1 h-1 z M7,12 h1 v1 h-1 z M9,12 h1 v1 h-1 z" />
            <path fill="#111" d="M1,14 h14 v1 h-14 z" />
        </g>

        <!-- 16-bit Computer Monitor -->
        <g id="pixel-computer">
            <path fill="#333" d="M1,1 h14 v10 h-14 z M6,11 h4 v3 h-4 z M4,14 h8 v1 h-8 z" />
            <path fill="#000" d="M2,2 h12 v8 h-12 z" />
            <path fill="#50E3C2" d="M4,4 h4 v1 h-4 z M4,6 h6 v1 h-6 z M4,8 h2 v1 h-2 z" />
            <path fill="#00FF66" d="M11,8 h1 v1 h-1 z" />
        </g>

        <!-- 16-bit AI Robot -->
        <g id="pixel-robot">
            <path fill="#7928CA" d="M4,2 h8 v2 h-8 z M3,4 h10 v8 h-10 z M7,1 h2 v1 h-2 z" />
            <path fill="#000" d="M5,6 h2 v3 h-2 z M9,6 h2 v3 h-2 z M6,10 h4 v1 h-4 z" />
            <path fill="#FF0080" d="M5,7 h2 v1 h-2 z M9,7 h2 v1 h-2 z" />
            <path fill="#50E3C2" d="M1,6 h2 v3 h-2 z M13,6 h2 v3 h-2 z" />
            <path fill="#333" d="M5,12 h6 v3 h-6 z" />
        </g>

        <!-- 16-bit Book -->
        <g id="pixel-book">
            <path fill="#FF0080" d="M2,2 h11 v12 h-11 z" />
            <path fill="#FFF" d="M4,3 h8 v10 h-8 z" />
            <path fill="#333" d="M2,2 h2 v12 h-2 z M12,2 h1 v12 h-1 z" />
            <path fill="#0070F3" d="M6,5 h4 v1 h-4 z M6,7 h5 v1 h-5 z M6,9 h3 v1 h-3 z" />
            <path fill="#F5A623" d="M8,1 h2 v4 h-2 z" />
        </g>

        <!-- 16-bit Sparkle / Star -->
        <g id="pixel-sparkle">
            <path fill="#50E3C2" d="M7,1 h2 v14 h-2 z M1,7 h14 v2 h-14 z M4,4 h2 v2 h-2 z M10,10 h2 v2 h-2 z M10,4 h2 v2 h-2 z M4,10 h2 v2 h-2 z" />
            <path fill="#FFF" d="M7,7 h2 v2 h-2 z" />
        </g>
    </svg>

    <!-- Top Navigation Bar (Vercel Dark & Pixel Masterpiece) -->
    <header class="student-top-nav" aria-label="Navigasi Utama">
        <div class="student-nav-container">
            <!-- Left: Brand Group -->
            <div class="student-brand-group">
                <a href="<?= BASE_URL ?>/" class="nav-brand-title" aria-label="NetQuiz Beranda">
                    <div class="brand-badge-box">
                        <svg class="pixel-brand-svg pixelated" viewBox="0 0 16 16">
                            <use href="#pixel-router"></use>
                        </svg>

                    </div>
                    <div class="brand-text-wrap">
                        <span class="nav-brand-text">Net<span class="nav-brand-accent">Quiz</span><span class="brand-cursor font-mono">_</span></span>
                    </div>
                </a>
            </div>

            <!-- Center: Navigation Links (Desktop) -->
            <nav class="student-nav-links" aria-label="Menu Navigasi">
                <a href="<?= BASE_URL ?>/" class="student-nav-link <?= isStudentNavActive('/', $currentPath) ? 'active' : '' ?>">
                    <span class="pixel-nav-dot" aria-hidden="true">■</span>
                    <span>Dashboard</span>
                </a>
                <a href="<?= BASE_URL ?>/quiz" class="student-nav-link <?= isStudentNavActive('/quiz', $currentPath) ? 'active' : '' ?>">
                    <span class="pixel-nav-dot" aria-hidden="true">■</span>
                    <span>Kuis</span>
                </a>
                <a href="<?= BASE_URL ?>/learn" class="student-nav-link <?= isStudentNavActive('/learn', $currentPath) ? 'active' : '' ?>">
                    <span class="pixel-nav-dot" aria-hidden="true">■</span>
                    <span>Materi</span>
                </a>
                <a href="<?= BASE_URL ?>/leaderboard" class="student-nav-link <?= isStudentNavActive('/leaderboard', $currentPath) ? 'active' : '' ?>">
                    <span class="pixel-nav-dot" aria-hidden="true">■</span>
                    <span>Leaderboard</span>
                </a>
            </nav>

            <!-- Right: Controls & User Profile -->
            <div class="student-nav-controls">
                <!-- Audio Synth Toggle -->
                <button type="button" id="soundToggleBtn" class="sound-toggle-btn font-mono" aria-label="Aktifkan/Matikan Suara">
                    <span id="soundIcon">🔊</span>
                    <span id="soundLabel">Sound: ON</span>
                </button>

                <!-- Admin Link if Admin -->
                <?php if ($isAdmin): ?>
                    <a href="<?= BASE_URL ?>/admin" class="admin-quick-link font-mono" title="Akses Admin Panel">
                        <i data-lucide="shield" class="w-3.5 h-3.5"></i>
                        <span>Admin</span>
                    </a>
                <?php endif; ?>

                <!-- User Keycap Avatar Box -->
                <span class="student-avatar-box font-mono" title="Hallo!! <?= htmlspecialchars($userName) ?>" aria-label="Profil Siswa">
                    <span><?= $userInitial ?></span>
                </span>

                <!-- Logout Button -->
                <a href="<?= BASE_URL ?>/logout" class="nav-logout-btn font-mono" title="Keluar dari Akun" aria-label="Logout">
                    <i data-lucide="log-out" class="w-4 h-4"></i>
                </a>
            </div>
        </div>
    </header>

    <!-- Floating Mobile Bottom Navigation Bar -->
    <nav class="student-floating-bottom-nav font-mono" aria-label="Navigasi Mobile">
        <div class="mobile-nav-pill-container">
            <a href="<?= BASE_URL ?>/" class="mobile-nav-item <?= isStudentNavActive('/', $currentPath) ? 'active' : '' ?>">
                <i data-lucide="layout-dashboard"></i>
                <span>Home</span>
            </a>
            <a href="<?= BASE_URL ?>/quiz" class="mobile-nav-item <?= isStudentNavActive('/quiz', $currentPath) ? 'active' : '' ?>">
                <i data-lucide="terminal"></i>
                <span>Kuis</span>
            </a>
            <a href="<?= BASE_URL ?>/learn" class="mobile-nav-item <?= isStudentNavActive('/learn', $currentPath) ? 'active' : '' ?>">
                <i data-lucide="book-open"></i>
                <span>Materi</span>
            </a>
            <a href="<?= BASE_URL ?>/leaderboard" class="mobile-nav-item <?= isStudentNavActive('/leaderboard', $currentPath) ? 'active' : '' ?>">
                <i data-lucide="trophy"></i>
                <span>Rank</span>
            </a>
            <a href="<?= BASE_URL ?>/settings" class="mobile-nav-item <?= isStudentNavActive('/settings', $currentPath) ? 'active' : '' ?>">
                <i data-lucide="user"></i>
                <span>Profil</span>
            </a>
        </div>
    </nav>

    <!-- Main Shell Container -->
    <main class="student-main-content">
        <div class="student-shell-container">