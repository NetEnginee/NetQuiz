<?php
declare(strict_types=1);

/**
 * RouterOS Quiz Academy - InfinityFree Shared Hosting Diagnostic Tool
 * Author: Senior DevOps & PHP Developer
 * Usage: Upload this file directly to your htdocs/ directory and visit http://yourdomain/diagnose.php in your browser.
 */

// Basic styling for a beautiful, premium diagnostic dashboard
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Diagnostics & Fix Tool | RouterOS Quiz</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #0f172a;
            color: #e2e8f0;
            margin: 0;
            padding: 2rem;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            box-sizing: border-box;
        }
        .container {
            width: 100%;
            max-width: 700px;
            background: rgba(30, 41, 59, 0.7);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 24px;
            padding: 2.5rem;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.3);
        }
        h1 {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 1.8rem;
            font-weight: 800;
            margin-top: 0;
            background: linear-gradient(135deg, #38bdf8 0%, #818cf8 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        .subtitle {
            color: #94a3b8;
            font-size: 0.95rem;
            margin-bottom: 2rem;
            line-height: 1.5;
        }
        .check-item {
            background: rgba(15, 23, 42, 0.4);
            border-radius: 16px;
            padding: 1.25rem;
            margin-bottom: 1rem;
            border: 1px solid rgba(255, 255, 255, 0.05);
            display: flex;
            align-items: flex-start;
            gap: 1rem;
        }
        .icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            flex-shrink: 0;
        }
        .icon-success {
            background: rgba(34, 197, 94, 0.15);
            color: #22c55e;
        }
        .icon-warning {
            background: rgba(245, 158, 11, 0.15);
            color: #f59e0b;
        }
        .icon-error {
            background: rgba(239, 68, 68, 0.15);
            color: #ef4444;
        }
        .check-details {
            flex-grow: 1;
        }
        .check-title {
            font-weight: 600;
            font-size: 1rem;
            margin-bottom: 0.25rem;
            color: #f8fafc;
        }
        .check-desc {
            font-size: 0.85rem;
            color: #94a3b8;
            line-height: 1.5;
        }
        .solution-box {
            margin-top: 0.75rem;
            padding: 0.75rem;
            background: rgba(239, 68, 68, 0.1);
            border-left: 3px solid #ef4444;
            border-radius: 4px;
            font-size: 0.85rem;
            color: #fca5a5;
        }
        .solution-box-warning {
            margin-top: 0.75rem;
            padding: 0.75rem;
            background: rgba(245, 158, 11, 0.1);
            border-left: 3px solid #f59e0b;
            border-radius: 4px;
            font-size: 0.85rem;
            color: #fde047;
        }
        .code-block {
            font-family: monospace;
            background: #090d16;
            padding: 0.5rem;
            border-radius: 6px;
            display: block;
            margin-top: 0.5rem;
            color: #38bdf8;
            overflow-x: auto;
        }
        .footer {
            margin-top: 2.5rem;
            text-align: center;
            font-size: 0.8rem;
            color: #64748b;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1><i data-lucide="shield-alert"></i> InfinityFree Diagnostics</h1>
        <p class="subtitle">Alat bantu otomatis untuk menganalisis dan mendiagnosis penyebab Error 403 Forbidden di shared hosting Anda.</p>

        <!-- CHECK 1: Directory Placement -->
        <?php
        $currentDir = __DIR__;
        $parentDir = dirname($currentDir);
        $isHtdocs = (basename($currentDir) === 'htdocs' || strpos($currentDir, 'htdocs') !== false);
        
        if ($isHtdocs) {
            $statusClass = 'icon-success';
            $icon = 'check-circle-2';
            $title = 'Struktur Folder Publik (htdocs) OK';
            $desc = 'File ini berada di dalam direktori <code>htdocs</code>. Ini adalah document root yang benar untuk melayani website.';
            $solution = null;
        } else {
            $statusClass = 'icon-error';
            $icon = 'alert-triangle';
            $title = 'Lokasi File Salah!';
            $desc = 'File ini berada di <code>' . htmlspecialchars($currentDir) . '</code> yang bukan atau di luar folder <code>htdocs</code>.';
            $solution = '<strong>Solusi:</strong> Pindahkan semua isi dari folder <code>public/</code> (termasuk index.php, .htaccess, css, js) secara langsung ke dalam folder <strong>htdocs</strong> melalui FTP client Anda.';
        }
        ?>
        <div class="check-item">
            <div class="icon <?=$statusClass?>"><i data-lucide="<?=$icon?>"></i></div>
            <div class="check-details">
                <div class="check-title"><?=$title?></div>
                <div class="check-desc"><?=$desc?></div>
                <?php if ($solution): ?>
                    <div class="solution-box"><?=$solution?></div>
                <?php endif; ?>
            </div>
        </div>

        <!-- CHECK 2: Index File Name and Location -->
        <?php
        $indexExists = file_exists($currentDir . '/index.php');
        $indexCaseCorrect = true;
        
        if ($indexExists) {
            // Linux is case-sensitive, check if it's exact lowercase "index.php"
            $files = scandir($currentDir);
            $indexCaseCorrect = in_array('index.php', $files, true);
        }

        if ($indexExists && $indexCaseCorrect) {
            $statusClass = 'icon-success';
            $icon = 'check-circle-2';
            $title = 'File index.php Ditemukan';
            $desc = 'File <code>index.php</code> (lowercase) terdeteksi di lokasi yang benar.';
            $solution = null;
        } elseif ($indexExists && !$indexCaseCorrect) {
            $statusClass = 'icon-error';
            $icon = 'alert-triangle';
            $title = 'Sensitivitas Huruf Besar/Kecil Salah (Case Sensitivity)';
            $desc = 'File index terdeteksi tetapi namanya tidak sepenuhnya lowercase (contoh: <code>Index.php</code> atau <code>INDEX.PHP</code>). Linux server shared hosting sangat sensitif terhadap hal ini.';
            $solution = '<strong>Solusi:</strong> Ubah nama file index tersebut di FTP Anda menjadi huruf kecil semua: <code>index.php</code>.';
        } else {
            $statusClass = 'icon-error';
            $icon = 'alert-triangle';
            $title = 'File index.php Tidak Ditemukan!';
            $desc = 'Tidak ditemukan file <code>index.php</code> di direktori saat ini.';
            $solution = '<strong>Solusi:</strong> Pastikan Anda telah mengunggah file <code>index.php</code> (yang sudah dimodifikasi) langsung ke dalam folder <code>htdocs/</code>.';
        }
        ?>
        <div class="check-item">
            <div class="icon <?=$statusClass?>"><i data-lucide="<?=$icon?>"></i></div>
            <div class="check-details">
                <div class="check-title"><?=$title?></div>
                <div class="check-desc"><?=$desc?></div>
                <?php if ($solution): ?>
                    <div class="solution-box"><?=$solution?></div>
                <?php endif; ?>
            </div>
        </div>

        <!-- CHECK 3: .htaccess check for Options -Indexes -->
        <?php
        $htaccessPath = $currentDir . '/.htaccess';
        $htaccessExists = file_exists($htaccessPath);
        $hasOptionsIndexes = false;
        $hasRequireAllDenied = false;

        if ($htaccessExists) {
            $content = file_get_contents($htaccessPath);
            if (preg_match('/^\s*Options\s+-Indexes/mi', $content)) {
                $hasOptionsIndexes = true;
            }
            if (preg_match('/^\s*Require\s+all\s+denied/mi', $content)) {
                $hasRequireAllDenied = true;
            }
        }

        if (!$htaccessExists) {
            $statusClass = 'icon-warning';
            $icon = 'help-circle';
            $title = 'File .htaccess Tidak Ditemukan';
            $desc = 'File <code>.htaccess</code> tidak terdeteksi di folder root. Routing halaman kuis tidak akan berfungsi dengan baik tanpa file ini.';
            $solution = '<strong>Solusi:</strong> Buat file <code>.htaccess</code> baru di folder <code>htdocs/</code> berisi aturan mod_rewrite yang kami sediakan.';
            $solutionType = 'warning';
        } elseif ($hasRequireAllDenied) {
            $statusClass = 'icon-error';
            $icon = 'alert-triangle';
            $title = 'Aturan Pemblokiran Global Ditemukan!';
            $desc = 'File <code>.htaccess</code> di folder ini memiliki perintah <code>Require all denied</code>. Ini memblokir seluruh website Anda!';
            $solution = '<strong>Solusi:</strong> Hapus baris <code>Require all denied</code> dari <code>htdocs/.htaccess</code>. Baris tersebut hanya boleh digunakan di dalam subfolder rahasia seperti <code>app/</code>, <code>config/</code>, dan <code>logs/</code>.';
            $solutionType = 'error';
        } elseif ($hasOptionsIndexes) {
            $statusClass = 'icon-warning';
            $icon = 'info';
            $title = 'Direktif Options -Indexes Aktif';
            $desc = 'File <code>.htaccess</code> memiliki aturan <code>Options -Indexes</code>. Di beberapa konfigurasi InfinityFree, ini dapat memicu error 403 atau 500 karena server melarang override.';
            $solution = '<strong>Saran:</strong> Jika website Anda masih menampilkan 403 Forbidden, coba edit file <code>.htaccess</code> Anda dan berikan tanda komentar pagar di depan baris tersebut: <code># Options -Indexes</code>.';
            $solutionType = 'warning';
        } else {
            $statusClass = 'icon-success';
            $icon = 'check-circle-2';
            $title = 'File .htaccess OK';
            $desc = 'File <code>.htaccess</code> ditemukan dan tidak memiliki aturan yang memblokir akses publik secara berbahaya.';
            $solution = null;
        }
        ?>
        <div class="check-item">
            <div class="icon <?=$statusClass?>"><i data-lucide="<?=$icon?>"></i></div>
            <div class="check-details">
                <div class="check-title"><?=$title?></div>
                <div class="check-desc"><?=$desc?></div>
                <?php if ($solution): ?>
                    <div class="solution-box-<?=($solutionType === 'error' ? 'error' : 'warning')?>"><?=$solution?></div>
                <?php endif; ?>
            </div>
        </div>

        <!-- CHECK 4: Core Directories Pathing Check -->
        <?php
        // Look for app/ and config/ folders in parent or same folder
        $appInParent = is_dir($parentDir . '/app');
        $configInParent = is_dir($parentDir . '/config');
        $appInSame = is_dir($currentDir . '/app');
        $configInSame = is_dir($currentDir . '/config');

        if ($appInParent && $configInParent) {
            $statusClass = 'icon-success';
            $icon = 'check-circle-2';
            $title = 'Struktur Backend Aman (Luar htdocs)';
            $desc = 'Folder <code>app/</code> dan <code>config/</code> berada di luar <code>htdocs</code>. Ini konfigurasi yang sangat aman.';
            $solution = null;
        } elseif ($appInSame && $configInSame) {
            $statusClass = 'icon-warning';
            $icon = 'info';
            $title = 'Struktur Backend Terbuka (Dalam htdocs)';
            $desc = 'Folder <code>app/</code> dan <code>config/</code> berada di dalam folder publik. Ini fungsional tetapi kurang aman.';
            $solution = '<strong>Saran:</strong> Pastikan Anda telah membuat file <code>.htaccess</code> berisi <code>Require all denied</code> di dalam folder <code>app/</code>, <code>config/</code>, dan <code>logs/</code> agar file PHP backend tidak dapat dibaca dari luar.';
            $solutionType = 'warning';
        } else {
            $statusClass = 'icon-error';
            $icon = 'alert-triangle';
            $title = 'Struktur Folder Core Hilang!';
            $desc = 'Tidak dapat menemukan folder <code>app/</code> atau <code>config/</code> baik sejajar dengan <code>htdocs</code> maupun di dalam folder saat ini.';
            $solution = '<strong>Solusi:</strong> Pastikan Anda telah mengunggah folder <code>app/</code>, <code>config/</code>, dan <code>logs/</code>. Jika diletakkan sejajar dengan <code>htdocs</code>, strukturnya harus benar.';
            $solutionType = 'error';
        }
        ?>
        <div class="check-item">
            <div class="icon <?=$statusClass?>"><i data-lucide="<?=$icon?>"></i></div>
            <div class="check-details">
                <div class="check-title"><?=$title?></div>
                <div class="check-desc"><?=$desc?></div>
                <?php if ($solution): ?>
                    <div class="solution-box-<?=($solutionType === 'error' ? 'error' : 'warning')?>"><?=$solution?></div>
                <?php endif; ?>
            </div>
        </div>

        <div class="footer">
            RouterOS Quiz Academy Tool &bull; Silakan hapus file <code>diagnose.php</code> ini setelah masalah terselesaikan demi keamanan.
        </div>
    </div>

    <script>
        // Initialize Lucide Icons
        if (window.lucide) {
            lucide.createIcons();
        }
    </script>
</body>
</html>
