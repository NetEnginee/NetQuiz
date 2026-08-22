<?php
$isAdmin = isset($_SESSION['user']['email']) && (strcasecmp(trim($_SESSION['user']['email']), 'admin@routerosquiz.academy') === 0);
$userName = $_SESSION['user']['name'] ?? 'Siswa';
$userEmail = $_SESSION['user']['email'] ?? '';
$userInitial = strtoupper(substr(htmlspecialchars($userName), 0, 1));
$currentUri = $_SERVER['REQUEST_URI'] ?? '/';
$currentPath = parse_url($currentUri, PHP_URL_PATH) ?? '/';

// Active tab helper
function isStudentNavActive(string $path, string $currentPath): bool {
    if ($path === '/' && ($currentPath === '/' || $currentPath === '/dashboard')) {
        return true;
    }
    if ($path !== '/' && str_starts_with($currentPath, $path)) {
        return true;
    }
    return false;
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
            transition: all 0.15s ease;
        }

        .student-nav-link:hover {
            color: #18181B;
            background-color: #F4F4F5;
        }

        .student-nav-link.active {
            color: #18181B;
            background-color: #F4F4F5;
            font-weight: 700;
        }

        .student-nav-controls {
            display: flex;
            align-items: center;
            position: relative;
        }

        /* Minimalist Circular Avatar Trigger */
        .nav-avatar-btn {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background-color: #18181B;
            color: #FFFFFF;
            font-family: var(--font-heading);
            font-size: 0.85rem;
            font-weight: 800;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid #E5E7EB;
            cursor: pointer;
            transition: all 0.15s ease;
            outline: none;
            padding: 0;
        }

        .nav-avatar-btn:hover,
        .nav-avatar-btn:focus-visible {
            border-color: #18181B;
            transform: scale(1.03);
        }

        /* Geist Avatar Popover Dropdown */
        .avatar-popover-menu {
            display: none;
            position: absolute;
            top: calc(100% + 8px);
            right: 0;
            width: 230px;
            background: #FFFFFF;
            border: 1px solid #E5E7EB;
            border-radius: 8px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.08), 0 8px 10px -6px rgba(0, 0, 0, 0.04);
            padding: 0.4rem;
            z-index: 1100;
            flex-direction: column;
            animation: fadeIn 0.12s ease-out;
        }

        .avatar-popover-menu.open {
            display: flex;
        }

        .popover-header {
            padding: 0.6rem 0.75rem;
            display: flex;
            flex-direction: column;
            gap: 0.15rem;
        }

        .popover-user-name {
            font-size: 0.875rem;
            font-weight: 700;
            color: #18181B;
            font-family: var(--font-heading);
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .popover-user-email {
            font-size: 0.75rem;
            color: #71717A;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .popover-divider {
            height: 1px;
            background-color: #E5E7EB;
            margin: 0.3rem 0;
        }

        .popover-item {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            padding: 0.5rem 0.75rem;
            font-size: 0.825rem;
            font-weight: 500;
            color: #18181B;
            text-decoration: none;
            border-radius: 6px;
            transition: background-color 0.15s ease;
        }

        .popover-item:hover {
            background-color: #F4F4F5;
        }

        .popover-item.danger {
            color: #DC2626;
        }

        .popover-item.danger:hover {
            background-color: #FEF2F2;
        }

        .popover-mobile-nav {
            display: none;
            flex-direction: column;
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

        @media (max-width: 768px) {
            .student-nav-links {
                display: none;
            }
            .popover-mobile-nav {
                display: flex;
            }
            .student-shell-container {
                padding: 0 1rem;
            }
            .student-main-content {
                padding-top: calc(var(--top-nav-height, 56px) + 1.25rem);
            }
        }
    </style>
</head>

<body>
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

            <!-- Center: Pure Text Navigation Links -->
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

            <!-- Right: Single Monochrome Avatar Popover -->
            <div class="student-nav-controls">
                <?php if (isset($_SESSION['user'])): ?>
                    <button type="button" class="nav-avatar-btn" id="btn-nav-avatar" aria-label="Menu Akun" aria-expanded="false" onclick="toggleNavAvatarPopover(event)">
                        <?= $userInitial ?>
                    </button>

                    <!-- Avatar Popover Dropdown -->
                    <div class="avatar-popover-menu" id="nav-avatar-popover" role="menu">
                        <!-- User Info Header -->
                        <div class="popover-header">
                            <span class="popover-user-name"><?= htmlspecialchars($userName) ?></span>
                            <span class="popover-user-email"><?= htmlspecialchars($userEmail) ?></span>
                        </div>

                        <!-- Mobile-only Navigation Links -->
                        <div class="popover-mobile-nav">
                            <div class="popover-divider"></div>
                            <a href="<?= BASE_URL ?>/" class="popover-item" role="menuitem">
                                <i data-lucide="layout-dashboard" style="width: 14px; height: 14px;"></i>
                                <span>Dashboard</span>
                            </a>
                            <a href="<?= BASE_URL ?>/quiz" class="popover-item" role="menuitem">
                                <i data-lucide="file-question" style="width: 14px; height: 14px;"></i>
                                <span>Kuis</span>
                            </a>
                            <a href="<?= BASE_URL ?>/learn" class="popover-item" role="menuitem">
                                <i data-lucide="book-open" style="width: 14px; height: 14px;"></i>
                                <span>Materi</span>
                            </a>
                            <a href="<?= BASE_URL ?>/leaderboard" class="popover-item" role="menuitem">
                                <i data-lucide="trophy" style="width: 14px; height: 14px;"></i>
                                <span>Leaderboard</span>
                            </a>
                        </div>

                        <div class="popover-divider"></div>

                        <!-- Menu Actions -->
                        <?php if ($isAdmin): ?>
                            <a href="<?= BASE_URL ?>/admin" class="popover-item" role="menuitem">
                                <i data-lucide="shield" style="width: 14px; height: 14px;"></i>
                                <span>Admin Panel</span>
                            </a>
                        <?php endif; ?>

                        <a href="<?= BASE_URL ?>/settings" class="popover-item" role="menuitem">
                            <i data-lucide="settings" style="width: 14px; height: 14px;"></i>
                            <span>Pengaturan Akun</span>
                        </a>

                        <div class="popover-divider"></div>

                        <a href="<?= BASE_URL ?>/logout" class="popover-item danger" role="menuitem">
                            <i data-lucide="log-out" style="width: 14px; height: 14px;"></i>
                            <span>Keluar</span>
                        </a>
                    </div>
                <?php else: ?>
                    <a href="<?= BASE_URL ?>/login" class="btn-primary-black" style="font-size: 0.8rem; padding: 0.4rem 0.85rem;">
                        Masuk
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <script>
        function toggleNavAvatarPopover(event) {
            event.stopPropagation();
            const popover = document.getElementById('nav-avatar-popover');
            const btn = document.getElementById('btn-nav-avatar');
            if (popover && btn) {
                const isOpen = popover.classList.contains('open');
                popover.classList.toggle('open', !isOpen);
                btn.setAttribute('aria-expanded', !isOpen ? 'true' : 'false');
                if (window.lucide) window.lucide.createIcons();
            }
        }

        // Close popover when clicking anywhere outside
        document.addEventListener('click', function(e) {
            const popover = document.getElementById('nav-avatar-popover');
            const btn = document.getElementById('btn-nav-avatar');
            if (popover && popover.classList.contains('open')) {
                if (!popover.contains(e.target) && (!btn || !btn.contains(e.target))) {
                    popover.classList.remove('open');
                    if (btn) btn.setAttribute('aria-expanded', 'false');
                }
            }
        });
    </script>

    <!-- 2. MAIN APPLICATION CONTENT SHELL -->
    <main class="student-main-content">
        <div class="student-shell-container">
