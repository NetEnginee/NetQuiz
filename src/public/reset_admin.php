<?php
declare(strict_types=1);

// Auto-load class mapping PSR-4
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $base_dir = dirname(__DIR__) . '/app/';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

// Load config
$config = require_once dirname(__DIR__) . '/config/config.php';
define('BASE_URL', $config['base_url']);

try {
    $db = \App\Core\Database::getInstance()->getConnection();
    
    // 1. Fetch and show current users in database for debug
    $stmt = $db->query("SELECT id, username, email FROM users");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h3>Daftar Pengguna Saat Ini di Database:</h3>";
    echo "<table border='1' cellpadding='5' style='border-collapse: collapse; margin-bottom: 2rem;'>";
    echo "<tr style='background: #f1f5f9;'><th>ID</th><th>Username</th><th>Email</th></tr>";
    foreach ($users as $u) {
        echo "<tr><td>{$u['id']}</td><td>" . htmlspecialchars($u['username']) . "</td><td>" . htmlspecialchars($u['email']) . "</td></tr>";
    }
    echo "</table>";

    // 2. Perform automated correction or insertion
    $newPasswordHash = password_hash('Admin12345!', PASSWORD_BCRYPT, ['cost' => 12]);
    $adminFound = false;
    
    // Check if there is an admin account under either old or new email
    $stmtCheck = $db->query("SELECT id, email FROM users WHERE email LIKE '%admin%' OR username LIKE '%admin%'");
    $admins = $stmtCheck->fetchAll(PDO::FETCH_ASSOC);
    
    if (!empty($admins)) {
        // Update existing admin account
        $adminId = $admins[0]['id'];
        $stmtUpdate = $db->prepare("UPDATE users SET email = 'admin@routerosquiz.academy', username = 'admin@routerosquiz.academy', password = :password WHERE id = :id");
        $stmtUpdate->execute(['password' => $newPasswordHash, 'id' => $adminId]);
        
        echo "<h3 style='color: #10b981;'>Sukses memperbarui akun Admin lama (ID: $adminId)!</h3>";
        echo "<p>Email & Username telah di-update menjadi: <strong>admin@routerosquiz.academy</strong></p>";
        echo "<p>Password baru telah di-reset menjadi: <strong>Admin12345!</strong></p>";
    } else {
        // Create new admin account from scratch
        $stmtInsert = $db->prepare("INSERT INTO users (username, email, password) VALUES ('admin@routerosquiz.academy', 'admin@routerosquiz.academy', :password)");
        $stmtInsert->execute(['password' => $newPasswordHash]);
        
        echo "<h3 style='color: #10b981;'>Sukses membuat akun Admin baru!</h3>";
        echo "<p>Email & Username: <strong>admin@routerosquiz.academy</strong></p>";
        echo "<p>Password: <strong>Admin12345!</strong></p>";
    }
    
    echo "<p style='color: #ef4444; font-weight: bold; margin-top: 1.5rem;'>PENTING: Segera hapus file reset_admin.php ini dari hosting Anda setelah selesai!</p>";

} catch (Exception $e) {
    echo "<h3>Terjadi error:</h3> " . htmlspecialchars($e->getMessage());
}
