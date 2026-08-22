<?php
$title = $title ?? 'Masuk ke NetQuiz';
$mode = $mode ?? 'login';
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title) ?> | NetQuiz</title>
    <meta name="description"
        content="Masuk ke NetQuiz untuk menguji kemampuan RouterOS MikroTik dan persiapan ujian sertifikasi MTCNA.">

    <!-- Fonts: Plus Jakarta Sans, Inter, & JetBrains Mono -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@500;600&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap"
        rel="stylesheet">

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest" defer></script>

    <!-- Custom Auth Stylesheet -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/auth.css?v=<?= time() ?>">

    <!-- Global App State -->
    <script>
        window.BASE_URL = "<?= BASE_URL ?>";
        window.INITIAL_MODE = "<?= $mode ?>";
        window.CSRF_TOKEN = "<?= \App\Core\Security::generateCsrfToken() ?>";
    </script>
</head>

<body>
    <!-- Background Canvas Ornaments (Vercel & Supabase Masterpiece Dual Grid + Structural Framing) -->
    <div class="bg-ornament-grid" aria-hidden="true"></div>
    <div class="bg-ornament-major-grid" aria-hidden="true"></div>
    <div class="bg-ornament-ambient" aria-hidden="true"></div>
    <div class="bg-frame-line left-line" aria-hidden="true"></div>
    <div class="bg-frame-line right-line" aria-hidden="true"></div>

    <!-- Floating Geist Toast Container (Vercel & Sonner Inspired Non-Blocking Toast) -->
    <div id="geist-toaster" class="geist-toaster" aria-live="polite" aria-atomic="true"></div>

    <main class="auth-gateway">
        <div class="gateway-container">
            <!-- Brand Header (Terminal Unified Brand Typography) -->
            <header class="gateway-header">
                <a href="<?= BASE_URL ?>/" class="brand-link" aria-label="NetQuiz Beranda">
                    <div class="brand-mark">
                        <i data-lucide="terminal" class="brand-icon"></i>
                        <span class="live-dot" title="Server Status: Online"></span>
                    </div>
                    <div class="brand-title-group">
                        <span class="brand-title">Net<span class="brand-accent">Quiz</span></span>
                        <span class="brand-cursor font-mono" aria-hidden="true">_</span>
                    </div>
                </a>
            </header>

            <!-- Authentication Card (Precision Crafted Box with Hairline Corner Accents) -->
            <section class="auth-card" aria-labelledby="form-heading">
                <!-- Precision Corner Crosshairs -->
                <div class="card-crosshair corner-tl" aria-hidden="true">+</div>
                <div class="card-crosshair corner-tr" aria-hidden="true">+</div>
                <div class="card-crosshair corner-bl" aria-hidden="true">+</div>
                <div class="card-crosshair corner-br" aria-hidden="true">+</div>

                <div class="card-intro">
                    <div class="card-tag font-mono">AUTH // VERIFIED_GATEWAY</div>
                    <h1 class="form-heading" id="form-heading">Masuk ke Akun</h1>
                    <p class="form-description">Isikan kredensial Anda untuk mengakses platform NetQuiz.</p>
                </div>

                <!-- LOGIN FORM -->
                <form id="login-form" method="POST" novalidate class="auth-form">
                    <input type="hidden" name="csrf_token" value="<?= \App\Core\Security::generateCsrfToken() ?>">

                    <!-- Email Field -->
                    <div class="field-group">
                        <label for="login-email" class="field-label">
                            <span>Alamat Email</span>
                            <span class="field-tag font-mono">IDENTIFIER</span>
                        </label>
                        <input type="email" id="login-email" name="email" class="field-input"
                            placeholder="nama@domain.com" required autocomplete="email">
                        <span class="field-error" id="login-email-error" aria-live="polite"></span>
                    </div>

                    <!-- Password Field -->
                    <div class="field-group">
                        <div class="label-row">
                            <label for="login-password" class="field-label">
                                <span>Password</span>
                                <span class="field-tag font-mono">KEY</span>
                            </label>
                        </div>
                        <div class="password-input-wrapper">
                            <input type="password" id="login-password" name="password" class="field-input"
                                placeholder="Masukkan password" required autocomplete="current-password">
                            <button type="button" class="password-toggle" aria-label="Tampilkan password"
                                data-target="login-password">
                                <i data-lucide="eye" class="toggle-icon-show" aria-hidden="true"></i>
                                <i data-lucide="eye-off" class="toggle-icon-hide hidden" aria-hidden="true"></i>
                            </button>
                        </div>
                        <span class="field-error" id="login-password-error" aria-live="polite"></span>
                    </div>

                    <!-- Remember Me Option -->
                    <div class="options-row">
                        <label class="remember-label">
                            <input type="checkbox" id="login-remember" name="remember" class="remember-checkbox">
                            <span class="checkbox-custom" aria-hidden="true"></span>
                            <span class="checkbox-caption">Ingat sesi di perangkat ini</span>
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" id="btn-login-submit" class="submit-button">
                        <span class="button-text">Masuk ke Platform</span>
                        <i data-lucide="arrow-right" class="button-arrow" aria-hidden="true"></i>
                        <span class="loading-spinner hidden" aria-hidden="true"></span>
                    </button>
                </form>
            </section>
        </div>
    </main>

    <!-- Client Interactivity Script -->
    <script src="<?= BASE_URL ?>/js/auth.js?v=<?= time() ?>" defer></script>
</body>

</html>