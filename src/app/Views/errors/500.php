<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? '500 - Kesalahan Server Internal') ?> | NetQuiz</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest" defer></script>
    <style>
        :root {
            --primary: #f43f5e;
            --bg: #f8fafc;
            --text: #0f172a;
            --text-muted: #64748b;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg);
            color: var(--text);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
        }

        .glow-bg {
            position: absolute;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(244, 63, 94, 0.06) 0%, rgba(248, 250, 252, 0) 75%);
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 1;
            pointer-events: none;
        }

        .container {
            position: relative;
            z-index: 2;
            text-align: center;
            padding: 2rem;
            width: 100%;
            max-width: 480px;
        }

        .error-code {
            font-family: 'Outfit', sans-serif;
            font-size: 6.5rem;
            font-weight: 800;
            line-height: 1;
            letter-spacing: -0.04em;
            color: #18181B;
            margin-bottom: 0.5rem;
        }

        .error-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 0.75rem;
        }

        .error-desc {
            font-size: 0.95rem;
            color: var(--text-muted);
            line-height: 1.6;
            margin-bottom: 2rem;
        }

        .btn-home {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background-color: #18181B;
            color: #ffffff;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 0.9rem;
            font-weight: 600;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            text-decoration: none;
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
            border: 1px solid #18181B;
        }

        .btn-home:hover {
            background-color: #27272A;
            transform: translateY(-2px);
            box-shadow: 0 10px 20px -5px rgba(24, 24, 27, 0.2);
        }

        .icon-box {
            width: 56px;
            height: 56px;
            background: #fff1f2;
            border: 1px solid #fecaca;
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.5rem;
            color: var(--primary);
        }
    </style>
</head>
<body>
    <div class="glow-bg" aria-hidden="true"></div>

    <div class="container">
        <div class="icon-box">
            <i data-lucide="server-crash" style="width: 28px; height: 28px;"></i>
        </div>
        <div class="error-code">500</div>
        <h1 class="error-title">Kesalahan Server Internal</h1>
        <p class="error-desc">
            Terjadi gangguan teknis yang tidak terduga pada server. Tim administrator telah menerima laporan log masalah ini.
        </p>
        <a href="<?= defined('BASE_URL') ? BASE_URL : '/' ?>" class="btn-home">
            <i data-lucide="arrow-left" style="width: 16px; height: 16px;"></i>
            <span>Kembali ke Halaman Utama</span>
        </a>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (window.lucide) {
                window.lucide.createIcons();
            }
        });
    </script>
</body>
</html>
