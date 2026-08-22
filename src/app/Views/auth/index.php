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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;600;700&family=Pixelify+Sans:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@500;600;700;800&family=Press+Start+2P&family=Silkscreen:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Chakra+Petch:wght@500;600;700;800&family=DotGothic16&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;600;700;800&family=Pixelify+Sans:wght@500;600;700&family=VT323&display=swap" rel="stylesheet">

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest" defer></script>

    <!-- Top Slim Loading Engine -->
    <script src="<?= function_exists('assetUrl') ? assetUrl('/js/page-loader.js') : (BASE_URL . '/js/page-loader.js') ?>"></script>

    <!-- Custom Auth Stylesheet -->
    <link rel="stylesheet" href="<?= function_exists('assetUrl') ? assetUrl('/css/auth.css') : (BASE_URL . '/css/auth.css') ?>">

    <!-- Global App State -->
    <script>
        window.BASE_URL = "<?= BASE_URL ?>";
        window.INITIAL_MODE = "<?= $mode ?>";
        window.CSRF_TOKEN = "<?= \App\Core\Security::generateCsrfToken() ?>";
    </script>
</head>

<body>
    <!-- 8-Bit Pixel Cyber-Grid & Cute Floating Background Ornaments -->
    <div class="bg-ornament-grid" aria-hidden="true"></div>
    <div class="bg-ornament-major-grid" aria-hidden="true"></div>
    <div class="bg-ornament-ambient" aria-hidden="true"></div>
    <div class="bg-frame-line left-line" aria-hidden="true"></div>
    <div class="bg-frame-line right-line" aria-hidden="true"></div>

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
    <script src="<?= function_exists('assetUrl') ? assetUrl('/js/auth.js') : (BASE_URL . '/js/auth.js') ?>" defer></script>
</body>

</html>