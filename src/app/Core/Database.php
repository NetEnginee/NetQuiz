<?php
declare(strict_types=1);

namespace App\Core;

use PDO;
use PDOException;
use RuntimeException;
use Throwable;

/**
 * Database Connection & Query Management.
 * Manages PDO instance with strict configuration, prepared query execution, and transactions.
 */
class Database
{
    private static ?Database $instance = null;
    private PDO $connection;

    public function __construct(?array $config = null)
    {
        if ($config === null) {
            $configFile = dirname(__DIR__, 2) . '/config/config.php';
            if (file_exists($configFile)) {
                $config = require $configFile;
            } else {
                throw new RuntimeException("Database configuration file not found at [{$configFile}].");
            }
        }

        $host = $config['db_host'] ?? 'localhost';
        $dbname = $config['db_name'] ?? 'db_mikrotik_quiz';
        $user = $config['db_user'] ?? 'root';
        $pass = $config['db_pass'] ?? '';
        $port = (int)($config['db_port'] ?? 3306);
        $charset = $config['db_charset'] ?? 'utf8mb4';

        $dsn = "mysql:host={$host};port={$port};dbname={$dbname};charset={$charset}";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::ATTR_PERSISTENT => true,
        ];

        try {
            $this->connection = new PDO($dsn, $user, $pass, $options);
        } catch (PDOException $e) {
            error_log("Database Connection Error: " . $e->getMessage());
            throw new RuntimeException("Koneksi database gagal: " . $e->getMessage(), 500, $e);
        }
    }

    /**
     * Singleton accessor for legacy components.
     */
    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Get underlying PDO connection.
     */
    public function getConnection(): PDO
    {
        return $this->connection;
    }

    /**
     * Begin a database transaction.
     */
    public function beginTransaction(): bool
    {
        return $this->connection->beginTransaction();
    }

    /**
     * Commit a database transaction.
     */
    public function commit(): bool
    {
        return $this->connection->commit();
    }

    /**
     * Rollback a database transaction.
     */
    public function rollBack(): bool
    {
        if ($this->connection->inTransaction()) {
            return $this->connection->rollBack();
        }
        return false;
    }

    /**
     * Check if currently in transaction.
     */
    public function inTransaction(): bool
    {
        return $this->connection->inTransaction();
    }

    /**
     * Execute a callback inside an atomic transaction.
     *
     * @template T
     * @param callable(PDO): T $callback
     * @return T
     * @throws Throwable
     */
    public function transaction(callable $callback): mixed
    {
        $this->beginTransaction();
        try {
            $result = $callback($this->connection);
            $this->commit();
            return $result;
        } catch (Throwable $e) {
            $this->rollBack();
            throw $e;
        }
    }
}
