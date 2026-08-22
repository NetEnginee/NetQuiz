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

    $html = '<nav class="admin-breadcrumb-nav" aria-label="Breadcrumb">';
    $html .= '<ol class="admin-breadcrumb-list">';

    $total = count($items);
    foreach ($items as $i => $item) {
        $isLast = ($i === $total - 1);
        $label = htmlspecialchars($item['label'] ?? '');
        $url = $item['url'] ?? null;

        if ($isLast || empty($url)) {
            $html .= '<li class="breadcrumb-item active" aria-current="page">';
            $html .= '<span class="breadcrumb-current-text">' . $label . '</span>';
            $html .= '</li>';
        } else {
            $html .= '<li class="breadcrumb-item">';
            $html .= '<a href="' . htmlspecialchars($url) . '" class="breadcrumb-link">' . $label . '</a>';
            $html .= '</li>';
            $html .= '<li class="breadcrumb-separator" aria-hidden="true">/</li>';
        }
    }

    $html .= '</ol>';
    $html .= '</nav>';

    return $html;
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? APP_NAME) ?> | NetQuiz</title>
    <meta name="description"
        content="Platform simulasi ujian, evaluasi kompetensi, dan materi pembelajaran MikroTik RouterOS berbasis standar sertifikasi internasional.">
    <meta name="robots" content="index, follow">
    <link rel="canonical"
        href="<?= (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] ?>">

    <!-- Fonts: Plus Jakarta Sans, Inter, & JetBrains Mono -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;600&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">

    <!-- Global Geist Design System Core Styles -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/dashboard.css?v=<?= time() ?>">

    <!-- Lucide Icons CDN -->
    <script src="https://unpkg.com/lucide@latest" defer></script>

    <style>
        html,
        body {
            background-color: #FAFAFA !important;
            background: #FAFAFA !important;
            color: #18181B;
        }

        /* Minimalist Geist Top Navbar */
        .student-top-nav {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: var(--top-nav-height, 56px);
            background-color: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--color-border, #E5E7EB);
            z-index: 1000;
            display: flex;
            align-items: center;
        }

        .student-nav-container {
            max-width: 1200px;
            width: 100%;
            margin: 0 auto;
            padding: 0 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .student-brand-group {
            display: flex;
            align-items: center;
        }

        .student-nav-links {
            display: flex;
            align-items: center;
            gap: 0.35rem;
        }

        .student-nav-link {
            padding: 0.4rem 0.85rem;
            font-size: 0.875rem;
            font-weight: 500;
            color: #52525B;
            text-decoration: none;
            border-radius: 6px;
            border: 1px solid transparent;
            transition: none !important;
            transform: none !important;
        }

        .student-nav-link:hover {
            color: #000000;
            background-color: var(--color-bg, #FAFAFA);
            border-color: #000000;
            transform: none !important;
        }

        .student-nav-link:active {
            background-color: #F4F4F5;
            color: #000000;
            border-color: #000000;
            transform: none !important;
        }

        .student-nav-link.active {
            color: #000000;
            background-color: #F4F4F5;
            border-color: #E4E4E7;
            font-weight: 700;
        }

        .student-nav-controls {
            display: flex;
            align-items: center;
            gap: 0.65rem;
        }

        .student-avatar-box {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background-color: #18181B;
            color: #FFFFFF;
            font-family: var(--font-heading);
            font-size: 0.825rem;
            font-weight: 800;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid #E5E7EB;
            flex-shrink: 0;
        }

        .student-main-content {
            flex: 1;
            padding-top: calc(var(--top-nav-height, 56px) + 2rem);
            padding-bottom: 5rem;
            position: relative;
            z-index: 1;
        }

        .student-shell-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1.5rem;
            width: 100%;
        }

        /* Dynamic Breadcrumb Styling */
        .admin-breadcrumb-nav {
            margin-bottom: 0.65rem;
        }

        .admin-breadcrumb-list {
            display: inline-flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 0.45rem;
            list-style: none;
            padding: 0;
            margin: 0;
            font-family: var(--font-heading);
            font-size: 0.8rem;
            color: #71717A;
        }

        .breadcrumb-item {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            line-height: 1.4;
        }

        .breadcrumb-link {
            color: #71717A;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.15s ease;
        }

        .breadcrumb-link:hover {
            color: #18181B;
        }

        .breadcrumb-separator {
            color: #D4D4D8;
            font-size: 0.75rem;
            user-select: none;
        }

        .breadcrumb-item.active {
            color: #18181B;
            font-weight: 600;
        }

        .breadcrumb-current-text {
            max-width: 280px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            display: inline-block;
            vertical-align: bottom;
        }

        /* Fixed Footer Styling */
        .student-fixed-footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            height: 40px;
            background-color: rgba(255, 255, 255, 0.92);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-top: 1px solid var(--color-border, #E5E7EB);
            z-index: 900;
            display: flex;
            align-items: center;
        }

        /* Floating Bottom Navigation for Mobile & Tablet */
        .student-floating-bottom-nav {
            display: none;
            position: fixed;
            bottom: max(16px, env(safe-area-inset-bottom, 16px));
            left: 50%;
            transform: translateX(-50%);
            z-index: 1000;
            width: auto;
            max-width: calc(100vw - 24px);
            pointer-events: none;
        }

        @media (max-width: 1024px) {
            .student-nav-links {
                display: none;
            }

            .student-floating-bottom-nav {
                display: flex;
            }

            .student-fixed-footer {
                display: none;
            }

            .student-shell-container {
                padding: 0 1rem;
            }

            .student-main-content {
                padding-top: calc(var(--top-nav-height, 56px) + 1.25rem);
                padding-bottom: 6.5rem;
            }
        }
    </style>
</head>

<body class="<?= $isQuizPlay ? 'quiz-play-mode' : '' ?>">
    <!-- CAD Blueprint Canvas Ornaments -->
    <div class="bg-ornament-grid" aria-hidden="true"></div>
    <div class="bg-ornament-major-grid" aria-hidden="true"></div>
    <div class="bg-ornament-ambient" aria-hidden="true"></div>
    <div class="viewport-framing-line left-line" aria-hidden="true"></div>
    <div class="viewport-framing-line right-line" aria-hidden="true"></div>

    <!-- 1. MINIMALIST TOP NAVBAR -->
    <header class="student-top-nav" aria-label="Navigasi Utama">
        <div class="student-nav-container">
            <!-- Left: Clean Brand Mark -->
            <div class="student-brand-group">
                <a href="<?= BASE_URL ?>/" class="nav-brand-title" aria-label="NetQuiz Beranda">
                    <div class="nav-brand-mark">
                        <i data-lucide="terminal" class="nav-brand-icon"></i>
                        <span class="live-dot" title="Server Status: Online"></span>
                    </div>
                    <span class="nav-brand-text">Net<span class="nav-brand-accent">Quiz</span><span class="brand-cursor">_</span></span>
                </a>
            </div>

            <!-- Center: Pure Text Navigation Links (Desktop) -->
            <nav class="student-nav-links" aria-label="Menu Siswa">
                <a href="<?= BASE_URL ?>/" class="student-nav-link <?= isStudentNavActive('/', $currentPath) ? 'active' : '' ?>">
                    Dashboard
                </a>
                <a href="<?= BASE_URL ?>/quiz" class="student-nav-link <?= isStudentNavActive('/quiz', $currentPath) ? 'active' : '' ?>">
                    Kuis
                </a>
                <a href="<?= BASE_URL ?>/learn" class="student-nav-link <?= isStudentNavActive('/learn', $currentPath) ? 'active' : '' ?>">
                    Materi
                </a>
                <a href="<?= BASE_URL ?>/leaderboard" class="student-nav-link <?= isStudentNavActive('/leaderboard', $currentPath) ? 'active' : '' ?>">
                    Leaderboard
                </a>
            </nav>

            <!-- Right: Admin Panel, Avatar Badge, & Keluar Button -->
            <div class="student-nav-controls">
                <?php if (isset($_SESSION['user'])): ?>
                    <?php if ($isAdmin): ?>
                        <a href="<?= BASE_URL ?>/admin" class="btn-geist-nav-secondary nav-text-desktop" title="Admin Panel">
                            <i data-lucide="shield" style="width: 14px; height: 14px;"></i>
                            <span>Admin Panel</span>
                        </a>
                    <?php endif; ?>

                    <div class="student-avatar-box" title="<?= htmlspecialchars($userName) ?>">
                        <?= $userInitial ?>
                    </div>

                    <div class="nav-context-divider" aria-hidden="true"></div>

                    <a href="<?= BASE_URL ?>/logout" class="btn-geist-nav-danger" title="Keluar dari akun">
                        <i data-lucide="log-out" style="width: 14px; height: 14px;"></i>
                        <span class="nav-text-desktop">Keluar</span>
                    </a>
                <?php else: ?>
                    <a href="<?= BASE_URL ?>/login" class="btn-primary-black" style="font-size: 0.825rem; padding: 0.4rem 0.85rem;">
                        <i data-lucide="log-in" style="width: 14px; height: 14px;"></i>
                        <span>Masuk</span>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <!-- 2. FLOATING BOTTOM NAVIGATION DOCK (MOBILE & TABLET EXCLUSIVE) -->
    <nav class="student-floating-bottom-nav" aria-label="Navigasi Bawah">
        <div class="floating-nav-container">
            <!-- 1. Dashboard -->
            <a href="<?= BASE_URL ?>/" class="floating-bottom-btn <?= isStudentNavActive('/', $currentPath) ? 'active' : '' ?>" title="Dashboard">
                <span class="floating-btn-icon-wrapper">
                    <i data-lucide="layout-dashboard" class="floating-btn-icon"></i>
                </span>
            </a>

            <!-- 2. Kuis -->
            <a href="<?= BASE_URL ?>/quiz" class="floating-bottom-btn <?= isStudentNavActive('/quiz', $currentPath) ? 'active' : '' ?>" title="Katalog Kuis">
                <span class="floating-btn-icon-wrapper">
                    <i data-lucide="file-question" class="floating-btn-icon"></i>
                </span>
            </a>

            <!-- 3. Materi Belajar -->
            <a href="<?= BASE_URL ?>/learn" class="floating-bottom-btn <?= isStudentNavActive('/learn', $currentPath) ? 'active' : '' ?>" title="Materi Belajar">
                <span class="floating-btn-icon-wrapper">
                    <i data-lucide="book-open" class="floating-btn-icon"></i>
                </span>
            </a>

            <!-- 4. Leaderboard -->
            <a href="<?= BASE_URL ?>/leaderboard" class="floating-bottom-btn <?= isStudentNavActive('/leaderboard', $currentPath) ? 'active' : '' ?>" title="Leaderboard">
                <span class="floating-btn-icon-wrapper">
                    <i data-lucide="trophy" class="floating-btn-icon"></i>
                </span>
            </a>

            <?php if ($isAdmin): ?>
                <!-- 5. Admin Panel -->
                <a href="<?= BASE_URL ?>/admin" class="floating-bottom-btn" title="Admin Panel">
                    <span class="floating-btn-icon-wrapper">
                        <i data-lucide="shield" class="floating-btn-icon"></i>
                    </span>
                </a>
            <?php endif; ?>
        </div>
    </nav>

    <!-- 3. MAIN APPLICATION CONTENT SHELL -->
    <main class="student-main-content">
        <div class="student-shell-container">