<?php
$title = $title ?? 'Admin Dashboard';
$quizzes = $quizzes ?? [];
$users_list = $users_list ?? [];
$materials_list = $materials_list ?? [];
$badges_list = $badges_list ?? [];
$stats = $stats ?? [
    'total_quizzes' => count($quizzes),
    'total_users' => count($users_list),
    'total_materials' => count($materials_list)
];
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title) ?> | NetQuiz Admin</title>
    <meta name="description" content="Panel Kontrol Administrator NetQuiz Platform.">

    <!-- Fonts: Plus Jakarta Sans, Inter, & JetBrains Mono -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;600;700&family=Pixelify+Sans:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@500;600;700;800&family=Press+Start+2P&family=Silkscreen:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Chakra+Petch:wght@500;600;700;800&family=DotGothic16&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;600;700;800&family=Pixelify+Sans:wght@500;600;700&family=VT323&display=swap" rel="stylesheet">

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <!-- Top Slim Loading Engine -->
    <script src="<?= function_exists('assetUrl') ? assetUrl('/js/page-loader.js') : (BASE_URL . '/js/page-loader.js') ?>"></script>

    <!-- Custom Dashboard Stylesheet -->
    <link rel="stylesheet" href="<?= function_exists('assetUrl') ? assetUrl('/css/dashboard.css') : (BASE_URL . '/css/dashboard.css') ?>">

    <!-- Global App State & Database Real Datasets -->
    <script>
        window.BASE_URL = "<?= BASE_URL ?>";
        window.CSRF_TOKEN = "<?= \App\Core\Security::generateCsrfToken() ?>";
        window.ADMIN_EMAIL = "<?= htmlspecialchars($_SESSION['user']['email'] ?? 'admin@routerosquiz.academy') ?>";
        window.NETQUIZ_QUIZZES = <?= json_encode($quizzes ?? []) ?>;
        window.NETQUIZ_MEMBERS = <?= json_encode($users_list ?? []) ?>;
        window.NETQUIZ_MATERIALS = <?= json_encode($materials_list ?? []) ?>;
        window.NETQUIZ_BADGES = <?= json_encode($badges_list ?? []) ?>;
    </script>
</head>

<body>
    <!-- Background Canvas Ornaments (Dual-Layer Blueprint Grid - Preserved) -->
    <!-- 8-Bit Pixel Cyber-Grid & Cute Floating Background Ornaments -->
    <div class="bg-ornament-grid" aria-hidden="true"></div>
    <div class="bg-ornament-major-grid" aria-hidden="true"></div>
    <div class="bg-ornament-ambient" aria-hidden="true"></div>
    <div class="viewport-framing-line left-line" aria-hidden="true"></div>
    <div class="viewport-framing-line right-line" aria-hidden="true"></div>

    <!-- Cute 8-Bit Floating Decors Layer (Enhanced Fun & Variety) -->
    <div class="pixel-bg-decor-layer" aria-hidden="true">
        <!-- 1. Pixel Clouds (4 Total) -->
        <svg class="pixel-bg-item pixel-cloud-left-1" viewBox="0 0 32 16" width="64" height="32" fill="none" xmlns="http://www.w3.org/2000/svg">
            <rect x="8" y="2" width="12" height="4" fill="#D4D4D8" />
            <rect x="4" y="6" width="24" height="4" fill="#D4D4D8" />
            <rect x="2" y="10" width="28" height="4" fill="#A1A1AA" />
            <rect x="6" y="4" width="4" height="2" fill="#FFFFFF" />
        </svg>

        <svg class="pixel-bg-item pixel-cloud-right-1" viewBox="0 0 36 18" width="72" height="36" fill="none" xmlns="http://www.w3.org/2000/svg">
            <rect x="10" y="2" width="14" height="4" fill="#D4D4D8" />
            <rect x="4" y="6" width="26" height="4" fill="#D4D4D8" />
            <rect x="2" y="10" width="32" height="4" fill="#A1A1AA" />
            <rect x="8" y="4" width="6" height="2" fill="#FFFFFF" />
        </svg>

        <svg class="pixel-bg-item pixel-cloud-left-2" viewBox="0 0 28 14" width="56" height="28" fill="none" xmlns="http://www.w3.org/2000/svg">
            <rect x="8" y="2" width="10" height="3" fill="#D4D4D8" />
            <rect x="4" y="5" width="20" height="4" fill="#D4D4D8" />
            <rect x="2" y="9" width="24" height="3" fill="#A1A1AA" />
        </svg>

        <svg class="pixel-bg-item pixel-cloud-right-2" viewBox="0 0 30 15" width="60" height="30" fill="none" xmlns="http://www.w3.org/2000/svg">
            <rect x="8" y="2" width="12" height="3" fill="#D4D4D8" />
            <rect x="3" y="5" width="22" height="4" fill="#D4D4D8" />
            <rect x="1" y="9" width="26" height="4" fill="#A1A1AA" />
        </svg>

        <!-- 2. 8-Bit Pixel Game Hearts ♥ (2 Total) -->
        <svg class="pixel-bg-item pixel-heart-1" viewBox="0 0 10 9" width="20" height="18" fill="none" xmlns="http://www.w3.org/2000/svg">
            <rect x="2" y="0" width="2" height="1" fill="#F43F5E" />
            <rect x="6" y="0" width="2" height="1" fill="#F43F5E" />
            <rect x="1" y="1" width="4" height="2" fill="#F43F5E" />
            <rect x="5" y="1" width="4" height="2" fill="#F43F5E" />
            <rect x="2" y="1" width="1" height="1" fill="#FFE4E6" />
            <rect x="1" y="3" width="8" height="2" fill="#F43F5E" />
            <rect x="2" y="5" width="6" height="2" fill="#F43F5E" />
            <rect x="3" y="7" width="4" height="1" fill="#F43F5E" />
            <rect x="4" y="8" width="2" height="1" fill="#F43F5E" />
        </svg>

        <svg class="pixel-bg-item pixel-heart-2" viewBox="0 0 10 9" width="18" height="16" fill="none" xmlns="http://www.w3.org/2000/svg">
            <rect x="2" y="0" width="2" height="1" fill="#FB7185" />
            <rect x="6" y="0" width="2" height="1" fill="#FB7185" />
            <rect x="1" y="1" width="4" height="2" fill="#FB7185" />
            <rect x="5" y="1" width="4" height="2" fill="#FB7185" />
            <rect x="2" y="1" width="1" height="1" fill="#FFF1F2" />
            <rect x="1" y="3" width="8" height="2" fill="#FB7185" />
            <rect x="2" y="5" width="6" height="2" fill="#FB7185" />
            <rect x="3" y="7" width="4" height="1" fill="#FB7185" />
            <rect x="4" y="8" width="2" height="1" fill="#FB7185" />
        </svg>

        <!-- 3. 8-Bit Pixel Lightning Bolts ⚡ (2 Total) -->
        <svg class="pixel-bg-item pixel-lightning-1" viewBox="0 0 8 12" width="16" height="24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <polygon points="5,0 1,6 4,6 3,12 7,5 4,5" fill="#F59E0B" stroke="#D97706" stroke-width="0.5" />
        </svg>

        <svg class="pixel-bg-item pixel-lightning-2" viewBox="0 0 8 12" width="14" height="21" fill="none" xmlns="http://www.w3.org/2000/svg">
            <polygon points="5,0 1,6 4,6 3,12 7,5 4,5" fill="#FBBF24" stroke="#D97706" stroke-width="0.5" />
        </svg>

        <!-- 4. 8-Bit Pixel WiFi Waves 📶 (2 Total) -->
        <svg class="pixel-bg-item pixel-wifi-1" viewBox="0 0 12 10" width="22" height="18" fill="none" xmlns="http://www.w3.org/2000/svg">
            <rect x="1" y="1" width="10" height="2" fill="#38BDF8" />
            <rect x="3" y="4" width="6" height="2" fill="#38BDF8" />
            <rect x="5" y="7" width="2" height="2" fill="#22C55E" />
        </svg>

        <svg class="pixel-bg-item pixel-wifi-2" viewBox="0 0 12 10" width="20" height="16" fill="none" xmlns="http://www.w3.org/2000/svg">
            <rect x="1" y="1" width="10" height="2" fill="#38BDF8" />
            <rect x="3" y="4" width="6" height="2" fill="#38BDF8" />
            <rect x="5" y="7" width="2" height="2" fill="#22C55E" />
        </svg>

        <!-- 5. 8-Bit Pixel Diamonds / Gems 💎 (2 Total) -->
        <svg class="pixel-bg-item pixel-gem-1" viewBox="0 0 12 10" width="20" height="16" fill="none" xmlns="http://www.w3.org/2000/svg">
            <polygon points="3,1 9,1 11,4 6,9 1,4" fill="#38BDF8" stroke="#0284C7" stroke-width="0.6" />
            <polygon points="4,2 8,2 9,4 6,7 3,4" fill="#BAE6FD" />
        </svg>

        <svg class="pixel-bg-item pixel-gem-2" viewBox="0 0 12 10" width="18" height="15" fill="none" xmlns="http://www.w3.org/2000/svg">
            <polygon points="3,1 9,1 11,4 6,9 1,4" fill="#34D399" stroke="#059669" stroke-width="0.6" />
            <polygon points="4,2 8,2 9,4 6,7 3,4" fill="#A7F3D0" />
        </svg>

        <!-- 6. Twinkling Pixel Stars ✦ (6 Total) -->
        <svg class="pixel-bg-item pixel-star-1" viewBox="0 0 9 9" width="18" height="18" fill="none" xmlns="http://www.w3.org/2000/svg">
            <rect x="4" y="0" width="1" height="9" fill="#38BDF8" />
            <rect x="0" y="4" width="9" height="1" fill="#38BDF8" />
            <rect x="3" y="3" width="3" height="3" fill="#0284C7" />
        </svg>

        <svg class="pixel-bg-item pixel-star-2" viewBox="0 0 9 9" width="16" height="16" fill="none" xmlns="http://www.w3.org/2000/svg">
            <rect x="4" y="0" width="1" height="9" fill="#F59E0B" />
            <rect x="0" y="4" width="9" height="1" fill="#F59E0B" />
            <rect x="3" y="3" width="3" height="3" fill="#D97706" />
        </svg>

        <svg class="pixel-bg-item pixel-star-3" viewBox="0 0 9 9" width="16" height="16" fill="none" xmlns="http://www.w3.org/2000/svg">
            <rect x="4" y="0" width="1" height="9" fill="#22C55E" />
            <rect x="0" y="4" width="9" height="1" fill="#22C55E" />
            <rect x="3" y="3" width="3" height="3" fill="#15803D" />
        </svg>

        <svg class="pixel-bg-item pixel-star-4" viewBox="0 0 9 9" width="14" height="14" fill="none" xmlns="http://www.w3.org/2000/svg">
            <rect x="4" y="0" width="1" height="9" fill="#A1A1AA" />
            <rect x="0" y="4" width="9" height="1" fill="#A1A1AA" />
            <rect x="3" y="3" width="3" height="3" fill="#71717A" />
        </svg>

        <svg class="pixel-bg-item pixel-star-5" viewBox="0 0 9 9" width="16" height="16" fill="none" xmlns="http://www.w3.org/2000/svg">
            <rect x="4" y="0" width="1" height="9" fill="#A855F7" />
            <rect x="0" y="4" width="9" height="1" fill="#A855F7" />
            <rect x="3" y="3" width="3" height="3" fill="#7E22CE" />
        </svg>

        <svg class="pixel-bg-item pixel-star-6" viewBox="0 0 9 9" width="15" height="15" fill="none" xmlns="http://www.w3.org/2000/svg">
            <rect x="4" y="0" width="1" height="9" fill="#38BDF8" />
            <rect x="0" y="4" width="9" height="1" fill="#38BDF8" />
            <rect x="3" y="3" width="3" height="3" fill="#0284C7" />
        </svg>

        <!-- 7. 3D Voxel Data Packet Cubes (4 Total) -->
        <svg class="pixel-bg-item pixel-cube-1" viewBox="0 0 16 16" width="22" height="22" fill="none" xmlns="http://www.w3.org/2000/svg">
            <polygon points="8,1 15,5 8,9 1,5" fill="#D4D4D8" />
            <polygon points="1,5 8,9 8,15 1,11" fill="#A1A1AA" />
            <polygon points="8,9 15,5 15,11 8,15" fill="#71717A" />
        </svg>

        <svg class="pixel-bg-item pixel-cube-2" viewBox="0 0 16 16" width="20" height="20" fill="none" xmlns="http://www.w3.org/2000/svg">
            <polygon points="8,1 15,5 8,9 1,5" fill="#D4D4D8" />
            <polygon points="1,5 8,9 8,15 1,11" fill="#A1A1AA" />
            <polygon points="8,9 15,5 15,11 8,15" fill="#71717A" />
        </svg>

        <svg class="pixel-bg-item pixel-cube-3" viewBox="0 0 16 16" width="22" height="22" fill="none" xmlns="http://www.w3.org/2000/svg">
            <polygon points="8,1 15,5 8,9 1,5" fill="#D4D4D8" />
            <polygon points="1,5 8,9 8,15 1,11" fill="#A1A1AA" />
            <polygon points="8,9 15,5 15,11 8,15" fill="#71717A" />
        </svg>

        <svg class="pixel-bg-item pixel-cube-4" viewBox="0 0 16 16" width="18" height="18" fill="none" xmlns="http://www.w3.org/2000/svg">
            <polygon points="8,1 15,5 8,9 1,5" fill="#D4D4D8" />
            <polygon points="1,5 8,9 8,15 1,11" fill="#A1A1AA" />
            <polygon points="8,9 15,5 15,11 8,15" fill="#71717A" />
        </svg>
    </div>

    <!-- 1. TOP NAVBAR (STRICT RULE 1 PRESERVED) -->
    <header class="admin-top-nav">
        <div class="admin-nav-container">
            <div class="nav-brand-group">
                <a href="<?= BASE_URL ?>/admin" class="nav-brand-title" aria-label="NetQuiz Admin Dashboard">
                    <div class="nav-brand-mark">
                        <i data-lucide="terminal" class="nav-brand-icon"></i>
                        <span class="live-dot" title="Server Status: Online"></span>
                    </div>
                    <span class="nav-brand-text">Net<span class="nav-brand-accent">Quiz</span><span class="brand-cursor">_</span></span>
                </a>
            </div>

            <div class="nav-controls-group">
                <span class="nav-admin-badge font-mono" title="Role: Administrator System">
                    <i data-lucide="shield-check" class="nav-user-icon"></i>
                    <span class="nav-text-desktop">Administrator</span>
                </span>

                <!-- Hairline Context Divider -->
                <div class="nav-context-divider" aria-hidden="true"></div>

                <a href="<?= BASE_URL ?>/" class="btn-geist-nav-secondary" title="Kembali ke situs publik">
                    <i data-lucide="external-link" style="width: 14px; height: 14px;"></i>
                    <span class="nav-text-desktop">Situs Utama</span>
                </a>
                <a href="<?= BASE_URL ?>/logout" class="btn-geist-nav-danger" title="Keluar dari sesi admin">
                    <i data-lucide="log-out" style="width: 14px; height: 14px;"></i>
                    <span class="nav-text-desktop">Keluar</span>
                </a>
            </div>
        </div>
    </header>

    <!-- MAIN APP LAYOUT (VERTICAL SIDEBAR + MAIN CANVAS SHELL) -->
    <div class="admin-app-layout">
        <!-- 2. LEFT VERTICAL SIDEBAR (STRICT RULE 2: PERMANENT + Buat Kuis Baru BUTTON) -->
        <aside id="admin-vertical-sidebar" class="admin-vertical-sidebar" aria-label="Navigasi Panel Administrator">
            <!-- Mobile Drawer Header with Close Button (X) -->
            <div class="sidebar-header-mobile">
                <span class="sidebar-mobile-title font-heading">Menu Navigasi</span>
                <button type="button" id="sidebar-close-btn" class="sidebar-close-btn" title="Tutup Menu">
                    <i data-lucide="x" style="width: 16px; height: 16px;"></i>
                </button>
            </div>

            <!-- Permanent Canonical Sidebar Top Button (+ Buat Kuis Baru) -->
            <button type="button" class="sidebar-cta-btn active" data-target="quiz-section" aria-selected="true" role="tab" id="tab-btn-quiz-section">
                <span class="sidebar-menu-label-group">
                    <span>Buat Kuis Baru</span>
                </span>
            </button>

            <!-- Separator / Divider -->
            <div class="sidebar-separator" aria-hidden="true"></div>

            <!-- Navigation Menu List -->
            <nav class="sidebar-menu-list" role="tablist" aria-label="Menu Utama Administrator">
                <button type="button" class="sidebar-menu-btn" role="tab" aria-selected="false" aria-controls="member-section" id="tab-btn-member-section" data-target="member-section">
                    <span class="sidebar-menu-label-group">
                        <i data-lucide="user-plus" class="menu-icon"></i>
                        <span>Daftarkan Member</span>
                    </span>
                </button>
                <button type="button" class="sidebar-menu-btn" role="tab" aria-selected="false" aria-controls="manage-section" id="tab-btn-manage-section" data-target="manage-section">
                    <span class="sidebar-menu-label-group">
                        <i data-lucide="users" class="menu-icon"></i>
                        <span>Manajemen Member</span>
                    </span>
                    <span class="sidebar-counter-badge" id="sidebar-count-members">0</span>
                </button>
                <button type="button" class="sidebar-menu-btn" role="tab" aria-selected="false" aria-controls="materials-section" id="tab-btn-materials-section" data-target="materials-section">
                    <span class="sidebar-menu-label-group">
                        <i data-lucide="book-open" class="menu-icon"></i>
                        <span>Materi Belajar</span>
                    </span>
                    <span class="sidebar-counter-badge" id="sidebar-count-materials">0</span>
                </button>
                <button type="button" class="sidebar-menu-btn" role="tab" aria-selected="false" aria-controls="badge-section" id="tab-btn-badge-section" data-target="badge-section">
                    <span class="sidebar-menu-label-group">
                        <i data-lucide="award" class="menu-icon"></i>
                        <span>Lencana</span>
                    </span>
                    <span class="sidebar-counter-badge" id="sidebar-count-badges">0</span>
                </button>
            </nav>
        </aside>

        <!-- 3. UNIFIED CANVAS SHELL CONTAINER (MAX 1152px / max-w-6xl RATA KIRI) -->
        <main class="admin-main-canvas" id="admin-workspace">
            <div id="admin-canvas-shell" class="canvas-shell-container max-w-6xl">
                <!-- Dynamic Page Header Bar (Breadcrumb + Title Left + Action Right) -->
                <div id="admin-page-header" class="admin-page-header">
                    <div class="page-header-text-group">
                        <nav class="admin-breadcrumb-nav" aria-label="Breadcrumb">
                            <ol class="admin-breadcrumb-list">
                                <li class="breadcrumb-item">
                                    <a href="<?= BASE_URL ?>/admin#quiz-section" class="breadcrumb-link">
                                        <i data-lucide="layout-dashboard" class="breadcrumb-icon"></i>
                                        <span>Dashboard</span>
                                    </a>
                                </li>
                                <li class="breadcrumb-separator" aria-hidden="true">/</li>
                                <li class="breadcrumb-item active" aria-current="page" id="breadcrumb-active-title">Buat Kuis</li>
                            </ol>
                        </nav>
                    </div>
                    <div id="page-header-action-container" class="page-header-action-group">
                        <!-- Dynamic page primary action button rendered here -->
                    </div>
                </div>

                <!-- System Session Alerts -->
                <?php if (isset($_SESSION['admin_success'])): ?>
                    <div style="background-color: #F0FDF4; border: 1px solid #BBF7D0; color: #166534; padding: 0.75rem 1rem; border-radius: 8px; margin-bottom: 1.5rem; font-size: 0.875rem;">
                        <?= htmlspecialchars($_SESSION['admin_success']);
                        unset($_SESSION['admin_success']); ?>
                    </div>
                <?php endif; ?>
                <?php if (isset($_SESSION['admin_error'])): ?>
                    <div style="background-color: #FFF0F0; border: 1px solid #FFC0C0; color: #CC0000; padding: 0.75rem 1rem; border-radius: 8px; margin-bottom: 1.5rem; font-size: 0.875rem;">
                        <?= htmlspecialchars($_SESSION['admin_error']);
                        unset($_SESSION['admin_error']); ?>
                    </div>
                <?php endif; ?>

                <!-- Scoped Module Workspace Sections -->
                <div id="quiz-section" class="admin-section-content active" role="tabpanel" aria-labelledby="tab-btn-quiz-section"></div>
                <div id="member-section" class="admin-section-content" role="tabpanel" aria-labelledby="tab-btn-member-section"></div>
                <div id="manage-section" class="admin-section-content" role="tabpanel" aria-labelledby="tab-btn-manage-section"></div>
                <div id="materials-section" class="admin-section-content" role="tabpanel" aria-labelledby="tab-btn-materials-section"></div>
                <div id="badge-section" class="admin-section-content" role="tabpanel" aria-labelledby="tab-btn-badge-section"></div>
            </div>
        </main>
    </div>

    <!-- 4. FLOATING BULK ACTION BAR (MEMBER MANAGEMENT) -->
    <div id="floating-bulk-bar" class="floating-bulk-bar" aria-live="polite">
        <span id="bulk-selected-count" class="bulk-count-badge">0 Dipilih</span>
        <div class="bulk-actions-group">
            <button type="button" id="btn-bulk-activate" class="btn-bulk-action">
                <i data-lucide="check-circle" style="width: 14px; height: 14px;"></i>
                <span>Aktifkan</span>
            </button>
            <button type="button" id="btn-bulk-suspend" class="btn-bulk-action">
                <i data-lucide="user-x" style="width: 14px; height: 14px;"></i>
                <span>Nonaktifkan</span>
            </button>
            <button type="button" id="btn-bulk-export" class="btn-bulk-action">
                <i data-lucide="download" style="width: 14px; height: 14px;"></i>
                <span>Export Terpilih</span>
            </button>
        </div>
        <button type="button" id="btn-bulk-dismiss" class="btn-bulk-dismiss" title="Batal Pilihan">
            <i data-lucide="x" style="width: 16px; height: 16px;"></i>
        </button>
    </div>

    <!-- 5. EDIT MEMBER MODAL (ROOT OVERLAY) -->
    <div id="edit-member-modal" class="admin-modal-overlay" aria-hidden="true" role="dialog" aria-modal="true">
        <div class="admin-modal-content" style="max-width: 480px;">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.25rem;">
                <div>
                    <h3 style="font-family: var(--font-heading); font-size: 1.05rem; font-weight: 800; color: #18181B; margin: 0;">Edit Data Member</h3>
                    <p style="font-size: 0.8rem; color: #52525B; margin-top: 0.2rem;">Perbarui username, email, atau reset kata sandi siswa.</p>
                </div>
                <button type="button" onclick="closeEditMemberModal()" class="toast-close-btn" title="Tutup">
                    <i data-lucide="x" style="width: 16px; height: 16px;"></i>
                </button>
            </div>
            <form id="edit-member-form" method="POST">
                <input type="hidden" name="csrf_token" value="<?= \App\Core\Security::generateCsrfToken() ?>">
                <div class="form-field-group">
                    <label class="form-field-label">Username Siswa</label>
                    <input type="text" class="form-field-input" name="username" id="edit-member-username" required>
                </div>
                <div class="form-field-group">
                    <label class="form-field-label">Alamat Email</label>
                    <input type="email" class="form-field-input" name="email" id="edit-member-email" required>
                </div>
                <div class="form-field-group">
                    <label class="form-field-label">Reset Password Baru (Opsional)</label>
                    <div class="password-input-wrapper">
                        <input type="password" class="form-field-input" name="password" id="edit-member-password" placeholder="Kosongkan jika tidak diubah">
                        <button type="button" class="btn-toggle-password" data-target="edit-member-password" title="Lihat/Sembunyikan Kata Sandi">
                            <i data-lucide="eye" style="width: 15px; height: 15px;"></i>
                        </button>
                    </div>
                </div>
                <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 1.25rem;">
                    <button type="button" class="btn-secondary-outline" onclick="closeEditMemberModal()">Batal</button>
                    <button type="submit" class="btn-primary-black">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- 6. GEIST CONFIRMATION MODAL -->
    <div id="geist-confirm-overlay" class="geist-confirm-overlay" aria-hidden="true">
        <div class="geist-confirm-box">
            <div id="confirm-icon-container" class="confirm-icon-box confirm-icon-danger">
                <i id="confirm-icon" data-lucide="alert-triangle" style="width: 24px; height: 24px;"></i>
            </div>
            <h3 id="confirm-modal-title" class="confirm-title">Konfirmasi Aksi</h3>
            <p id="confirm-modal-message" class="confirm-message">Apakah Anda yakin ingin melanjutkan tindakan ini?</p>
            <div class="confirm-btn-actions">
                <button type="button" id="btn-confirm-cancel" class="btn-confirm-cancel">Batal</button>
                <button type="button" id="btn-confirm-submit" class="btn-confirm-danger">Lanjutkan</button>
            </div>
        </div>
    </div>

    <!-- 8. FLOATING BOTTOM BUTTON NAVIGATION DOCK (MOBILE & TABLET EXCLUSIVE) -->
    <nav id="admin-floating-bottom-nav" class="admin-floating-bottom-nav" role="tablist" aria-label="Navigasi Cepat Panel Admin">
        <div class="floating-nav-container">
            <!-- 1. Buat Kuis Baru (Active Default / CTA Hero) -->
            <button type="button" class="floating-bottom-btn active" data-target="quiz-section" role="tab" aria-selected="true" aria-controls="quiz-section" id="floating-btn-quiz-section" title="Buat Kuis Baru" aria-label="Buat Kuis Baru">
                <span class="floating-btn-icon-wrapper">
                    <i data-lucide="plus" class="floating-btn-icon"></i>
                </span>
            </button>

            <!-- 2. Daftarkan Member -->
            <button type="button" class="floating-bottom-btn" data-target="member-section" role="tab" aria-selected="false" aria-controls="member-section" id="floating-btn-member-section" title="Daftarkan Member" aria-label="Daftarkan Member">
                <span class="floating-btn-icon-wrapper">
                    <i data-lucide="user-plus" class="floating-btn-icon"></i>
                </span>
            </button>

            <!-- 3. Manajemen Member -->
            <button type="button" class="floating-bottom-btn" data-target="manage-section" role="tab" aria-selected="false" aria-controls="manage-section" id="floating-btn-manage-section" title="Manajemen Member" aria-label="Manajemen Member">
                <span class="floating-btn-icon-wrapper">
                    <i data-lucide="users" class="floating-btn-icon"></i>
                    <span class="floating-counter-badge" id="floating-count-members">0</span>
                </span>
            </button>

            <!-- 4. Materi Belajar -->
            <button type="button" class="floating-bottom-btn" data-target="materials-section" role="tab" aria-selected="false" aria-controls="materials-section" id="floating-btn-materials-section" title="Materi Belajar" aria-label="Materi Belajar">
                <span class="floating-btn-icon-wrapper">
                    <i data-lucide="book-open" class="floating-btn-icon"></i>
                    <span class="floating-counter-badge" id="floating-count-materials">0</span>
                </span>
            </button>

            <!-- 5. Lencana -->
            <button type="button" class="floating-bottom-btn" data-target="badge-section" role="tab" aria-selected="false" aria-controls="badge-section" id="floating-btn-badge-section" title="Lencana Prestasi" aria-label="Lencana">
                <span class="floating-btn-icon-wrapper">
                    <i data-lucide="award" class="floating-btn-icon"></i>
                    <span class="floating-counter-badge" id="floating-count-badges">0</span>
                </span>
            </button>
        </div>
    </nav>

    <!-- Scripts -->
    <script src="<?= function_exists('assetUrl') ? assetUrl('/js/admin-dashboard.js') : (BASE_URL . '/js/admin-dashboard.js') ?>"></script>
    <script src="<?= function_exists('assetUrl') ? assetUrl('/js/admin-builder.js') : (BASE_URL . '/js/admin-builder.js') ?>"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            if (window.lucide) {
                window.lucide.createIcons();
            }
        });
    </script>
</body>

</html>