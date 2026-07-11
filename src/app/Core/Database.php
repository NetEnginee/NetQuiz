<?php
declare(strict_types=1);
namespace App\Core;

use PDO;
use PDOException;

class Database
{
    private static $instance = null;
    private $conn;

    private function __construct()
    {
        // Load configurations
        $config = require dirname(__DIR__, 2) . '/config/config.php';

        $dsn = "mysql:host=" . $config['db_host'] . ";dbname=" . $config['db_name'] . ";charset=utf8mb4";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];

        try {
            $this->conn = new PDO($dsn, $config['db_user'], $config['db_pass'], $options);
            $this->ensureTablesExist();
        } catch (PDOException $e) {
            // Log database connection error securely
            error_log("Database Connection Error: " . $e->getMessage());
            http_response_code(500);
            die("Database Connection Error. Silakan hubungi administrator.");
        }
    }

    private function ensureTablesExist()
    {
        // Create badges table if not exists
        $sql = "CREATE TABLE IF NOT EXISTS badges (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            description TEXT NOT NULL,
            icon VARCHAR(100) NOT NULL DEFAULT 'award',
            metric VARCHAR(100) NOT NULL,
            target_value INT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
        $this->conn->exec($sql);

        // Create login_attempts table if not exists for brute force protection
        $sqlAttempts = "CREATE TABLE IF NOT EXISTS login_attempts (
            id INT AUTO_INCREMENT PRIMARY KEY,
            ip_address VARCHAR(45) NOT NULL,
            email VARCHAR(255) NOT NULL,
            attempted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            KEY idx_ip_time (ip_address, attempted_at),
            KEY idx_email_time (email, attempted_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
        $this->conn->exec($sqlAttempts);

        try {
            $this->conn->exec("ALTER TABLE quizzes ADD COLUMN image_path VARCHAR(255) NULL");
        } catch (PDOException $e) {
            // Kolom mungkin sudah ada, abaikan
        }

        try {
            $this->conn->exec("ALTER TABLE questions ADD COLUMN image_path VARCHAR(255) NULL");
        } catch (PDOException $e) {
            // Kolom mungkin sudah ada, abaikan
        }

        // Check if badges table is empty
        $stmt = $this->conn->query("SELECT COUNT(*) FROM badges");
        $count = (int) $stmt->fetchColumn();
        if ($count === 0) {
            // Seed default badges
            $defaultBadges = [
                ['Keluar dari Zona Nyaman', 'Selesaikan satu kuis.', 'play', 'completed_quizzes', 1],
            ];

            $stmtInsert = $this->conn->prepare("INSERT INTO badges (title, description, icon, metric, target_value) VALUES (?, ?, ?, ?, ?)");
            foreach ($defaultBadges as $badge) {
                $stmtInsert->execute($badge);
            }
        }
    }

    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public function getConnection()
    {
        return $this->conn;
    }
}
