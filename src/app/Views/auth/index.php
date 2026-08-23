<?php
$title = $title ?? 'Masuk ke NetQuiz';
$mode = $mode ?? 'login';
?>
<!DOCTYPE html>
<html lang="id" class="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title) ?> | NetQuiz</title>
    <meta name="description"
        content="Masuk ke NetQuiz_ Gateway untuk menguji kemampuan RouterOS MikroTik, sertifikasi MTCNA, dan eksplorasi materi jaringan.">

    <!-- Fonts: Inter (UI), JetBrains Mono (Code/Metadata), Press Start 2P (Retro Accents) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=JetBrains+Mono:ital,wght@0,400;0,500;0,600;0,700;1,400&family=Press+Start+2P&display=swap" rel="stylesheet">

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest" defer></script>

    <!-- Top Slim Loading Engine -->
    <script src="<?= function_exists('assetUrl') ? assetUrl('/js/page-loader.js') : (BASE_URL . '/js/page-loader.js') ?>"></script>

    <!-- Custom Vercel Dark & Pixel Auth Stylesheet -->
    <link rel="stylesheet" href="<?= function_exists('assetUrl') ? assetUrl('/css/auth.css') : (BASE_URL . '/css/auth.css') ?>">

    <!-- Global App State -->
    <script>
        window.BASE_URL = "<?= BASE_URL ?>";
        window.INITIAL_MODE = "<?= $mode ?>";
        window.CSRF_TOKEN = "<?= \App\Core\Security::generateCsrfToken() ?>";
    </script>
</head>

<body class="grid-bg">
    <!-- Network Data Stream Animation Canvas Background -->
    <canvas id="networkCanvas" class="network-canvas" aria-hidden="true"></canvas>

    <!-- Inline SVG Pixel Art Sprite Sheet -->
    <svg class="svg-sprite-sheet" aria-hidden="true" style="display: none;">
        <!-- Pixel Router -->
        <g id="pixel-router">
            <path fill="#0070F3" d="M2,8 h12 v6 h-12 z" />
            <path fill="#50E3C2" d="M4,2 h1 v6 h-1 z M11,2 h1 v6 h-1 z" />
            <path fill="#7928CA" d="M3,9 h2 v2 h-2 z M7,9 h2 v2 h-2 z M11,9 h2 v2 h-2 z" />
            <path fill="#00FF66" d="M3,12 h1 v1 h-1 z M5,12 h1 v1 h-1 z M7,12 h1 v1 h-1 z M9,12 h1 v1 h-1 z" />
            <path fill="#111" d="M1,14 h14 v1 h-14 z" />
        </g>
    </svg>

    <!-- Floating Geist Toast Notification Container -->
    <div id="geist-toaster" class="geist-toaster" aria-live="polite" aria-atomic="true"></div>

    <!-- Main Authentication Gateway Layout -->
    <div class="auth-layout">
        <!-- Center Gateway Section -->
        <main class="auth-main">
            <div class="auth-container">
                <!-- Komponen Brand & Control di Atas (Luar Card Form) -->
                <div class="auth-top-cluster">
                    <a href="<?= BASE_URL ?>/" class="cluster-brand" aria-label="NetQuiz Beranda">
                        <div class="brand-badge-box">
                            <svg class="pixel-brand-svg pixelated" viewBox="0 0 16 16">
                                <use href="#pixel-router"></use>
                            </svg>
                            <span class="status-ping" title="Sistem Aktif"></span>
                            <span class="status-dot"></span>
                        </div>
                        <div class="brand-text-block">
                            <div class="brand-sub-label font-mono">ROUTEROS ACADEMY //</div>
                            <div class="brand-name">
                                <span>NetQuiz</span><span class="brand-cursor">_</span>
                            </div>
                        </div>
                    </a>

                    <div class="cluster-controls">
                        <button type="button" id="soundToggleBtn" class="sound-toggle-btn font-mono" aria-label="Aktifkan/Matikan Suara">
                            <span id="soundIcon">🔊</span>
                            <span id="soundLabel">Sound: ON</span>
                        </button>
                        <div class="system-status-pill font-mono">
                            <span>Secure</span>
                        </div>
                    </div>
                </div>

                <!-- Precision Auth Card -->
                <section class="auth-card" aria-labelledby="auth-card-title">
                    <!-- Corner Precision Crosshairs -->
                    <div class="card-crosshair corner-tl" aria-hidden="true">+</div>
                    <div class="card-crosshair corner-tr" aria-hidden="true">+</div>
                    <div class="card-crosshair corner-bl" aria-hidden="true">+</div>
                    <div class="card-crosshair corner-br" aria-hidden="true">+</div>

                    <!-- Meta Tag & Title -->
                    <div class="card-intro-block">
                        <div class="card-tag font-mono">
                            <span class="tag-icon">⚡</span>
                            <span>AUTH // SYSTEM_GATEWAY</span>
                            <span class="tag-badge font-pixel">MTCNA</span>
                        </div>
                        <h1 id="auth-card-title" class="card-title">Masuk ke Platform</h1>
                        <p class="card-subtitle">
                            Masukkan kredensial Anda untuk mengakses evaluasi kompetensi & materi RouterOS.
                        </p>
                    </div>

                    <!-- Login Form -->
                    <form id="login-form" method="POST" novalidate class="auth-form" autocomplete="on">
                        <input type="hidden" name="csrf_token" value="<?= \App\Core\Security::generateCsrfToken() ?>">

                        <!-- Email / Username Field -->
                        <div class="field-wrapper">
                            <div class="field-header">
                                <label for="login-email" class="field-label">Alamat Email / Username</label>
                                <span class="field-hint font-mono">[IDENTIFIER]</span>
                            </div>
                            <div class="input-container">
                                <span class="input-icon-left" aria-hidden="true">
                                    <i data-lucide="user"></i>
                                </span>
                                <input type="text" id="login-email" name="email" class="form-input with-left-icon font-mono"
                                    placeholder="operator@routerosquiz.academy atau username" required autocomplete="username" spellcheck="false">
                            </div>
                            <span class="field-error-text font-mono" id="login-email-error" aria-live="polite"></span>
                        </div>

                        <!-- Password Field -->
                        <div class="field-wrapper">
                            <div class="field-header">
                                <label for="login-password" class="field-label">Kata Sandi</label>
                                <span class="field-hint font-mono">[KEY]</span>
                            </div>
                            <div class="input-container">
                                <span class="input-icon-left" aria-hidden="true">
                                    <i data-lucide="lock"></i>
                                </span>
                                <input type="password" id="login-password" name="password" class="form-input with-left-icon with-right-toggle font-mono"
                                    placeholder="••••••••••••" required autocomplete="current-password" spellcheck="false">
                                <button type="button" class="password-toggle-btn" aria-label="Tampilkan kata sandi" data-target="login-password">
                                    <i data-lucide="eye" class="icon-show" aria-hidden="true"></i>
                                    <i data-lucide="eye-off" class="icon-hide hidden" aria-hidden="true"></i>
                                </button>
                            </div>
                            <span class="field-error-text font-mono" id="login-password-error" aria-live="polite"></span>
                        </div>

                        <!-- Form Options (Remember Me) -->
                        <div class="form-options-row">
                            <label class="custom-checkbox-label">
                                <input type="checkbox" id="login-remember" name="remember" class="custom-checkbox-input">
                                <span class="checkbox-box" aria-hidden="true">
                                    <i data-lucide="check" class="checkbox-check-icon" aria-hidden="true"></i>
                                </span>
                                <span class="checkbox-text font-mono">Ingat sesi di perangkat ini</span>
                            </label>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" id="btn-login-submit" class="auth-submit-btn font-mono">
                            <span class="btn-content">
                                <span class="btn-label">Masuk ke Platform</span>
                                <i data-lucide="arrow-right" class="btn-arrow" aria-hidden="true"></i>
                            </span>
                            <span class="btn-spinner hidden" aria-hidden="true"></span>
                        </button>
                    </form>

                    <!-- Card Footer Micro-Note -->
                    <div class="card-footer-note">
                        <span class="note-text font-mono">
                            Setiap sesi dilindungi dengan enkripsi AES-256.
                        </span>
                    </div>
                </section>
            </div>
        </main>
    </div>

    <!-- Client Interactivity Script -->
    <script src="<?= function_exists('assetUrl') ? assetUrl('/js/auth.js') : (BASE_URL . '/js/auth.js') ?>" defer></script>
</body>

</html>