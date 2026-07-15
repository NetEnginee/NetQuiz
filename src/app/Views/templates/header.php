<?php
$headerUnlockedBadges = [];
$headerLockedAchievements = [];
$headerNextBadge = null;
$allBadges = [];
$isAdmin = isset($_SESSION['user']['email']) && (strcasecmp(trim($_SESSION['user']['email']), 'admin@routerosquiz.academy') === 0);

if (isset($_SESSION['user'])) {
    try {
        $db = \App\Core\Database::getInstance()->getConnection();
        $userId = $_SESSION['user']['id'];

        // 1. Completed quizzes
        $stmt = $db->prepare("SELECT COUNT(*) as count FROM quiz_attempts WHERE user_id = :uid AND status = 'finished'");
        $stmt->execute(['uid' => $userId]);
        $completedQuizzes = (int) ($stmt->fetch(\PDO::FETCH_ASSOC)['count'] ?? 0);

        // 2. Total score
        $stmt = $db->prepare("SELECT SUM(score) as total FROM quiz_attempts WHERE user_id = :uid");
        $stmt->execute(['uid' => $userId]);
        $totalScore = (int) ($stmt->fetch(\PDO::FETCH_ASSOC)['total'] ?? 0);

        // 3. Category count
        $categories = ['Routing' => 0, 'Firewall & NAT' => 0, 'Wireless' => 0, 'Network Management' => 0];
        $stmt = $db->prepare("SELECT category, COUNT(*) as count FROM quiz_attempts WHERE user_id = :uid AND status = 'finished' GROUP BY category");
        $stmt->execute(['uid' => $userId]);
        foreach ($stmt->fetchAll(\PDO::FETCH_ASSOC) as $row) {
            if (isset($categories[$row['category']])) {
                $categories[$row['category']] = (int) $row['count'];
            }
        }

        // 4. Perfect scores
        $stmt = $db->prepare("SELECT COUNT(*) as count FROM quiz_attempts WHERE user_id = :uid AND score = 100 AND status = 'finished'");
        $stmt->execute(['uid' => $userId]);
        $perfectScores = (int) ($stmt->fetch(\PDO::FETCH_ASSOC)['count'] ?? 0);

        // Badges calculation
        $stmtBadges = $db->query("SELECT * FROM badges ORDER BY id ASC");
        $badgesDb = $stmtBadges->fetchAll(\PDO::FETCH_ASSOC);

        $badgesDef = [];
        foreach ($badgesDb as $b) {
            $progress = 0;
            switch ($b['metric']) {
                case 'completed_quizzes':
                    $progress = $completedQuizzes;
                    break;
                case 'total_score':
                    $progress = $totalScore;
                    break;
                case 'perfect_scores':
                    $progress = $perfectScores;
                    break;
                case 'category_routing':
                    $progress = $categories['Routing'] ?? 0;
                    break;
                case 'category_firewall':
                    $progress = $categories['Firewall & NAT'] ?? 0;
                    break;
                case 'category_wireless':
                    $progress = $categories['Wireless'] ?? 0;
                    break;
                case 'category_network':
                    $progress = $categories['Network Management'] ?? 0;
                    break;
            }
            $badgesDef[] = [
                'id' => $b['id'],
                'title' => $b['title'],
                'description' => $b['description'],
                'icon' => $b['icon'],
                'progress' => $progress,
                'max' => (int) $b['target_value']
            ];
        }

        foreach ($badgesDef as $b) {
            $progVal = min($b['progress'], $b['max']);
            $isUnlocked = $progVal >= $b['max'];
            $badgeData = [
                'id' => $b['id'],
                'title' => $b['title'],
                'description' => $b['description'],
                'icon' => $b['icon'],
                'progress' => $progVal,
                'max' => $b['max'],
                'unlocked' => $isUnlocked,
                'percent' => round(($progVal / $b['max']) * 100)
            ];
            $allBadges[] = $badgeData;
            if ($isUnlocked) {
                $headerUnlockedBadges[] = $badgeData;
            } else {
                $headerLockedAchievements[] = $badgeData;
            }
        }

        if (!empty($headerLockedAchievements)) {
            $tempLocked = $headerLockedAchievements;
            usort($tempLocked, function ($a, $b) {
                return $b['percent'] <=> $a['percent'];
            });
            $headerNextBadge = $tempLocked[0];
        }
    } catch (\Exception $e) {
        // Silent fallback
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? APP_NAME) ?> | NetQuiz</title>
    <meta name="description"
        content="Kuasai konfigurasi MikroTik RouterOS melalui kuis interaktif, simulasi ujian sertifikasi, dan papan peringkat (leaderboard) secara real-time di NetQuiz.">
    <meta name="robots" content="index, follow">
    <link rel="canonical"
        href="<?= (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] ?>">

    <?php if (isset($preloadImage) && !empty($preloadImage)): ?>
        <!-- Preload LCP Image -->
        <link rel="preload" href="<?= htmlspecialchars($preloadImage) ?>" as="image">
    <?php endif; ?>

    <!-- Modern Typography (Plus Jakarta Sans & Inter) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap"
        rel="stylesheet">

    <!-- Lucide Icons CDN -->
    <script src="https://unpkg.com/lucide@latest" defer></script>

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
            /* Same off-white background as auth pages */
            color: #475569;
            /* Slate-600 body text */
            margin: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        header {
            border-bottom: 1px solid #e2e8f0;
            /* Slate-200 border */
            padding: 0.75rem 2rem;
            background-color: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(8px);
            /* White navbar background with modern glassmorphism blur */
            box-shadow: 0 1px 3px 0 rgba(15, 23, 42, 0.03);
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 100;
        }

        .container {
            max-width: 1000px;
            margin: 0 auto;
            width: 100%;
        }

        main {
            flex: 1;
            padding: 6.5rem 2rem 6rem 2rem;
            display: flex;
            flex-direction: column;
            align-items: stretch;
        }

        .card {
            background-color: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            padding: 2.5rem;
            box-shadow: 0 4px 6px -1px rgba(15, 23, 42, 0.03), 0 2px 4px -2px rgba(15, 23, 42, 0.03);
            margin-bottom: 2rem;
        }

        h1,
        h2,
        h3,
        h4,
        strong {
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: #0f172a;
            /* Slate-900 titles */
            margin-top: 0;
        }

        p {
            color: #475569;
            font-size: 1rem;
            line-height: 1.6;
        }

        footer {
            border-top: 1px solid #e2e8f0;
            padding: 1.25rem 2rem;
            text-align: center;
            color: #64748b;
            font-size: 0.85rem;
            background-color: #ffffff;
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            z-index: 50;
            box-shadow: 0 -2px 10px rgba(15, 23, 42, 0.02);
        }

        .hidden {
            display: none !important;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-8px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .profile-btn-trigger {
            display: flex;
            align-items: center;
            gap: 0.65rem;
            background-color: #ffffff;
            border: 1px solid #e2e8f0;
            padding: 0.35rem 0.85rem 0.35rem 0.35rem;
            cursor: pointer;
            border-radius: 9999px;
            box-shadow: 0 1px 3px 0 rgba(15, 23, 42, 0.05);
            transition: border-color 0.2s ease;
            outline: none;
        }

        .profile-btn-trigger:hover {
            border-color: #7c3aed;
        }

        /* Modern Minimalist Mobile Hamburger Styles */
        .mobile-toggle {
            display: none;
            background-color: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 50%;
            cursor: pointer;
            width: 2.25rem;
            height: 2.25rem;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
            outline: none;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }

        .mobile-toggle:hover {
            border-color: #7c3aed;
            color: #7c3aed;
        }

        #mobile-menu {
            display: none;
            flex-direction: column;
            position: absolute;
            top: 120%;
            right: 2rem;
            width: 180px;
            background-color: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.08), 0 8px 10px -6px rgba(15, 23, 42, 0.04);
            padding: 0.5rem;
            z-index: 100;
            animation: slideDown 0.15s ease-out;
        }

        #mobile-menu.active {
            display: flex;
        }

        @media (max-width: 1024px) {
            header .container nav {
                display: none !important;
            }

            .desktop-only {
                display: none !important;
            }

            .mobile-toggle {
                display: flex !important;
            }
        }
    </style>
    <style>
        /* Modal Styles */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background-color: rgba(15, 23, 42, 0.85);
            z-index: 1000;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.2s ease;
        }

        .modal-overlay.active {
            opacity: 1;
            pointer-events: auto;
        }

        .modal-content {
            background-color: #ffffff;
            border-radius: 16px;
            padding: 2rem;
            max-width: 800px;
            width: 90%;
            max-height: 85vh;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            transform: scale(0.95);
            transition: transform 0.2s ease;
            position: relative;
        }

        @media (max-width: 640px) {
            .modal-content {
                width: calc(100% - 2.5rem);
                padding: 1.5rem 1rem;
            }
        }

        .modal-content::-webkit-scrollbar {
            display: none;
        }

        .modal-body-scroll::-webkit-scrollbar {
            display: none;
        }

        .modal-body-scroll {
            -ms-overflow-style: none;
            /* IE and Edge */
            scrollbar-width: none;
            /* Firefox */
        }

        .modal-overlay.active .modal-content {
            transform: scale(1);
        }

        .modal-close-btn {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: none;
            border: none;
            cursor: pointer;
            color: #64748b;
            padding: 0.5rem;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background-color 0.2s;
        }

        .modal-close-btn:hover {
            background-color: #f1f5f9;
            color: #0f172a;
        }

        .badges-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1.25rem;
            margin-top: 1.5rem;
        }

        @media (min-width: 640px) {
            .badges-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        .badge-modal-card {
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 1.25rem;
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
            transition: border-color 0.2s;
        }

        .badge-modal-card.unlocked {
            border-color: #c7d2fe;
            background-color: #faf5ff;
        }

        /* Simple & Elegant Page Loader CSS */
        .page-loader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background-color: rgba(255, 255, 255, 0.95);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 999999;
            transition: opacity 0.3s ease, visibility 0.3s ease;
            opacity: 1;
            visibility: visible;
        }

        .page-loader.fade-out {
            opacity: 0;
            visibility: hidden;
        }

        .loader-spinner {
            width: 44px;
            height: 44px;
            border: 3px solid #e2e8f0;
            border-top-color: #7c3aed;
            border-radius: 50%;
            animation: spinLoader 0.7s linear infinite;
        }

        @keyframes spinLoader {
            to {
                transform: rotate(360deg);
            }
        }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Elegant loader transition scripts
            const loader = document.getElementById('page-loader');
            if (loader) {
                setTimeout(() => {
                    loader.classList.add('fade-out');
                }, 80);
            }

            // Ensure loader fades out on pageshow (e.g., when navigating using browser Back/Forward BFCache)
            window.addEventListener('pageshow', (event) => {
                if (loader) {
                    loader.classList.add('fade-out');
                }
            });



            // 1. Auto-pause quiz when navigating away via internal links
            document.addEventListener('click', (e) => {
                const target = e.target.closest('a');
                if (target) {
                    const href = target.getAttribute('href');
                    const currentPath = window.location.href.toLowerCase();

                    if (currentPath.includes('/quiz/play/') && href && !href.startsWith('#') && !href.startsWith('javascript:') && window.isQuizSubmitting === false) {
                        const quizForm = document.getElementById('quiz-form');
                        if (quizForm) {
                            e.preventDefault();
                            window.isQuizSubmitting = true;

                            // Transform action URL to pause route and attach redirect destination
                            const actionUrl = quizForm.getAttribute('action').replace('/submit/', '/pause/');
                            quizForm.action = actionUrl + '?redirect=' + encodeURIComponent(target.href);

                            if (loader) {
                                loader.style.setProperty('display', 'flex', 'important');
                                loader.classList.remove('fade-out');
                            }

                            quizForm.submit();
                        }
                    }
                }
            });

            // 2. Auto-pause quiz when leaving the page (tab close, refresh, browser back/forward)
            window.addEventListener('pagehide', () => {
                const currentPath = window.location.href.toLowerCase();
                if (currentPath.includes('/quiz/play/') && window.isQuizSubmitting === false) {
                    const quizForm = document.getElementById('quiz-form');
                    if (quizForm) {
                        const formData = new FormData(quizForm);
                        const actionUrl = quizForm.getAttribute('action').replace('/submit/', '/pause/');
                        navigator.sendBeacon(actionUrl, formData);
                    }
                }
            });

            // 3. Page Loader Click Listener (deferred to run last, respects e.defaultPrevented)
            setTimeout(() => {
                document.addEventListener('click', (e) => {
                    if (e.defaultPrevented) {
                        return;
                    }
                    const target = e.target.closest('a');
                    if (target) {
                        const href = target.getAttribute('href');
                        const targetAttr = target.getAttribute('target');
                        if (href &&
                            !href.startsWith('#') &&
                            !href.startsWith('javascript:') &&
                            (!targetAttr || targetAttr === '_self') &&
                            !e.ctrlKey && !e.metaKey && !e.shiftKey) {
                            try {
                                const linkUrl = new URL(target.href);
                                if (linkUrl.origin === window.location.origin) {
                                    if (loader) {
                                        loader.classList.remove('fade-out');
                                    }
                                }
                            } catch (err) { }
                        }
                    }
                });
            }, 100);

            document.addEventListener('submit', (e) => {
                if (loader && !e.defaultPrevented) {
                    loader.classList.remove('fade-out');
                }
            });

            const trigger = document.getElementById('profile-dropdown-trigger');
            const menu = document.getElementById('profile-dropdown-menu');
            const chevron = document.getElementById('profile-chevron');
            const achTrigger = document.getElementById('ach-dropdown-trigger');
            const modal = document.getElementById('ach-modal');
            const closeModalBtn = document.getElementById('close-ach-modal-btn');

            if (trigger && menu) {
                trigger.addEventListener('click', (e) => {
                    e.stopPropagation();
                    const isHidden = menu.classList.contains('hidden');
                    if (isHidden) {
                        menu.classList.remove('hidden');
                        menu.style.display = 'flex';
                        if (chevron) {
                            chevron.style.transform = 'rotate(180deg)';
                        }
                    } else {
                        menu.classList.add('hidden');
                        menu.style.display = 'none';
                        if (chevron) {
                            chevron.style.transform = 'rotate(0deg)';
                        }
                    }
                });

                // Close dropdown when clicking outside
                document.addEventListener('click', (e) => {
                    if (!trigger.contains(e.target) && !menu.contains(e.target)) {
                        menu.classList.add('hidden');
                        menu.style.display = 'none';
                        if (chevron) {
                            chevron.style.transform = 'rotate(0deg)';
                        }
                    }
                });
            }

            if (achTrigger && modal) {
                achTrigger.addEventListener('click', (e) => {
                    e.stopPropagation();
                    modal.classList.add('active');
                    if (menu) {
                        menu.classList.add('hidden');
                        menu.style.display = 'none';
                        if (chevron) {
                            chevron.style.transform = 'rotate(0deg)';
                        }
                    }
                });
            }

            if (closeModalBtn && modal) {
                closeModalBtn.addEventListener('click', () => {
                    modal.classList.remove('active');
                });
            }

            if (modal) {
                modal.addEventListener('click', (e) => {
                    if (e.target === modal) {
                        modal.classList.remove('active');
                    }
                });
            }


            // Mobile menu toggle (UX Optimized: Auto-close on click outside)
            const mobileTrigger = document.getElementById('mobile-menu-trigger');
            const mobileMenu = document.getElementById('mobile-menu');
            if (mobileTrigger && mobileMenu) {
                const menuSvg = `<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#475569" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="4" x2="20" y1="12" y2="12"/><line x1="4" x2="20" y1="6" y2="6"/><line x1="4" x2="20" y1="18" y2="18"/></svg>`;
                const closeSvg = `<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#475569" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" x2="6" y1="6" y2="18"/><line x1="6" x2="18" y1="6" y2="18"/></svg>`;

                mobileTrigger.addEventListener('click', (e) => {
                    e.stopPropagation();
                    mobileMenu.classList.toggle('active');
                    if (mobileMenu.classList.contains('active')) {
                        mobileTrigger.innerHTML = closeSvg;
                    } else {
                        mobileTrigger.innerHTML = menuSvg;
                    }
                });

                document.addEventListener('click', (e) => {
                    if (!mobileTrigger.contains(e.target) && !mobileMenu.contains(e.target)) {
                        mobileMenu.classList.remove('active');
                        mobileTrigger.innerHTML = menuSvg;
                    }
                });
            }

            // Initialize Lucide Icons globally
            if (window.lucide) {
                window.lucide.createIcons();
            }

            // Load custom profile avatar from localStorage if set
            const savedAvatar = localStorage.getItem('user_avatar');
            if (savedAvatar) {
                const headerAvatarImg = document.querySelector('.header-avatar-img');
                const headerAvatarInitials = document.querySelector('.header-avatar-initials');
                if (headerAvatarImg && headerAvatarInitials) {
                    headerAvatarImg.src = savedAvatar;
                    headerAvatarImg.classList.remove('hidden');
                    headerAvatarInitials.classList.add('hidden');
                }
            }


        });
    </script>
</head>

<body>
    <!-- Elegant Page Loader -->
    <div id="page-loader" class="page-loader">
        <div class="loader-spinner"></div>
    </div>

    <header style="position: fixed; top: 0; left: 0; right: 0; z-index: 100;">
        <div class="container"
            style="display: flex; justify-content: space-between; align-items: center; position: relative;">
            <!-- Brand Logo (Left) -->
            <div style="flex: 1; display: flex; justify-content: flex-start;">
                <a href="<?= $isAdmin ? BASE_URL . '/admin' : BASE_URL . '/' ?>"
                    style="text-decoration: none; font-size: 1.25rem; font-weight: 800; font-family: 'Plus Jakarta Sans', sans-serif; color: #0f172a; letter-spacing: -0.02em; display: inline-flex; align-items: center; gap: 0.5rem;">
                    Net<span style="color: #7c3aed;">Quiz</span>
                </a>
            </div>

            <!-- Navigation Menu (Center) -->
            <nav style="display: flex; gap: 1.5rem; align-items: center; justify-content: center; flex: 1;">
                <?php if (strpos($_SERVER['REQUEST_URI'], '/admin') === false): ?>
                    <a href="<?= BASE_URL ?>/learn"
                        style="color: #475569; text-decoration: none; font-weight: 500; transition: color 0.2s;"
                        onmouseover="this.style.color='#7c3aed'" onmouseout="this.style.color='#475569'">Materi</a>
                    <a href="<?= BASE_URL ?>/quiz"
                        style="color: #475569; text-decoration: none; font-weight: 500; transition: color 0.2s;"
                        onmouseover="this.style.color='#7c3aed'" onmouseout="this.style.color='#475569'">Quiz</a>
                    <a href="<?= BASE_URL ?>/leaderboard"
                        style="color: #475569; text-decoration: none; font-weight: 500; transition: color 0.2s;"
                        onmouseover="this.style.color='#7c3aed'" onmouseout="this.style.color='#475569'">Leaderboard</a>
                    <?php if ($isAdmin): ?>
                        <a href="<?= BASE_URL ?>/admin"
                            style="color: #7c3aed; text-decoration: none; font-weight: 700; transition: color 0.2s;"
                            onmouseover="this.style.color='#6d28d9'" onmouseout="this.style.color='#7c3aed'">Admin Panel</a>
                    <?php endif; ?>
                <?php endif; ?>
            </nav>

            <!-- User Controls (Right) -->
            <div style="display: flex; gap: 1rem; align-items: center; justify-content: flex-end; flex: 1;">
                <?php if (isset($_SESSION['user'])): ?>
                    <?php if (!$isAdmin): ?>
                        <!-- Achievements Trigger (Direct Modal Access) -->
                        <button type="button" id="ach-dropdown-trigger"
                            style="display: flex; align-items: center; justify-content: center; width: 2.25rem; height: 2.25rem; background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 50%; cursor: pointer; position: relative; transition: all 0.2s; outline: none; box-shadow: 0 1px 3px rgba(0,0,0,0.05);"
                            onmouseover="this.style.borderColor='#7c3aed'" onmouseout="this.style.borderColor='#e2e8f0'">
                            <i data-lucide="award" style="width: 1.1rem; height: 1.1rem; color: #475569;"></i>
                            <?php if (count($headerUnlockedBadges) > 0): ?>
                                <span
                                    style="position: absolute; top: -3px; right: -3px; background-color: #7c3aed; color: #ffffff; font-size: 0.65rem; font-weight: 700; width: 15px; height: 15px; border-radius: 50%; display: flex; align-items: center; justify-content: center; border: 2px solid #ffffff;">
                                    <?= count($headerUnlockedBadges) ?>
                                </span>
                            <?php endif; ?>
                        </button>

                        <!-- Hamburger Trigger Button -->
                        <button type="button" id="mobile-menu-trigger" class="mobile-toggle">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                                stroke="#475569" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="4" x2="20" y1="12" y2="12" />
                                <line x1="4" x2="20" y1="6" y2="6" />
                                <line x1="4" x2="20" y1="18" y2="18" />
                            </svg>
                        </button>
                    <?php endif; ?>

                    <!-- Profile Dropdown Container -->
                    <?php if ($isAdmin): ?>
                        <a href="<?= BASE_URL ?>/logout"
                            style="color: #f43f5e; border: 1px solid #fecaca; background-color: #fff1f2; text-decoration: none; font-weight: 700; padding: 0.5rem 1rem; border-radius: 10px; font-size: 0.85rem; display: flex; align-items: center; gap: 0.4rem; transition: all 0.2s;"
                            onmouseover="this.style.backgroundColor='#f43f5e'; this.style.color='#ffffff'; this.style.borderColor='#f43f5e';"
                            onmouseout="this.style.backgroundColor='#fff1f2'; this.style.color='#f43f5e'; this.style.borderColor='#fecaca';">
                            <i data-lucide="log-out" style="width: 0.95rem; height: 0.95rem;"></i>
                            Keluar
                        </a>
                    <?php else: ?>
                        <div class="profile-dropdown-wrapper desktop-only" style="position: relative; display: inline-block;">
                            <!-- Dropdown Trigger Button -->
                            <button type="button" id="profile-dropdown-trigger" class="profile-btn-trigger">
                                <!-- Avatar Circle (Initials / Image) -->
                                <div class="header-avatar-circle"
                                    style="width: 2rem; height: 2rem; border-radius: 50%; background: linear-gradient(135deg, #7c3aed 0%, #4f46e5 100%); color: #ffffff; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.85rem; font-family: 'Plus Jakarta Sans', sans-serif; overflow: hidden; position: relative;">
                                    <span
                                        class="header-avatar-initials"><?= strtoupper(substr(htmlspecialchars($_SESSION['user']['name']), 0, 1)) ?></span>
                                    <img class="header-avatar-img hidden" alt="Avatar"
                                        style="width: 100%; height: 100%; object-fit: cover;">
                                </div>

                                <i data-lucide="chevron-down"
                                    style="width: 1rem; height: 1rem; color: #64748b; transition: transform 0.2s;"
                                    id="profile-chevron"></i>
                            </button>

                            <!-- Dropdown Menu -->
                            <div id="profile-dropdown-menu" class="hidden"
                                style="position: absolute; right: 0; top: 115%; width: 220px; background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.08), 0 8px 10px -6px rgba(15, 23, 42, 0.04); padding: 0.5rem; z-index: 100; flex-direction: column; animation: slideDown 0.15s ease-out; display: none;">
                                <!-- User Information -->
                                <div
                                    style="padding: 0.75rem 1rem; display: flex; flex-direction: column; gap: 0.15rem; text-align: left;">
                                    <span
                                        style="font-size: 0.875rem; font-weight: 700; color: #0f172a; font-family: 'Plus Jakarta Sans', sans-serif;">
                                        <?= htmlspecialchars($_SESSION['user']['name']) ?>
                                    </span>
                                    <span style="font-size: 0.75rem; color: #64748b; word-break: break-all;">
                                        <?= htmlspecialchars($_SESSION['user']['email']) ?>
                                    </span>
                                </div>

                                <!-- Separator -->
                                <div style="height: 1px; background-color: #e2e8f0; margin: 0.4rem 0;"></div>

                                <!-- Menu Links -->

                                <a href="<?= BASE_URL ?>/settings"
                                    style="display: flex; align-items: center; gap: 0.65rem; padding: 0.6rem 0.75rem; color: #475569; font-size: 0.85rem; font-weight: 500; border-radius: 8px; text-decoration: none; transition: background-color 0.2s, color 0.2s;"
                                    onmouseover="this.style.backgroundColor='#f1f5f9'; this.style.color='#7c3aed';"
                                    onmouseout="this.style.backgroundColor='transparent'; this.style.color='#475569';">
                                    <i data-lucide="user-cog" style="width: 1rem; height: 1rem;"></i>
                                    Pengaturan Akun
                                </a>
                                <a href="<?= BASE_URL ?>/logout"
                                    style="display: flex; align-items: center; gap: 0.65rem; padding: 0.6rem 0.75rem; color: #f43f5e; font-size: 0.85rem; font-weight: 600; border-radius: 8px; text-decoration: none; transition: background-color 0.2s;"
                                    onmouseover="this.style.backgroundColor='#fff1f2';"
                                    onmouseout="this.style.backgroundColor='transparent';">
                                    <i data-lucide="log-out" style="width: 1rem; height: 1rem;"></i>
                                    Keluar
                                </a>
                            </div>
                        <?php endif; ?>
                    <?php elseif (strpos($_SERVER['REQUEST_URI'], '/admin') === false): ?>
                        <a href="<?= BASE_URL ?>/login" class="desktop-only"
                            style="color: #7c3aed; text-decoration: none; font-weight: 600; transition: color 0.2s;"
                            onmouseover="this.style.color='#6d28d9'" onmouseout="this.style.color='#7c3aed'">Masuk</a>
                    <?php endif; ?>

                </div>
            </div>
            <!-- Mobile Dropdown Menu -->
            <div id="mobile-menu">
                <?php if (strpos($_SERVER['REQUEST_URI'], '/admin') === false): ?>
                    <a href="<?= BASE_URL ?>/learn"
                        style="color: #475569; text-decoration: none; font-weight: 600; padding: 0.6rem 0.75rem; border-radius: 8px; font-size: 0.9rem; display: block; transition: all 0.2s;"
                        onmouseover="this.style.backgroundColor='#f1f5f9'; this.style.color='#7c3aed';"
                        onmouseout="this.style.backgroundColor='transparent'; this.style.color='#475569';">
                        Belajar
                    </a>
                    <a href="<?= BASE_URL ?>/quiz"
                        style="color: #475569; text-decoration: none; font-weight: 600; padding: 0.6rem 0.75rem; border-radius: 8px; font-size: 0.9rem; display: block; transition: all 0.2s;"
                        onmouseover="this.style.backgroundColor='#f1f5f9'; this.style.color='#7c3aed';"
                        onmouseout="this.style.backgroundColor='transparent'; this.style.color='#475569';">
                        Quiz
                    </a>
                    <a href="<?= BASE_URL ?>/leaderboard"
                        style="color: #475569; text-decoration: none; font-weight: 600; padding: 0.6rem 0.75rem; border-radius: 8px; font-size: 0.9rem; display: block; transition: all 0.2s;"
                        onmouseover="this.style.backgroundColor='#f1f5f9'; this.style.color='#7c3aed';"
                        onmouseout="this.style.backgroundColor='transparent'; this.style.color='#475569';">
                        Leaderboard
                    </a>
                    <?php if ($isAdmin): ?>
                        <a href="<?= BASE_URL ?>/admin"
                            style="color: #7c3aed; text-decoration: none; font-weight: 700; padding: 0.6rem 0.75rem; border-radius: 8px; font-size: 0.9rem; display: block; transition: all 0.2s;"
                            onmouseover="this.style.backgroundColor='#f5f3ff'; this.style.color='#6d28d9';"
                            onmouseout="this.style.backgroundColor='transparent'; this.style.color='#7c3aed';">
                            Admin Panel
                        </a>
                    <?php endif; ?>
                <?php endif; ?>

                <?php if (isset($_SESSION['user'])): ?>
                    <!-- Separator -->
                    <div style="height: 1px; background-color: #e2e8f0; margin: 0.4rem 0.75rem;"></div>





                    <?php if (!$isAdmin): ?>
                        <!-- Settings option -->
                        <a href="<?= BASE_URL ?>/settings"
                            style="color: #475569; text-decoration: none; font-weight: 600; padding: 0.6rem 0.75rem; border-radius: 8px; font-size: 0.9rem; display: block; transition: all 0.2s;"
                            onmouseover="this.style.backgroundColor='#f1f5f9'; this.style.color='#7c3aed';"
                            onmouseout="this.style.backgroundColor='transparent'; this.style.color='#475569';">
                            Pengaturan Profil
                        </a>
                    <?php endif; ?>

                    <!-- Logout option -->
                    <a href="<?= BASE_URL ?>/logout"
                        style="color: #f43f5e; text-decoration: none; font-weight: 600; padding: 0.6rem 0.75rem; border-radius: 8px; font-size: 0.9rem; display: block; transition: all 0.2s;"
                        onmouseover="this.style.backgroundColor='#fff1f2';"
                        onmouseout="this.style.backgroundColor='transparent';">
                        Keluar
                    </a>
                <?php elseif (strpos($_SERVER['REQUEST_URI'], '/admin') === false): ?>
                    <!-- Separator -->
                    <div style="height: 1px; background-color: #e2e8f0; margin: 0.4rem 0.75rem;"></div>

                    <a href="<?= BASE_URL ?>/login"
                        style="color: #7c3aed; text-decoration: none; font-weight: 600; padding: 0.6rem 0.75rem; border-radius: 8px; font-size: 0.9rem; display: block; transition: all 0.2s;"
                        onmouseover="this.style.backgroundColor='#f5f3ff';"
                        onmouseout="this.style.backgroundColor='transparent';">
                        Masuk
                    </a>
                <?php endif; ?>
            </div>
    </header>

    <!-- Achievements & Badges Fullscreen Modal -->
    <?php if (isset($_SESSION['user']) && !$isAdmin): ?>
        <div id="ach-modal" class="modal-overlay">
            <div class="modal-content">
                <button type="button" id="close-ach-modal-btn" class="modal-close-btn" title="Tutup">
                    <i data-lucide="x" style="width: 1.25rem; height: 1.25rem;"></i>
                </button>
                <div style="flex-shrink: 0; padding-bottom: 1rem; border-bottom: 1px solid #f1f5f9;">
                    <h3
                        style="font-size: 1.35rem; font-weight: 800; color: #0f172a; margin-bottom: 0.25rem; font-family: 'Plus Jakarta Sans', sans-serif;">
                        Daftar Semua Lencana & Pencapaian</h3>
                    <p style="font-size: 0.85rem; color: #64748b; margin: 0;">Selesaikan kuis dan kumpulkan skor untuk
                        membuka semua lencana di bawah ini.</p>
                </div>

                <div class="modal-body-scroll"
                    style="overflow-y: auto; flex: 1; padding: 1rem 0.25rem 0.5rem 0.25rem; margin-top: 0.5rem;">
                    <div class="badges-grid" style="margin-top: 0;">
                        <?php foreach ($allBadges as $badge): ?>
                            <div class="badge-modal-card <?= $badge['unlocked'] ? 'unlocked' : '' ?>">
                                <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                                    <strong
                                        style="font-size: 1rem; color: #0f172a; font-family: 'Plus Jakarta Sans', sans-serif;">
                                        <?= htmlspecialchars($badge['title']) ?>
                                    </strong>
                                    <span
                                        style="font-size: 0.75rem; font-weight: 600; padding: 3px 8px; border-radius: 20px; background-color: <?= $badge['unlocked'] ? '#dcfce7' : '#f1f5f9' ?>; color: <?= $badge['unlocked'] ? '#166534' : '#64748b' ?>;">
                                        <?= $badge['unlocked'] ? 'Terbuka' : 'Terkunci' ?>
                                    </span>
                                </div>
                                <p style="font-size: 0.8rem; color: #475569; margin: 0.25rem 0 0.75rem 0; line-height: 1.4;">
                                    <?= htmlspecialchars($badge['description']) ?>
                                </p>
                                <div style="margin-top: auto;">
                                    <div
                                        style="display: flex; justify-content: space-between; align-items: center; font-size: 0.75rem; color: #64748b; font-weight: 500; margin-bottom: 4px;">
                                        <span>Kemajuan</span>
                                        <span><?= $badge['progress'] ?> / <?= $badge['max'] ?></span>
                                    </div>
                                    <div
                                        style="width: 100%; height: 5px; background-color: #e2e8f0; border-radius: 99px; overflow: hidden;">
                                        <div
                                            style="height: 100%; width: <?= $badge['percent'] ?>%; background: linear-gradient(90deg, #7c3aed 0%, #4f46e5 100%); border-radius: 99px;">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
    <main>
        <div class="container">