<?php require_once dirname(__DIR__) . '/templates/header.php'; ?>

<!-- Custom Styles for Settings Page -->
<link rel="stylesheet" href="<?= BASE_URL ?>/css/settings.css?v=<?= time() ?>">

<!-- Inject Base URL to JS -->
<script>
    window.BASE_URL = "<?= BASE_URL ?>";
    window.CSRF_TOKEN = "<?= \App\Core\Security::generateCsrfToken() ?>";
</script>

<div class="settings-header"
    style="display: flex; flex-direction: column; align-items: flex-start; gap: 0.75rem; margin-bottom: 2rem;">
    <!-- Breadcrumb Navigation -->
    <nav class="breadcrumb"
        style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem; font-size: 0.85rem; font-weight: 500; color: #64748b; font-family: 'Plus Jakarta Sans', sans-serif;">
        <span style="color: #64748b;">Dashboard</span>
        <span style="color: #cbd5e1;">/</span>
        <span style="color: #0f172a; font-weight: 600;">Pengaturan Akun</span>
    </nav>
    <a href="<?= BASE_URL ?>/" class="btn-secondary" style="align-self: flex-start;">
        <i data-lucide="arrow-left" style="width: 1rem; height: 1rem;"></i>
        Kembali ke Dashboard
    </a>
</div>

<div class="settings-layout">
    <!-- Left Sidebar: Profile Summary & Nav Tabs -->
    <aside class="settings-sidebar">
        <div class="sidebar-profile-card">
            <button type="button" class="profile-avatar-large" id="avatar-trigger-btn" title="Ubah Foto Profil">
                <span
                    class="avatar-initials"><?= strtoupper(substr(htmlspecialchars($user['username']), 0, 1)) ?></span>
                <img id="avatar-preview-img" class="hidden" alt="Avatar Preview"
                    style="width: 100%; height: 100%; object-fit: cover;">
                <div class="avatar-overlay">
                    <i data-lucide="camera" style="width: 1.35rem; height: 1.35rem; color: #ffffff;"></i>
                </div>
            </button>
            <input type="file" id="avatar-file-input" class="hidden" accept="image/*">
            <h3 class="profile-name-title"><?= htmlspecialchars($user['username']) ?></h3>
            <span class="profile-email-subtitle"><?= htmlspecialchars($user['email']) ?></span>
            <div class="profile-meta-date">
                <span>Terdaftar: <?= date('d M Y', strtotime($user['created_at'])) ?></span>
            </div>
        </div>

        <nav class="sidebar-nav-menu">
            <button class="sidebar-nav-item active" data-target="profile-card">
                <i data-lucide="user"></i>
                <span>Profil Saya</span>
            </button>
            <button class="sidebar-nav-item" data-target="password-card">
                <i data-lucide="lock"></i>
                <span>Keamanan</span>
            </button>
            <button class="sidebar-nav-item" data-target="badge-card">
                <i data-lucide="award"></i>
                <span>Lencana Saya</span>
            </button>
        </nav>
    </aside>

    <!-- Right Panels: Forms -->
    <div class="settings-content">
        <!-- 1. Profile Information Panel -->
        <section id="profile-card" class="settings-card active">
            <div class="settings-card-header">
                <h3 class="settings-card-title">Informasi Profil</h3>
                <p class="settings-card-desc">Perbarui detail profil akun Anda, seperti nama lengkap dan alamat email.
                </p>
            </div>

            <!-- Form -->
            <form id="profile-settings-form" class="settings-form" novalidate>
                <!-- Alert Box -->
                <div class="alert-banner hidden" role="alert">
                    <i data-lucide="alert-circle" class="alert-icon"></i>
                    <span></span>
                </div>

                <!-- Username Input -->
                <div class="input-group">
                    <label for="username" class="input-label">Nama Lengkap</label>
                    <div class="input-wrapper">
                        <span class="input-icon-left">
                        </span>
                        <input type="text" id="username" name="username" class="form-input"
                            value="<?= htmlspecialchars($user['username']) ?>" placeholder="Masukkan nama lengkap Anda"
                            required>
                    </div>
                    <span class="error-msg" id="username-error"></span>
                </div>

                <!-- Email Input -->
                <div class="input-group">
                    <label for="email" class="input-label">Alamat Email (Tidak dapat diubah)</label>
                    <div class="input-wrapper">
                        <span class="input-icon-left">
                        </span>
                        <input type="email" id="email" name="email" class="form-input"
                            value="<?= htmlspecialchars($user['email']) ?>" placeholder="nama@email.com" required disabled style="background-color: #f1f5f9; color: #64748b; cursor: not-allowed;">
                    </div>
                    <span class="error-msg" id="email-error"></span>
                </div>

                <!-- Submit -->
                <button type="submit" class="btn-primary">
                    <i data-lucide="save" style="width: 1.15rem; height: 1.15rem;"></i>
                    <span class="btn-text">Simpan Perubahan</span>
                    <span class="loading-spinner hidden"></span>
                </button>
            </form>
        </section>

        <!-- 2. Password Panel -->
        <section id="password-card" class="settings-card">
            <div class="settings-card-header">
                <h3 class="settings-card-title">Ubah Password</h3>
                <p class="settings-card-desc">Amankan akun Anda dengan menggunakan password yang kuat dan unik.</p>
            </div>

            <!-- Form -->
            <form id="password-settings-form" class="settings-form" novalidate>
                <!-- Alert Box -->
                <div class="alert-banner hidden" role="alert">
                    <i data-lucide="alert-circle" class="alert-icon"></i>
                    <span></span>
                </div>

                <!-- Current Password -->
                <div class="input-group">
                    <label for="current_password" class="input-label">Password Saat Ini</label>
                    <div class="input-wrapper">
                        <span class="input-icon-left">
                        </span>
                        <input type="password" id="current_password" name="current_password" class="form-input"
                            placeholder="Ketik password saat ini" required>
                        <button type="button" class="password-toggle" aria-label="Tampilkan password"
                            data-target="current_password">
                            <i data-lucide="eye" class="toggle-icon-show"></i>
                            <i data-lucide="eye-off" class="toggle-icon-hide hidden"></i>
                        </button>
                    </div>
                    <span class="error-msg" id="current_password-error"></span>
                </div>

                <!-- New Password -->
                <div class="input-group">
                    <label for="new_password" class="input-label">Password Baru</label>
                    <div class="input-wrapper">
                        <span class="input-icon-left">
                        </span>
                        <input type="password" id="new_password" name="new_password" class="form-input"
                            placeholder="Minimal 8 karakter" required>
                        <button type="button" class="password-toggle" aria-label="Tampilkan password"
                            data-target="new_password">
                            <i data-lucide="eye" class="toggle-icon-show"></i>
                            <i data-lucide="eye-off" class="toggle-icon-hide hidden"></i>
                        </button>
                    </div>
                    <span class="error-msg" id="new_password-error"></span>
                </div>

                <!-- Confirm Password -->
                <div class="input-group">
                    <label for="confirm_password" class="input-label">Konfirmasi Password Baru</label>
                    <div class="input-wrapper">
                        <span class="input-icon-left">
                        </span>
                        <input type="password" id="confirm_password" name="confirm_password" class="form-input"
                            placeholder="Ulangi password baru" required>
                        <button type="button" class="password-toggle" aria-label="Tampilkan password"
                            data-target="confirm_password">
                            <i data-lucide="eye" class="toggle-icon-show"></i>
                            <i data-lucide="eye-off" class="toggle-icon-hide hidden"></i>
                        </button>
                    </div>
                    <span class="error-msg" id="confirm_password-error"></span>
                </div>

                <!-- Submit -->
                <button type="submit" class="btn-primary btn-sm">
                    <i data-lucide="key-round" style="width: 1rem; height: 1rem;"></i>
                    <span class="btn-text">Perbarui Password</span>
                    <span class="loading-spinner hidden"></span>
                </button>
            </form>
        </section>

        <!-- 3. Badges Panel -->
        <section id="badge-card" class="settings-card">
            <div class="settings-card-header">
                <h3 class="settings-card-title">Lencana Saya</h3>
                <p class="settings-card-desc">Daftar lencana yang berhasil Anda buka selama mengerjakan kuis.</p>
            </div>

            <div class="settings-badges-scroll"
                style="display: flex; flex-direction: column; gap: 0.75rem; height: calc(100% - 4.5rem); overflow-y: auto; padding-right: 0.25rem;">
                <?php
                $unlockedCount = 0;
                foreach ($allBadges as $badge) {
                    if ($badge['unlocked']) {
                        $unlockedCount++;
                        ?>
                        <div
                            style="display: flex; justify-content: space-between; align-items: center; padding: 0.75rem 1rem; background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px;">
                            <div>
                                <strong
                                    style="color: #0f172a; font-size: 0.9rem; display: block; font-family: 'Plus Jakarta Sans', sans-serif;"><?= htmlspecialchars($badge['title']) ?></strong>
                                <span
                                    style="color: #64748b; font-size: 0.75rem;"><?= htmlspecialchars($badge['description']) ?></span>
                            </div>
                            <span
                                style="font-size: 0.7rem; font-weight: 700; color: #166534; background-color: #dcfce7; padding: 3px 10px; border-radius: 20px;">Terbuka</span>
                        </div>
                        <?php
                    }
                }

                if ($unlockedCount === 0) {
                    ?>
                    <div
                        style="text-align: center; padding: 2rem 1rem; color: #94a3b8; font-style: italic; font-size: 0.85rem;">
                        Belum ada lencana yang terbuka. Terus kerjakan kuis untuk mendapatkan lencana!
                    </div>
                    <?php
                }
                ?>
            </div>
        </section>
    </div>
</div>

<!-- Settings JS Logic -->
<script src="<?= BASE_URL ?>/js/settings.js?v=<?= time() ?>"></script>

<?php require_once dirname(__DIR__) . '/templates/footer.php'; ?>