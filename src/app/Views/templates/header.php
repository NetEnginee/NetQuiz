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
    <link rel="stylesheet" href="<?= function_exists('assetUrl') ? assetUrl('/css/dashboard.css') : (BASE_URL . '/css/dashboard.css') ?>">

    <!-- Lucide Icons CDN -->
    <script src="https://unpkg.com/lucide@latest" defer></script>

    <!-- Top Slim Loading Engine -->
    <script src="<?= function_exists('assetUrl') ? assetUrl('/js/page-loader.js') : (BASE_URL . '/js/page-loader.js') ?>"></script>

    <style>
        html,
        body {
            background-color: #FAFAFA !important;
            background: #FAFAFA !important;
            color: #18181B;
        }

        /* 3D Isometric Voxel & Geist Top Navbar */
        .student-top-nav {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: var(--top-nav-height, 56px);
            background-color: rgba(255, 255, 255, 0.94);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
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

        .pixel-brand-mark {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            position: relative;
            padding: 2px 4px;
            background-color: #FFFFFF;
            border: 1.5px solid #18181B;
            border-radius: 8px;
            box-shadow: 0 2.5px 0 #18181B, 0 4px 8px rgba(0, 0, 0, 0.08);
            perspective: 400px;
            user-select: none;
            transition: transform 0.15s ease, box-shadow 0.15s ease;
        }

        .nav-brand-title:hover .pixel-brand-mark {
            transform: translateY(-1px) rotateX(4deg) rotateY(-6deg);
            box-shadow: 0 3.5px 0 #18181B, 0 6px 12px rgba(0, 0, 0, 0.12);
        }

        .pixel-netty-svg {
            display: block;
            width: 26px;
            height: 26px;
        }

        .nav-brand-title:hover .netty-3d-eye {
            fill: #22C55E;
        }

        .pixel-live-dot {
            position: absolute;
            top: 2px;
            right: 2px;
            width: 6px;
            height: 6px;
            background-color: #22C55E;
            box-shadow: 0 0 5px #22C55E;
            border-radius: 2px;
            border: 1px solid #18181B;
        }

        .student-nav-links {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .student-nav-link {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            padding: 0.35rem 0.85rem;
            font-size: 0.85rem;
            font-weight: 600;
            color: #52525B;
            text-decoration: none;
            border-radius: 6px;
            border: 1.5px solid transparent;
            box-shadow: none;
            transition: none !important;
            transform: none !important;
        }

        .pixel-nav-dot {
            font-size: 7px;
            line-height: 1;
            color: transparent;
            display: inline-block;
            vertical-align: middle;
            user-select: none;
        }

        .student-nav-link:hover {
            color: #000000;
            background-color: #FAFAFA;
            border-color: #18181B;
            box-shadow: 0 2px 0 #18181B;
            transform: none !important;
        }

        .student-nav-link:hover .pixel-nav-dot {
            color: #71717A;
        }

        .student-nav-link:active {
            background-color: #F4F4F5;
            color: #000000;
            border-color: #18181B;
            box-shadow: none;
            transform: translateY(2px) !important;
        }

        .student-nav-link.active {
            color: #FFFFFF;
            background-color: #18181B;
            border-color: #000000;
            box-shadow: 0 2.5px 0 #000000;
            font-weight: 700;
        }

        .student-nav-link.active .pixel-nav-dot {
            color: #22C55E;
        }

        .student-nav-controls {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        /* 3D Voxel Keycap Avatar Box */
        .student-avatar-box {
            width: 33px;
            height: 33px;
            background: linear-gradient(180deg, #27272A 0%, #18181B 100%);
            color: #FFFFFF;
            font-size: 0.85rem;
            font-weight: 800;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 6px;
            border: 1.5px solid #000000;
            box-shadow: 0 2.5px 0 #000000, 0 4px 6px rgba(0, 0, 0, 0.12);
            flex-shrink: 0;
            user-select: none;
        }

        /* 3D Tactile Buttons */
        .btn-geist-nav-secondary {
            border-radius: 6px !important;
            border: 1.5px solid #18181B !important;
            box-shadow: 0 2.5px 0 #18181B !important;
        }

        .btn-geist-nav-secondary:active {
            box-shadow: none !important;
            transform: translateY(2.5px) !important;
        }

        .btn-geist-nav-danger {
            border-radius: 6px !important;
            border: 1.5px solid #18181B !important;
            box-shadow: 0 2.5px 0 #18181B !important;
        }

        .btn-geist-nav-danger:active {
            box-shadow: none !important;
            transform: translateY(2.5px) !important;
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

        /* Breadcrumb Navigation */
        .breadcrumb-list {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.825rem;
            font-family: var(--font-mono, monospace);
            list-style: none;
            padding: 0;
            margin: 0;
            color: #71717A;
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
            <!-- Left: Clean Brand Mark with Cute Pixel Router Bot ("Netty") -->
            <div class="student-brand-group">
                <a href="<?= BASE_URL ?>/" class="nav-brand-title" aria-label="NetQuiz Beranda">
                    <div class="pixel-brand-mark">
                        <svg class="pixel-netty-svg voxel-netty-3d" viewBox="0 0 28 28" width="26" height="26" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <!-- 3D Isometric Drop Shadow -->
                            <ellipse cx="14" cy="24.5" rx="9" ry="3" fill="rgba(0,0,0,0.14)" />

                            <!-- 3D Left Antenna -->
                            <path d="M7 8V2.5" stroke="#18181B" stroke-width="1.8" stroke-linecap="round" />
                            <circle cx="7" cy="2" r="1.8" fill="#22C55E" stroke="#18181B" stroke-width="0.8" />

                            <!-- 3D Right Antenna -->
                            <path d="M21 8V2.5" stroke="#18181B" stroke-width="1.8" stroke-linecap="round" />
                            <circle cx="21" cy="2" r="1.8" fill="#22C55E" stroke="#18181B" stroke-width="0.8" />

                            <!-- 3D Chassis: Top Plane (Isometric Highlight Face) -->
                            <polygon points="14,4.5 24.5,9.8 14,15 3.5,9.8" fill="#3F3F46" stroke="#18181B" stroke-width="1" stroke-linejoin="round" />
                            <!-- Top Cooling Vents -->
                            <line x1="11" y1="8" x2="17" y2="11" stroke="#27272A" stroke-width="1" stroke-linecap="round" />
                            <line x1="8.5" y1="9.2" x2="14.5" y2="12.2" stroke="#27272A" stroke-width="1" stroke-linecap="round" />

                            <!-- 3D Chassis: Front-Left Screen Face -->
                            <polygon points="3.5,9.8 14,15 14,22.8 3.5,17.6" fill="#09090B" stroke="#18181B" stroke-width="1" stroke-linejoin="round" />

                            <!-- Cute 3D Pixel Glowing Eyes -->
                            <polygon class="netty-3d-eye" points="6.2,13 8.2,14 8.2,16 6.2,15" fill="#38BDF8" />
                            <polygon class="netty-3d-eye" points="10,14.8 12,15.8 12,17.8 10,16.8" fill="#38BDF8" />

                            <!-- 3D Chassis: Front-Right Extrusion Face -->
                            <polygon points="14,15 24.5,9.8 24.5,17.6 14,22.8" fill="#18181B" stroke="#18181B" stroke-width="1" stroke-linejoin="round" />

                            <!-- 3D Side Ethernet Port Insets -->
                            <polygon points="16,16.2 22.5,13 22.5,15.2 16,18.4" fill="#09090B" />
                            <!-- 3D LEDs -->
                            <polygon points="17,14 18.5,13.2 18.5,14.4 17,15.2" fill="#22C55E" />
                            <polygon points="19.5,12.8 21,12 21,13.2 19.5,14" fill="#38BDF8" />
                        </svg>
                        <span class="pixel-live-dot" title="Server Status: Online"></span>
                    </div>
                    <span class="nav-brand-text">Net<span class="nav-brand-accent">Quiz</span><span class="brand-cursor font-mono">_</span></span>
                </a>
            </div>

            <!-- Center: Pure Text Navigation Links with Cute Pixel Dot Indicators (Desktop) -->
            <nav class="student-nav-links" aria-label="Menu Siswa">
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

            <!-- Right: Admin Panel, Avatar Badge, & Keluar Button -->
            <div class="student-nav-controls">
                <?php if (isset($_SESSION['user'])): ?>
                    <?php if ($isAdmin): ?>
                        <a href="<?= BASE_URL ?>/admin" class="btn-geist-nav-secondary nav-text-desktop" title="Admin Panel">
                            <i data-lucide="shield" style="width: 14px; height: 14px;"></i>
                            <span>Admin Panel</span>
                        </a>
                    <?php endif; ?>

                    <div class="student-avatar-box font-mono" title="<?= htmlspecialchars($userName) ?>">
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