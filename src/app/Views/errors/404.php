<!DOCTYPE html>
<html lang="id" class="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Rute Tidak Ditemukan | NetQuiz</title>

    <!-- Fonts: Inter (UI), JetBrains Mono (Code/Metadata), Press Start 2P (Retro Accents) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:ital,wght@0,400;0,700;1,400&family=Press+Start+2P&display=swap" rel="stylesheet">

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest" defer></script>

    <!-- Stylesheet -->
    <link rel="stylesheet" href="<?= defined('BASE_URL') ? BASE_URL : '' ?>/css/errors.css">
</head>

<body class="error-page-body theme-404">

    <!-- Interactive Background Pixel Critters Canvas -->
    <canvas id="pixelCrittersCanvas" class="error-canvas-bg" aria-hidden="true"></canvas>

    <!-- Retro CRT Scanline Texture Overlay -->
    <div class="scanlines-overlay" aria-hidden="true"></div>

    <!-- Main Vercel Dark Error Panel Card -->
    <div class="error-panel-card">
        <span class="panel-crosshair corner-tl">+</span>
        <span class="panel-crosshair corner-tr">+</span>
        <span class="panel-crosshair corner-bl">+</span>
        <span class="panel-crosshair corner-br">+</span>

        <!-- Terminal Status Badge -->
        <div class="error-status-badge">
            <span class="status-dot-pulse"></span>
            <span>STATUS: ERR_ROUTE_NOT_FOUND</span>
        </div>

        <!-- 8-Bit Glowing Error Number -->
        <div class="error-huge-code">404</div>

        <!-- Title & Subtitle -->
        <h1 class="error-main-heading">
            OH TIDAK! IP Route Tidak Ditemukan
        </h1>
        <p class="error-main-desc">
            Paket data Anda tersesat di routing table NetQuiz. Silahkan kembali yaa!
        </p>

        <!-- CLI Context Box -->
        <div class="error-cli-box">
            <span class="error-cli-prompt">[admin@netquiz] &gt;</span>
            <span class="error-cli-text">/ip route print where dst-address="404" ; # unreachable</span>
        </div>

        <!-- Action Buttons Group -->
        <div class="error-actions-group">
            <a href="<?= defined('BASE_URL') ? BASE_URL : '/' ?>/" class="btn-pixel-primary">
                <i data-lucide="arrow-left" style="width: 14px; height: 14px;"></i>
                <span>Kembali ke Dashboard</span>
            </a>
            <a href="<?= defined('BASE_URL') ? BASE_URL : '/' ?>/quiz" class="btn-pixel-secondary">
                <i data-lucide="zap" style="width: 14px; height: 14px;"></i>
                <span>Kembali ke Kuis</span>
            </a>
        </div>

        <div class="error-footer-hint">
            Tip: Gerakkan kursor kamu untuk berinteraksi dengan karakter pixel di latar belakang.
        </div>
    </div>

    <!-- Scripts -->
    <script src="<?= defined('BASE_URL') ? BASE_URL : '' ?>/js/pixel-critters.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (window.lucide) {
                window.lucide.createIcons();
            }
        });
    </script>
</body>

</html>