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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@500;600&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap" rel="stylesheet">

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
    <div class="bg-ornament-grid" aria-hidden="true"></div>
    <div class="bg-ornament-major-grid" aria-hidden="true"></div>
    <div class="bg-ornament-ambient" aria-hidden="true"></div>
    <div class="viewport-framing-line left-line" aria-hidden="true"></div>
    <div class="viewport-framing-line right-line" aria-hidden="true"></div>

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