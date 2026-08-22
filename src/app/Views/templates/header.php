<?php
$isAdmin = isset($_SESSION['user']['email']) && (strcasecmp(trim($_SESSION['user']['email']), 'admin@routerosquiz.academy') === 0);
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
        /* Sizing & Canvas Shell for Student View */
        .student-top-nav {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: var(--top-nav-height, 56px);
            background-color: rgba(255, 255, 255, 0.92);
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
            gap: 1rem;
        }

        .student-brand-group {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .student-nav-links {
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }

        .student-nav-link {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            padding: 0.45rem 0.85rem;
            font-size: 0.875rem;
            font-weight: 600;
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
            gap: 0.65rem;
        }

        .student-user-pill {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.3rem 0.65rem 0.3rem 0.35rem;
            background-color: #F4F4F5;
            border: 1px solid #E5E7EB;
            border-radius: 9999px;
            font-size: 0.8rem;
            font-weight: 600;
            color: #18181B;
            text-decoration: none;
            transition: all 0.15s ease;
        }

        .student-user-pill:hover {
            border-color: #D4D4D8;
            background-color: #E4E4E7;
        }

        .student-avatar-box {
            width: 22px;
            height: 22px;
            border-radius: 50%;
            background-color: #18181B;
            color: #FFFFFF;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.7rem;
            font-weight: 700;
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

        /* Mobile Hamburger & Drawer */
        .student-mobile-toggle {
            display: none;
            background: transparent;
            border: 1px solid #E5E7EB;
            border-radius: 6px;
            padding: 0.4rem;
            color: #18181B;
            cursor: pointer;
        }

        .student-mobile-menu {
            display: none;
            position: fixed;
            top: var(--top-nav-height, 56px);
            left: 0;
            right: 0;
            background: #FFFFFF;
            border-bottom: 1px solid #E5E7EB;
            padding: 1rem 1.5rem;
            flex-direction: column;
            gap: 0.5rem;
            z-index: 999;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05);
        }

        .student-mobile-menu.active {
            display: flex;
        }

        @media (max-width: 768px) {
            .student-nav-links {
                display: none;
            }
            .student-mobile-toggle {
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

    <!-- 1. TOP NAVBAR GEIST -->
    <header class="student-top-nav" aria-label="Navigasi Utama">
        <div class="student-nav-container">
            <!-- Brand Logo -->
            <div class="student-brand-group">
                <a href="<?= BASE_URL ?>/" class="nav-brand-title" aria-label="NetQuiz Beranda">
                    <div class="nav-brand-mark">
                        <i data-lucide="terminal" class="nav-brand-icon"></i>
                        <span class="live-dot" title="Server Status: Online"></span>
                    </div>
                    <span class="nav-brand-text">Net<span class="nav-brand-accent">Quiz</span><span class="brand-cursor">_</span></span>
                </a>
            </div>

            <!-- Main Menu Links -->
            <nav class="student-nav-links" aria-label="Menu Utama Siswa">
                <a href="<?= BASE_URL ?>/" class="student-nav-link <?= isStudentNavActive('/', $currentPath) ? 'active' : '' ?>">
                    <i data-lucide="layout-dashboard" style="width: 15px; height: 15px;"></i>
                    <span>Dashboard</span>
                </a>
                <a href="<?= BASE_URL ?>/quiz" class="student-nav-link <?= isStudentNavActive('/quiz', $currentPath) ? 'active' : '' ?>">
                    <i data-lucide="file-question" style="width: 15px; height: 15px;"></i>
                    <span>Katalog Kuis</span>
                </a>
                <a href="<?= BASE_URL ?>/learn" class="student-nav-link <?= isStudentNavActive('/learn', $currentPath) ? 'active' : '' ?>">
                    <i data-lucide="book-open" style="width: 15px; height: 15px;"></i>
                    <span>Materi Belajar</span>
                </a>
                <a href="<?= BASE_URL ?>/leaderboard" class="student-nav-link <?= isStudentNavActive('/leaderboard', $currentPath) ? 'active' : '' ?>">
                    <i data-lucide="trophy" style="width: 15px; height: 15px;"></i>
                    <span>Leaderboard</span>
                </a>
            </nav>

            <!-- User Controls / Auth -->
            <div class="student-nav-controls">
                <?php if (isset($_SESSION['user'])): ?>
                    <?php if ($isAdmin): ?>
                        <a href="<?= BASE_URL ?>/admin" class="btn-geist-nav-secondary" title="Buka Panel Admin">
                            <i data-lucide="shield" style="width: 14px; height: 14px;"></i>
                            <span class="nav-text-desktop">Admin Panel</span>
                        </a>
                    <?php endif; ?>

                    <a href="<?= BASE_URL ?>/settings" class="student-user-pill" title="Pengaturan Akun">
                        <div class="student-avatar-box">
                            <?= strtoupper(substr(htmlspecialchars($_SESSION['user']['name'] ?? 'U'), 0, 1)) ?>
                        </div>
                        <span class="nav-text-desktop"><?= htmlspecialchars($_SESSION['user']['name'] ?? 'Siswa') ?></span>
                    </a>

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

                <!-- Mobile Hamburger Button -->
                <button type="button" class="student-mobile-toggle" id="btn-student-mobile-menu" aria-label="Toggle Navigasi" onclick="toggleStudentMobileMenu()">
                    <i data-lucide="menu" style="width: 18px; height: 18px;"></i>
                </button>
            </div>
        </div>

        <!-- Mobile Drawer Navigation -->
        <div id="student-mobile-drawer" class="student-mobile-menu">
            <a href="<?= BASE_URL ?>/" class="student-nav-link <?= isStudentNavActive('/', $currentPath) ? 'active' : '' ?>">
                <i data-lucide="layout-dashboard" style="width: 16px; height: 16px;"></i>
                <span>Dashboard</span>
            </a>
            <a href="<?= BASE_URL ?>/quiz" class="student-nav-link <?= isStudentNavActive('/quiz', $currentPath) ? 'active' : '' ?>">
                <i data-lucide="file-question" style="width: 16px; height: 16px;"></i>
                <span>Katalog Kuis</span>
            </a>
            <a href="<?= BASE_URL ?>/learn" class="student-nav-link <?= isStudentNavActive('/learn', $currentPath) ? 'active' : '' ?>">
                <i data-lucide="book-open" style="width: 16px; height: 16px;"></i>
                <span>Materi Belajar</span>
            </a>
            <a href="<?= BASE_URL ?>/leaderboard" class="student-nav-link <?= isStudentNavActive('/leaderboard', $currentPath) ? 'active' : '' ?>">
                <i data-lucide="trophy" style="width: 16px; height: 16px;"></i>
                <span>Leaderboard</span>
            </a>
            <?php if (isset($_SESSION['user'])): ?>
                <a href="<?= BASE_URL ?>/settings" class="student-nav-link <?= isStudentNavActive('/settings', $currentPath) ? 'active' : '' ?>">
                    <i data-lucide="user" style="width: 16px; height: 16px;"></i>
                    <span>Pengaturan Akun</span>
                </a>
            <?php endif; ?>
        </div>
    </header>

    <script>
        function toggleStudentMobileMenu() {
            const drawer = document.getElementById('student-mobile-drawer');
            if (drawer) {
                drawer.classList.toggle('active');
            }
        }
    </script>

    <!-- 2. MAIN APPLICATION CONTENT SHELL -->
    <main class="student-main-content">
        <div class="student-shell-container">
