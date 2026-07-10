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
    
    // Reset password for admin@routerosquiz.academy to 'Admin12345!'
    $newPasswordHash = password_hash('Admin12345!', PASSWORD_BCRYPT, ['cost' => 12]);
    
    $stmt = $db->prepare("UPDATE users SET password = :password WHERE email = 'admin@routerosquiz.academy'");
    $stmt->execute(['password' => $newPasswordHash]);
    
    if ($stmt->rowCount() > 0) {
        echo "<h3>Sukses! Password Admin berhasil di-reset menjadi:</h3>";
        echo "<p style='font-size: 1.25rem; font-weight: bold; color: #10b981;'>Admin12345!</p>";
        echo "<p style='color: #ef4444; font-weight: bold;'>PENTING: Segera hapus file reset_admin.php ini dari hosting Anda demi alasan keamanan!</p>";
    } else {
        echo "<h3>Gagal me-reset password.</h3>";
        echo "<p>Akun dengan email 'admin@routerosquiz.academy' tidak ditemukan di database Anda.</p>";
    }
} catch (Exception $e) {
    echo "<h3>Terjadi error:</h3> " . htmlspecialchars($e->getMessage());
}
