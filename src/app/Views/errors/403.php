<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akses Ditolak | 403 Forbidden</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        :root {
            --primary: #ef4444;
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

        /* Animated background glow */
        .glow-bg {
            position: absolute;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(239, 68, 68, 0.06) 0%, rgba(248, 250, 252, 0) 75%);
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
            font-size: 9rem;
            font-weight: 800;
            line-height: 1;
            background: linear-gradient(135deg, #ef4444 0%, #b91c1c 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 1.5rem;
            letter-spacing: -0.04em;
            display: inline-block;
            animation: float 5s infinite ease-in-out;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-8px); }
        }

        h1 {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 1.75rem;
            font-weight: 800;
            color: var(--text);
            margin-bottom: 0.75rem;
            letter-spacing: -0.03em;
        }

        p {
            color: var(--text-muted);
            font-size: 0.95rem;
            line-height: 1.6;
            margin-bottom: 2.25rem;
            padding: 0 1.5rem;
        }

        .btn-home {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background-color: #7c3aed;
            color: #ffffff;
            text-decoration: none;
            padding: 0.85rem 2rem;
            border-radius: 16px;
            font-weight: 600;
            font-size: 0.9rem;
            box-shadow: 0 10px 25px -5px rgba(124, 58, 237, 0.25);
            transition: all 0.2s ease;
            border: none;
            cursor: pointer;
        }

        .btn-home:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 30px -5px rgba(124, 58, 237, 0.35);
            background-color: #6d28d9;
        }

        .btn-home:active {
            transform: translateY(0);
        }
    </style>
</head>
<body>
    <div class="glow-bg"></div>
    <div class="container">
        <div class="error-code">403</div>
        <h1>Akses Ditolak</h1>
        <p>
            Maaf, Anda tidak memiliki izin untuk mengakses halaman ini. Silakan kembali ke dashboard atau hubungi administrator Anda.
        </p>
        <div style="display: flex; gap: 1rem; justify-content: center; margin-top: 1.5rem; flex-wrap: wrap;">
            <a href="<?= BASE_URL ?>/" class="btn-home" style="background-color: #7c3aed; box-shadow: 0 10px 25px -5px rgba(124, 58, 237, 0.25);">
                <i data-lucide="arrow-left" style="width: 1.1rem; height: 1.1rem;"></i>
                Kembali ke Dashboard
            </a>
            <a href="<?= BASE_URL ?>/logout" class="btn-home" style="background-color: #ef4444; box-shadow: 0 10px 25px -5px rgba(239, 68, 68, 0.25);">
                <i data-lucide="log-out" style="width: 1.1rem; height: 1.1rem;"></i>
                Logout & Ganti Akun
            </a>
        </div>
    </div>

    <script>
        // Initialize Lucide Icons
        if (window.lucide) {
            window.lucide.createIcons();
        }
    </script>
</body>
</html>
