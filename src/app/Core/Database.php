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

        // Create materials table if not exists
        $sqlMaterials = "CREATE TABLE IF NOT EXISTS materials (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            content LONGTEXT NOT NULL,
            category VARCHAR(100) NOT NULL,
            difficulty VARCHAR(50) NOT NULL DEFAULT 'Mudah',
            image_path VARCHAR(255) DEFAULT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
        $this->conn->exec($sqlMaterials);

        // Seed default materials if empty
        $stmtMat = $this->conn->query("SELECT COUNT(*) FROM materials");
        $countMat = (int) $stmtMat->fetchColumn();
        if ($countMat === 0) {
            $defaultMaterials = [
                [
                    'Pengenalan Dasar Routing Static di MikroTik',
                    '<h2>Apa itu Routing Static?</h2><p>Static routing adalah metode routing di mana administrator jaringan secara manual mengkonfigurasi rute-rute dalam tabel routing router. Ini adalah cara paling mendasar untuk menghubungkan dua atau lebih jaringan berbeda melalui MikroTik.</p><h3>Langkah Konfigurasi Static Route di RouterOS:</h3><pre><code>/ip route\nadd dst-address=192.168.2.0/24 gateway=192.168.1.1</code></pre><p>Parameter utama yang perlu dikonfigurasi adalah <strong>dst-address</strong> (jaringan tujuan) dan <strong>gateway</strong> (IP hop berikutnya/interface keluar).</p>',
                    'Routing',
                    'Mudah'
                ],
                [
                    'Dasar-Dasar Firewall Filter Rules',
                    '<h2>Fungsi Firewall Filter</h2><p>Firewall filter pada MikroTik RouterOS digunakan untuk melindungi router dari akses tidak sah serta mengontrol lalu lintas data yang masuk, keluar, atau melewati router.</p><h3>Chain Utama pada Filter Rules:</h3><ul><li><strong>Input</strong>: Digunakan untuk memfilter paket data yang ditujukan langsung ke router.</li><li><strong>Forward</strong>: Digunakan untuk memfilter paket data yang melintasi router dari satu interface ke interface lainnya.</li><li><strong>Output</strong>: Digunakan untuk memfilter paket data yang berasal dari router itu sendiri.</li></ul><h3>Contoh Konfigurasi Memblokir Ping (ICMP):</h3><pre><code>/ip firewall filter\nadd chain=input protocol=icmp action=drop</code></pre>',
                    'Firewall & NAT',
                    'Mudah'
                ]
            ];
            $stmtInsertMat = $this->conn->prepare("INSERT INTO materials (title, content, category, difficulty) VALUES (?, ?, ?, ?)");
            foreach ($defaultMaterials as $mat) {
                $stmtInsertMat->execute($mat);
            }
        }

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
