<?php

declare(strict_types=1);

/**
 * NetQuiz - Standalone Admin User Credential Management Utility
 * 
 * Penggunaan:
 * 1. Melalui Browser: Buka http://namadomainanda/reset_admin.php (atau http://localhost:8080/reset_admin.php)
 * 2. Melalui Terminal / CLI: php reset_admin.php [username] [email] [password]
 */

// Load project configuration
$configFile = dirname(__DIR__) . '/config/config.php';
if (!file_exists($configFile)) {
    $configFile = __DIR__ . '/config/config.php';
}

$config = file_exists($configFile) ? require $configFile : [];

// Default DB values from config
$dbHost = $config['db_host'] ?? 'sql301.infinityfree.com';
$dbName = $config['db_name'] ?? 'if0_42727530_netquiz';
$dbUser = $config['db_user'] ?? 'if0_42727530';
$dbPass = $config['db_pass'] ?? '1UnionMzCADHseR';
$dbPort = (int)($config['db_port'] ?? 3306);

// Default Admin values
$defaultUsername = 'admin';
$defaultEmail = 'super@netquiz.academy';
$defaultPassword = 'superuser123_';

$message = null;
$messageType = null;
$pdo = null;

// Allow POST to override DB parameters if provided
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['custom_db_host'])) {
    $dbHost = trim((string)$_POST['custom_db_host']);
    $dbName = trim((string)($_POST['custom_db_name'] ?? $dbName));
    $dbUser = trim((string)($_POST['custom_db_user'] ?? $dbUser));
    $dbPass = (string)($_POST['custom_db_pass'] ?? $dbPass);
}

// Attempt Database Connection
$dbError = null;
try {
    $dsn = "mysql:host={$dbHost};port={$dbPort};dbname={$dbName};charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::ATTR_TIMEOUT => 5,
    ];
    $pdo = new PDO($dsn, $dbUser, $dbPass, $options);
} catch (\Throwable $e) {
    $dbError = $e->getMessage();
}

// CLI Execution Mode
$isCli = (PHP_SAPI === 'cli');
if ($isCli) {
    $adminUsername = $argv[1] ?? $defaultUsername;
    $adminEmail = $argv[2] ?? $defaultEmail;
    $adminPassword = $argv[3] ?? $defaultPassword;

    echo "\n=======================================================\n";
    echo "  NETQUIZ - ADMIN CREDENTIAL SETUP (CLI)\n";
    echo "=======================================================\n";
    echo "DB Host  : {$dbHost}\n";
    echo "DB Name  : {$dbName}\n";
    echo "DB User  : {$dbUser}\n";
    echo "-------------------------------------------------------\n";

    if ($pdo === null) {
        echo "[FATAL ERROR] Koneksi Database Gagal:\n" . $dbError . "\n";
        echo "=======================================================\n\n";
        exit(1);
    }

    $res = executeReset($pdo, $adminUsername, $adminEmail, $adminPassword);
    if ($res['success']) {
        echo "[SUCCESS] " . $res['message'] . "\n";
        echo "Username : " . $adminUsername . "\n";
        echo "Email    : " . $adminEmail . "\n";
        echo "Password : " . $adminPassword . "\n";
        echo "Hash     : " . $res['hash'] . "\n";
    } else {
        echo "[ERROR] " . $res['message'] . "\n";
    }
    echo "=======================================================\n\n";
    exit($res['success'] ? 0 : 1);
}

// Handle Form Submission via Web Browser
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $adminUsername = trim((string)($_POST['username'] ?? $defaultUsername));
    $adminEmail = trim((string)($_POST['email'] ?? $defaultEmail));
    $adminPassword = (string)($_POST['password'] ?? $defaultPassword);

    if ($pdo === null) {
        $message = "Koneksi database gagal. Password atau username database hosting Anda salah: " . $dbError;
        $messageType = "error";
    } elseif (empty($adminUsername) || empty($adminEmail) || empty($adminPassword)) {
        $message = "Semua kolom kredensial admin wajib diisi!";
        $messageType = "error";
    } elseif (!filter_var($adminEmail, FILTER_VALIDATE_EMAIL)) {
        $message = "Format alamat email tidak valid!";
        $messageType = "error";
    } else {
        $res = executeReset($pdo, $adminUsername, $adminEmail, $adminPassword);
        $message = $res['message'];
        $messageType = $res['success'] ? 'success' : 'error';
    }
}

/**
 * Execute User Admin Upsert into Database
 */
function executeReset(PDO $pdo, string $username, string $email, string $password): array
{
    try {
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);

        $stmt = $pdo->prepare("SELECT id, username, email FROM users WHERE LOWER(TRIM(email)) = LOWER(TRIM(:email)) LIMIT 1");
        $stmt->execute(['email' => $email]);
        $existingUser = $stmt->fetch();

        if ($existingUser) {
            $updateStmt = $pdo->prepare("
                UPDATE users 
                SET username = :username, 
                    password = :password,
                    status = 'Aktif',
                    updated_at = NOW() 
                WHERE id = :id
            ");
            $updateStmt->execute([
                'username' => $username,
                'password' => $hashedPassword,
                'id' => $existingUser['id']
            ]);

            return [
                'success' => true,
                'message' => "Akun admin berhasil diperbarui (ID #{$existingUser['id']})!",
                'hash' => $hashedPassword
            ];
        } else {
            $insertStmt = $pdo->prepare("
                INSERT INTO users (username, email, password, status, created_at, updated_at) 
                VALUES (:username, :email, :password, 'Aktif', NOW(), NOW())
            ");
            $insertStmt->execute([
                'username' => $username,
                'email' => $email,
                'password' => $hashedPassword
            ]);
            $newId = $pdo->lastInsertId();

            return [
                'success' => true,
                'message' => "Akun admin baru berhasil dibuat (ID #{$newId})!",
                'hash' => $hashedPassword
            ];
        }
    } catch (\Throwable $e) {
        return [
            'success' => false,
            'message' => "Gagal mengeksekusi ke database: " . $e->getMessage(),
            'hash' => ''
        ];
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Kredensial Admin | NetQuiz Gateway</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #09090b;
            --card-bg: rgba(18, 18, 23, 0.92);
            --border: #27272a;
            --primary: #0070F3;
            --primary-hover: #3291ff;
            --accent: #50E3C2;
            --text-main: #f4f4f5;
            --text-muted: #a1a1aa;
            --success: #10b981;
            --error: #ef4444;
            --warning: #f59e0b;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            background-color: var(--bg);
            color: var(--text-main);
            font-family: 'Inter', -apple-system, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            background-image: 
                linear-gradient(to right, rgba(255,255,255,0.03) 1px, transparent 1px),
                linear-gradient(to bottom, rgba(255,255,255,0.03) 1px, transparent 1px);
            background-size: 32px 32px;
        }

        .container {
            width: 100%;
            max-width: 560px;
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 32px;
            box-shadow: 0 20px 40px -15px rgba(0,0,0,0.7), 0 0 30px rgba(0, 112, 243, 0.1);
            backdrop-filter: blur(12px);
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .badge-mono {
            display: inline-block;
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.7rem;
            color: var(--accent);
            background: rgba(80, 227, 194, 0.1);
            border: 1px solid rgba(80, 227, 194, 0.25);
            padding: 4px 10px;
            border-radius: 9999px;
            margin-bottom: 12px;
            letter-spacing: 0.5px;
        }

        .title {
            font-size: 1.4rem;
            font-weight: 700;
            letter-spacing: -0.5px;
            color: #fff;
            margin-bottom: 6px;
        }

        .subtitle {
            font-size: 0.85rem;
            color: var(--text-muted);
            line-height: 1.4;
        }

        .alert {
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 0.85rem;
            line-height: 1.4;
            display: flex;
            align-items: flex-start;
            gap: 10px;
        }

        .alert-success {
            background: rgba(16, 185, 129, 0.12);
            border: 1px solid rgba(16, 185, 129, 0.3);
            color: #34d399;
        }

        .alert-error {
            background: rgba(239, 68, 68, 0.12);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #f87171;
        }

        .alert-warning {
            background: rgba(245, 158, 11, 0.12);
            border: 1px solid rgba(245, 158, 11, 0.3);
            color: #fbbf24;
        }

        .form-group {
            margin-bottom: 16px;
        }

        .label {
            display: block;
            font-size: 0.8rem;
            font-weight: 600;
            color: #e4e4e7;
            margin-bottom: 6px;
            font-family: 'JetBrains Mono', monospace;
        }

        .input {
            width: 100%;
            background: #18181b;
            border: 1px solid var(--border);
            color: #fff;
            padding: 10px 14px;
            border-radius: 6px;
            font-size: 0.9rem;
            font-family: 'JetBrains Mono', monospace;
            transition: all 0.2s;
        }

        .input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 2px rgba(0, 112, 243, 0.2);
        }

        .btn-submit {
            width: 100%;
            background: var(--primary);
            color: #fff;
            border: none;
            padding: 12px 16px;
            border-radius: 6px;
            font-size: 0.9rem;
            font-weight: 600;
            font-family: 'JetBrains Mono', monospace;
            cursor: pointer;
            transition: background 0.2s;
            margin-top: 8px;
        }

        .btn-submit:hover {
            background: var(--primary-hover);
        }

        .db-status-bar {
            background: #141417;
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 12px 14px;
            margin-bottom: 20px;
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.75rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .status-dot {
            display: inline-block;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            margin-right: 6px;
        }

        .status-connected { background: var(--success); box-shadow: 0 0 8px var(--success); }
        .status-disconnected { background: var(--error); box-shadow: 0 0 8px var(--error); }

        .details-toggle {
            color: var(--accent);
            cursor: pointer;
            font-size: 0.75rem;
            text-decoration: underline;
            background: none;
            border: none;
            font-family: inherit;
        }

        .custom-db-box {
            background: #121216;
            border: 1px solid rgba(80, 227, 194, 0.3);
            border-radius: 8px;
            padding: 16px;
            margin-bottom: 20px;
            display: none;
        }

        .custom-db-box.show {
            display: block;
        }

        .help-box {
            font-size: 0.75rem;
            color: #cbd5e1;
            background: rgba(0, 112, 243, 0.08);
            border: 1px dashed rgba(0, 112, 243, 0.3);
            border-radius: 6px;
            padding: 10px 12px;
            margin-bottom: 14px;
            line-height: 1.5;
        }

        .help-box code {
            color: var(--accent);
            background: rgba(0,0,0,0.4);
            padding: 2px 4px;
            border-radius: 4px;
        }

        .creds-preview {
            background: #141417;
            border: 1px dashed var(--border);
            border-radius: 8px;
            padding: 14px;
            margin-top: 20px;
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.8rem;
        }

        .creds-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 6px;
        }

        .creds-row:last-child {
            margin-bottom: 0;
        }

        .creds-label {
            color: var(--text-muted);
        }

        .creds-val {
            color: var(--accent);
            font-weight: 600;
        }

        .footer-note {
            text-align: center;
            margin-top: 24px;
            font-size: 0.75rem;
            color: var(--text-muted);
            line-height: 1.4;
        }

        .link-login {
            display: inline-block;
            margin-top: 12px;
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
        }

        .link-login:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <span class="badge-mono">// SYSTEM UTILITY</span>
        <h1 class="title">Admin Credential Setup</h1>
        <p class="subtitle">Setel ulang kredensial login akun Administrator NetQuiz.</p>
    </div>

    <!-- Database Status Bar -->
    <div class="db-status-bar">
        <div>
            <span class="status-dot <?= $pdo ? 'status-connected' : 'status-disconnected' ?>"></span>
            <span>DB: <?= htmlspecialchars($dbHost) ?> (<?= htmlspecialchars($dbUser) ?>)</span>
        </div>
        <button type="button" class="details-toggle" onclick="document.getElementById('customDbBox').classList.toggle('show');">
            <?= $pdo ? 'Ubah DB' : 'Sesuaikan Kredensial DB' ?>
        </button>
    </div>

    <?php if ($message): ?>
        <div class="alert alert-<?= $messageType ?>">
            <div><?= htmlspecialchars($message) ?></div>
        </div>
    <?php elseif ($dbError): ?>
        <div class="alert alert-error">
            <div>
                <strong>Koneksi Database Gagal (Access Denied):</strong><br>
                Password atau Username MySQL di InfinityFree tidak cocok. Silakan periksa password akun vPanel/InfinityFree Anda di bawah ini.
            </div>
        </div>
    <?php endif; ?>

    <form method="POST" action="">
        <!-- Custom DB Credentials Panel -->
        <div id="customDbBox" class="custom-db-box <?= ($dbError || !$pdo) ? 'show' : '' ?>">
            <div style="font-size: 0.82rem; font-weight: 600; margin-bottom: 8px; color: var(--accent);">
                ⚙️ Kredensial Database Hosting InfinityFree:
            </div>
            
            <div class="help-box">
                💡 <strong>Cara Cek di InfinityFree:</strong><br>
                Buka <code>dash.infinityfree.com</code> &rarr; Pilih Akun Anda &rarr; <strong>Account Details</strong> &rarr; Salin <strong>MySQL Password</strong> (Password vPanel).
            </div>

            <div class="form-group" style="margin-bottom: 10px;">
                <label class="label" style="font-size: 0.75rem;">MYSQL HOSTNAME</label>
                <input type="text" name="custom_db_host" class="input" value="<?= htmlspecialchars($dbHost) ?>" placeholder="e.g. sql301.infinityfree.com" required>
            </div>
            <div class="form-group" style="margin-bottom: 10px;">
                <label class="label" style="font-size: 0.75rem;">MYSQL DATABASE NAME</label>
                <input type="text" name="custom_db_name" class="input" value="<?= htmlspecialchars($dbName) ?>" placeholder="e.g. if0_42727530_netquiz" required>
            </div>
            <div class="form-group" style="margin-bottom: 10px;">
                <label class="label" style="font-size: 0.75rem;">MYSQL USERNAME</label>
                <input type="text" name="custom_db_user" class="input" value="<?= htmlspecialchars($dbUser) ?>" placeholder="e.g. if0_42727530" required>
            </div>
            <div class="form-group" style="margin-bottom: 0;">
                <label class="label" style="font-size: 0.75rem;">MYSQL PASSWORD (vPanel Password)</label>
                <input type="text" name="custom_db_pass" class="input" value="<?= htmlspecialchars($dbPass) ?>" placeholder="Password akun InfinityFree" required>
            </div>
        </div>

        <div class="form-group">
            <label class="label" for="username">[ADMIN_USERNAME]</label>
            <input type="text" id="username" name="username" class="input" value="<?= htmlspecialchars($_POST['username'] ?? $defaultUsername) ?>" required autocomplete="off">
        </div>

        <div class="form-group">
            <label class="label" for="email">[ADMIN_EMAIL]</label>
            <input type="email" id="email" name="email" class="input" value="<?= htmlspecialchars($_POST['email'] ?? $defaultEmail) ?>" required autocomplete="off">
        </div>

        <div class="form-group">
            <label class="label" for="password">[NEW_PASSWORD]</label>
            <input type="text" id="password" name="password" class="input" value="<?= htmlspecialchars($_POST['password'] ?? $defaultPassword) ?>" required autocomplete="off">
        </div>

        <button type="submit" class="btn-submit">Simpan & Perbarui Kredensial Admin</button>
    </form>

    <div class="creds-preview">
        <div class="creds-row">
            <span class="creds-label">Login URL:</span>
            <a href="login" class="creds-val" style="text-decoration: underline;">/login</a>
        </div>
        <div class="creds-row">
            <span class="creds-label">Role Akses:</span>
            <span class="creds-val">Administrator</span>
        </div>
    </div>

    <div class="footer-note">
        <p>⚠️ <strong>Catatan:</strong> Setelah sukses memperbarui admin, hapus file <code>reset_admin.php</code> dari file manager.</p>
        <a href="login" class="link-login">&larr; Buka Halaman Login NetQuiz</a>
    </div>
</div>

</body>
</html>
