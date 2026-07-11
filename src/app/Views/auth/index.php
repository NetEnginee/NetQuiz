<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title) ?> | RouterOS Quiz</title>
    <meta name="description"
        content="Masuk atau daftarkan diri Anda di RouterOS Quiz untuk menguasai konfigurasi MikroTik RouterOS melalui kuis interaktif dan simulasi ujian sertifikasi.">

    <!-- Modern Fonts (Plus Jakarta Sans & Inter) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap"
        rel="stylesheet">

    <!-- Lucide Icons CDN -->
    <script src="https://unpkg.com/lucide@latest" defer></script>

    <!-- Custom Vanilla CSS -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/auth.css?v=<?= time() ?>">
    <style>
        .tab-container {
            display: none !important;
        }

        .card-footer {
            display: none !important;
        }
    </style>

    <!-- Inject Base URL to JS -->
    <script>
        window.BASE_URL = "<?= BASE_URL ?>";
        window.INITIAL_MODE = "<?= $mode ?>";
        window.CSRF_TOKEN = "<?= \App\Core\Security::generateCsrfToken() ?>";
    </script>
</head>

<body>
    <div class="auth-container">
        <!-- Left Column: Branding / Illustration (Visible on Desktop) -->
        <section class="auth-visual-side">
            <!-- Subtle Radial Gradient Background Mesh -->
            <div class="mesh-overlay"></div>

            <div class="visual-content">
                <!-- Brand Logo & Name -->
                <a href="<?= BASE_URL ?>/" class="brand-link">
                    <div class="brand-icon">
                        <i data-lucide="router" class="icon-brand"></i>
                    </div>
                    <span class="brand-text">RouterOS <span class="highlight">Quiz</span></span>
                </a>

                <!-- Catchy Tagline -->
                <div class="hero-text-wrapper">
                    <h1 class="visual-heading">Tes Pemahaman RouterOS mu Disini</h1>
                    <p class="visual-subheading">Akses semua yang dibutuhkan dan persiapkan dirimu untuk
                        sertifikasi MTCNA langsung dalam satu platform.</p>
                </div>

                <!-- Feature List -->
                <ul class="feature-list">
                    <li class="feature-item">
                        <span class="feature-bullet">
                            <i data-lucide="check-circle" class="icon-success"></i>
                        </span>
                        <div>
                            <strong class="feature-title">Simulasi Ujian</strong>
                            <p class="feature-desc">Materi yang diselaraskan dengan silabus ujian sertifikasi MikroTik
                                MTCNA.</p>
                        </div>
                    </li>
                    <li class="feature-item">
                        <span class="feature-bullet">
                            <i data-lucide="check-circle" class="icon-success"></i>
                        </span>
                        <div>
                            <strong class="feature-title">Pelatihan Pemahaman</strong>
                            <p class="feature-desc">Dapatkan penjelasan mendalam dan contoh kasus untuk setiap konsep
                                RouterOS.</p>
                        </div>
                    </li>
                    <li class="feature-item">
                        <span class="feature-bullet">
                            <i data-lucide="check-circle" class="icon-success"></i>
                        </span>
                        <div>
                            <strong class="feature-title">Pelacakan Progres</strong>
                            <p class="feature-desc">Analisis performa topik seperti Routing, Firewall, NAT,
                                Wireless dan Lainnya.</p>
                        </div>
                    </li>
                </ul>


            </div>


        </section>

        <!-- Right Column: Interactive Authentication Forms -->
        <main class="auth-form-side">
            <div class="form-wrapper">
                <!-- Mobile Header Brand (Visible only on mobile/tablet) -->
                <div class="mobile-brand">
                    <a href="<?= BASE_URL ?>/" class="brand-link-mobile">
                        <i data-lucide="router" class="icon-brand-mobile"></i>
                        <span>RouterOS Quiz</span>
                    </a>
                </div>



                <!-- Form Card -->
                <div class="auth-card">
                    <!-- Heading -->
                    <div class="card-header">
                        <h2 class="form-title" id="auth-title">Selamat Datang Kembali</h2>
                        <p class="form-subtitle" id="auth-subtitle">Masukkan email dan password Anda untuk masuk ke
                            dashboard quiz.</p>
                    </div>

                    <!-- Alert Banner (General Errors / System Messages) -->
                    <div id="general-alert" class="alert-banner hidden" role="alert">
                        <i data-lucide="alert-circle" class="alert-icon"></i>
                        <span id="general-alert-text"></span>
                    </div>

                    <!-- LOGIN FORM -->
                    <form id="login-form" method="POST" novalidate class="auth-form active">
                        <!-- Email Input Group -->
                        <div class="input-group">
                            <label for="login-email" class="input-label">Alamat Email</label>
                            <div class="input-wrapper">
                                <span class="input-icon-left">
                                    <i data-lucide="mail"></i>
                                </span>
                                <input type="email" id="login-email" name="email" class="form-input"
                                    placeholder="nama@email.com" required autocomplete="email">
                            </div>
                            <span class="error-msg" id="login-email-error"></span>
                        </div>

                        <!-- Password Input Group -->
                        <div class="input-group">
                            <div class="label-wrapper">
                                <label for="login-password" class="input-label">Password</label>
                            </div>
                            <div class="input-wrapper">
                                <span class="input-icon-left">
                                    <i data-lucide="lock"></i>
                                </span>
                                <input type="password" id="login-password" name="password" class="form-input"
                                    placeholder="Minimal 8 karakter" required autocomplete="current-password">
                                <button type="button" class="password-toggle" aria-label="Tampilkan password"
                                    data-target="login-password">
                                    <i data-lucide="eye" class="toggle-icon-show"></i>
                                    <i data-lucide="eye-off" class="toggle-icon-hide hidden"></i>
                                </button>
                            </div>
                            <span class="error-msg" id="login-password-error"></span>
                        </div>

                        <!-- Keep Signed In & Remember Me -->
                        <div class="options-wrapper">
                            <label class="checkbox-container">
                                <input type="checkbox" id="login-remember" name="remember">
                                <span class="checkmark"></span>
                                <span class="checkbox-label">Ingat saya di perangkat ini</span>
                            </label>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" id="btn-login-submit" class="btn-primary">
                            <span class="btn-text">Masuk ke Dashboard</span>
                            <span class="loading-spinner hidden"></span>
                        </button>
                    </form>


                </div>
            </div>
        </main>
    </div>

    <!-- Custom Client-side Controller (Interactive validations and toggle animations) -->
    <script src="<?= BASE_URL ?>/js/auth.js?v=<?= time() ?>" defer></script>
</body>

</html>