<?php
$user = $user ?? [
    'id' => $_SESSION['user']['id'] ?? 0,
    'username' => $_SESSION['user']['name'] ?? 'Siswa',
    'email' => $_SESSION['user']['email'] ?? '',
    'created_at' => date('Y-m-d H:i:s')
];
$allBadges = $allBadges ?? [];

require_once dirname(__DIR__) . '/templates/header.php';
?>

<!-- Inject Base URL and CSRF Token -->
<script>
    window.BASE_URL = "<?= BASE_URL ?>";
    window.CSRF_TOKEN = "<?= \App\Core\Security::generateCsrfToken() ?>";
</script>

<!-- Breadcrumb & Header -->
<div style="margin-bottom: 2rem;">
    <nav class="admin-breadcrumb-nav" aria-label="Breadcrumb" style="margin-bottom: 0.5rem;">
        <span class="breadcrumb-item">Siswa</span>
        <span class="breadcrumb-separator">/</span>
        <span class="breadcrumb-active">Pengaturan Akun</span>
    </nav>
    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h1 style="font-family: var(--font-heading); font-size: 1.5rem; font-weight: 800; color: #18181B; margin: 0 0 0.25rem 0; letter-spacing: -0.02em;">
                Pengaturan Akun & Profil
            </h1>
            <p style="font-size: 0.875rem; color: #71717A; margin: 0;">
                Kelola informasi identitas, kata sandi, dan lihat koleksi lencana prestasi Anda.
            </p>
        </div>
        <a href="<?= BASE_URL ?>/" class="btn-secondary-outline" style="font-size: 0.85rem; padding: 0.45rem 0.85rem;">
            <i data-lucide="arrow-left" style="width: 14px; height: 14px;"></i>
            <span>Kembali ke Dashboard</span>
        </a>
    </div>
</div>

<!-- 2-Column Settings Layout -->
<div style="display: grid; grid-template-columns: 280px 1fr; gap: 1.5rem; align-items: start;">
    <!-- LEFT SIDEBAR: User Card & Tab Nav -->
    <aside style="display: flex; flex-direction: column; gap: 1rem;">
        <!-- User Profile Card -->
        <div class="supabase-panel-card" style="padding: 1.5rem; text-align: center;">
            <span class="corner-crosshair corner-tl">+</span>
            <span class="corner-crosshair corner-tr">+</span>
            <span class="corner-crosshair corner-bl">+</span>
            <span class="corner-crosshair corner-br">+</span>

            <div style="width: 56px; height: 56px; border-radius: 50%; background-color: #18181B; color: #FFFFFF; font-size: 1.35rem; font-weight: 800; display: flex; align-items: center; justify-content: center; margin: 0 auto 0.85rem; font-family: var(--font-heading);">
                <?= strtoupper(substr(htmlspecialchars($user['username']), 0, 1)) ?>
            </div>

            <h3 style="font-family: var(--font-heading); font-size: 1rem; font-weight: 800; color: #18181B; margin: 0 0 0.2rem 0;">
                <?= htmlspecialchars($user['username']) ?>
            </h3>
            <span style="font-size: 0.8rem; color: #71717A; display: block; margin-bottom: 0.75rem; word-break: break-all;">
                <?= htmlspecialchars($user['email']) ?>
            </span>
            <span class="status-badge" style="background-color: #F4F4F5; font-size: 0.7rem;">
                Terdaftar: <?= date('d M Y', strtotime($user['created_at'])) ?>
            </span>
        </div>

        <!-- Nav Tab Switcher -->
        <nav style="display: flex; flex-direction: column; gap: 0.35rem;">
            <button type="button" class="sidebar-nav-item active" data-target="profile-card" style="display: flex; align-items: center; gap: 0.65rem; padding: 0.65rem 0.85rem; background: #18181B; color: #FFFFFF; border: 1px solid #18181B; border-radius: 6px; font-weight: 600; font-size: 0.85rem; cursor: pointer; text-align: left; transition: all 0.15s ease;" onclick="switchSettingsTab('profile-card', this)">
                <i data-lucide="user" style="width: 15px; height: 15px;"></i>
                <span>Profil Saya</span>
            </button>
            <button type="button" class="sidebar-nav-item" data-target="password-card" style="display: flex; align-items: center; gap: 0.65rem; padding: 0.65rem 0.85rem; background: #FFFFFF; color: #52525B; border: 1px solid #E5E7EB; border-radius: 6px; font-weight: 600; font-size: 0.85rem; cursor: pointer; text-align: left; transition: all 0.15s ease;" onclick="switchSettingsTab('password-card', this)">
                <i data-lucide="lock" style="width: 15px; height: 15px;"></i>
                <span>Keamanan Password</span>
            </button>
            <button type="button" class="sidebar-nav-item" data-target="badge-card" style="display: flex; align-items: center; gap: 0.65rem; padding: 0.65rem 0.85rem; background: #FFFFFF; color: #52525B; border: 1px solid #E5E7EB; border-radius: 6px; font-weight: 600; font-size: 0.85rem; cursor: pointer; text-align: left; transition: all 0.15s ease;" onclick="switchSettingsTab('badge-card', this)">
                <i data-lucide="award" style="width: 15px; height: 15px;"></i>
                <span>Koleksi Lencana</span>
            </button>
        </nav>
    </aside>

    <!-- RIGHT CONTENT: Tab Panels -->
    <div>
        <!-- 1. TAB PROFIL -->
        <section id="profile-card" class="settings-card active supabase-panel-card" style="padding: 2rem;">
            <span class="corner-crosshair corner-tl">+</span>
            <span class="corner-crosshair corner-tr">+</span>
            <span class="corner-crosshair corner-bl">+</span>
            <span class="corner-crosshair corner-br">+</span>

            <div style="margin-bottom: 1.5rem; padding-bottom: 0.75rem; border-bottom: 1px solid #E5E7EB;">
                <h3 style="font-family: var(--font-heading); font-size: 1.1rem; font-weight: 800; color: #18181B; margin: 0 0 0.25rem 0;">Informasi Profil</h3>
                <p style="font-size: 0.825rem; color: #71717A; margin: 0;">Perbarui nama akun tampilan dan alamat email Anda.</p>
            </div>

            <form id="profile-settings-form">
                <div class="form-grid-2col" style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem;">
                    <div class="form-field-group">
                        <label class="form-field-label">Nama Lengkap</label>
                        <input type="text" class="form-field-input" id="username" name="username" value="<?= htmlspecialchars($user['username']) ?>" required>
                    </div>
                    <div class="form-field-group">
                        <label class="form-field-label">Alamat Email</label>
                        <input type="email" class="form-field-input" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
                    </div>
                </div>

                <div style="display: flex; justify-content: flex-end;">
                    <button type="submit" class="btn-primary-black" style="font-size: 0.85rem; padding: 0.5rem 1.25rem;">
                        <i data-lucide="check" style="width: 14px; height: 14px;"></i>
                        <span>Simpan Perubahan</span>
                    </button>
                </div>
            </form>
        </section>

        <!-- 2. TAB PASSWORD -->
        <section id="password-card" class="settings-card supabase-panel-card" style="padding: 2rem; display: none;">
            <span class="corner-crosshair corner-tl">+</span>
            <span class="corner-crosshair corner-tr">+</span>
            <span class="corner-crosshair corner-bl">+</span>
            <span class="corner-crosshair corner-br">+</span>

            <div style="margin-bottom: 1.5rem; padding-bottom: 0.75rem; border-bottom: 1px solid #E5E7EB;">
                <h3 style="font-family: var(--font-heading); font-size: 1.1rem; font-weight: 800; color: #18181B; margin: 0 0 0.25rem 0;">Keamanan & Kata Sandi</h3>
                <p style="font-size: 0.825rem; color: #71717A; margin: 0;">Ganti kata sandi akun Anda secara mandiri untuk menjaga keamanan.</p>
            </div>

            <form id="password-settings-form">
                <div class="form-field-group" style="margin-bottom: 1rem;">
                    <label class="form-field-label">Kata Sandi Saat Ini</label>
                    <input type="password" class="form-field-input" id="current_password" name="current_password" placeholder="Masukkan kata sandi lama Anda" required>
                </div>

                <div class="form-grid-2col" style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem;">
                    <div class="form-field-group">
                        <label class="form-field-label">Kata Sandi Baru</label>
                        <input type="password" class="form-field-input" id="new_password" name="new_password" placeholder="Minimal 8 karakter" required>
                    </div>
                    <div class="form-field-group">
                        <label class="form-field-label">Konfirmasi Kata Sandi Baru</label>
                        <input type="password" class="form-field-input" id="confirm_password" name="confirm_password" placeholder="Ulangi kata sandi baru" required>
                    </div>
                </div>

                <div style="display: flex; justify-content: flex-end;">
                    <button type="submit" class="btn-primary-black" style="font-size: 0.85rem; padding: 0.5rem 1.25rem;">
                        <i data-lucide="lock" style="width: 14px; height: 14px;"></i>
                        <span>Perbarui Kata Sandi</span>
                    </button>
                </div>
            </form>
        </section>

        <!-- 3. TAB LENCANA SAYA -->
        <section id="badge-card" class="settings-card supabase-panel-card" style="padding: 2rem; display: none;">
            <span class="corner-crosshair corner-tl">+</span>
            <span class="corner-crosshair corner-tr">+</span>
            <span class="corner-crosshair corner-bl">+</span>
            <span class="corner-crosshair corner-br">+</span>

            <div style="margin-bottom: 1.5rem; padding-bottom: 0.75rem; border-bottom: 1px solid #E5E7EB;">
                <h3 style="font-family: var(--font-heading); font-size: 1.1rem; font-weight: 800; color: #18181B; margin: 0 0 0.25rem 0;">Koleksi Lencana Prestasi</h3>
                <p style="font-size: 0.825rem; color: #71717A; margin: 0;">Seluruh lencana pencapaian yang dapat Anda buka melalui kuis.</p>
            </div>

            <?php if (empty($allBadges)): ?>
                <p style="font-size: 0.85rem; color: #71717A; text-align: center; padding: 2rem;">Belum ada lencana yang terdaftar di sistem.</p>
            <?php else: ?>
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); gap: 1rem;">
                    <?php foreach ($allBadges as $b): ?>
                        <?php $isUnlocked = !empty($b['unlocked']); ?>
                        <div style="padding: 1rem; background-color: #FAFAFA; border: 1px solid <?= $isUnlocked ? '#18181B' : '#E5E7EB' ?>; border-radius: 8px; display: flex; flex-direction: column; justify-content: space-between;">
                            <div>
                                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.75rem;">
                                    <div style="width: 36px; height: 36px; border-radius: 8px; background-color: <?= $isUnlocked ? '#18181B' : '#E4E4E7' ?>; color: <?= $isUnlocked ? '#FFFFFF' : '#71717A' ?>; display: flex; align-items: center; justify-content: center;">
                                        <i data-lucide="<?= htmlspecialchars($b['icon'] ?? 'award') ?>" style="width: 18px; height: 18px;"></i>
                                    </div>
                                    <span class="status-badge <?= $isUnlocked ? 'status-active' : '' ?>" style="<?= !$isUnlocked ? 'background-color: #F4F4F5; color: #71717A;' : '' ?> font-size: 0.7rem;">
                                        <?= $isUnlocked ? 'Terbuka' : 'Terkunci' ?>
                                    </span>
                                </div>
                                <h4 style="font-family: var(--font-heading); font-size: 0.925rem; font-weight: 800; color: #18181B; margin: 0 0 0.25rem 0;">
                                    <?= htmlspecialchars($b['title']) ?>
                                </h4>
                                <p style="font-size: 0.775rem; color: #52525B; margin: 0 0 0.75rem 0; line-height: 1.35;">
                                    <?= htmlspecialchars($b['description']) ?>
                                </p>
                            </div>

                            <div>
                                <div style="display: flex; align-items: center; justify-content: space-between; font-size: 0.725rem; color: #71717A; margin-bottom: 0.25rem;" class="font-mono">
                                    <span>Target</span>
                                    <span><?= (int)($b['progress'] ?? 0) ?> / <?= (int)($b['max'] ?? 1) ?></span>
                                </div>
                                <div style="width: 100%; height: 5px; background-color: #E5E7EB; border-radius: 9999px; overflow: hidden;">
                                    <div style="height: 100%; width: <?= min(100, (int)($b['percent'] ?? 0)) ?>%; background-color: #18181B; border-radius: 9999px;"></div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>
    </div>
</div>

<script>
    function switchSettingsTab(targetId, btn) {
        document.querySelectorAll('.settings-card').forEach(c => c.style.display = 'none');
        document.querySelectorAll('.sidebar-nav-item').forEach(b => {
            b.style.backgroundColor = '#FFFFFF';
            b.style.color = '#52525B';
            b.style.borderColor = '#E5E7EB';
        });

        const targetCard = document.getElementById(targetId);
        if (targetCard) {
            targetCard.style.display = 'block';
        }

        if (btn) {
            btn.style.backgroundColor = '#18181B';
            btn.style.color = '#FFFFFF';
            btn.style.borderColor = '#18181B';
        }

        if (window.lucide) window.lucide.createIcons();
    }
</script>

<?php require_once dirname(__DIR__) . '/templates/footer.php'; ?>
