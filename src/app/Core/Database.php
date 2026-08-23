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
    private ?PDO $connection = null;
    private array $config;

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
        $this->config = $config;
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
     * Connect to database lazily when first needed.
     */
    private function connect(): void
    {
        $host = $this->config['db_host'] ?? 'localhost';
        $dbname = $this->config['db_name'] ?? 'db_mikrotik_quiz';
        $user = $this->config['db_user'] ?? 'root';
        $pass = $this->config['db_pass'] ?? '';
        $port = (int)($this->config['db_port'] ?? 3306);
        $charset = $this->config['db_charset'] ?? 'utf8mb4';

        $dsn = "mysql:host={$host};port={$port};dbname={$dbname};charset={$charset}";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::ATTR_PERSISTENT => false, // Must be false on shared hosting like InfinityFree
            PDO::ATTR_TIMEOUT => 5,
        ];

        try {
            $this->connection = new PDO($dsn, $user, $pass, $options);
        } catch (PDOException $e) {
            error_log("Database Connection Error: " . $e->getMessage());
            throw new RuntimeException("Koneksi database gagal: " . $e->getMessage(), 500, $e);
        }
    }

    /**
     * Get underlying PDO connection lazily.
     */
    public function getConnection(): PDO
    {
        if ($this->connection === null) {
            $this->connect();
        }
        return $this->connection;
    }

    /**
     * Proxy PDO prepare.
     */
    public function prepare(string $query, array $options = []): \PDOStatement
    {
        return $this->getConnection()->prepare($query, $options);
    }

    /**
     * Proxy PDO query.
     */
    public function query(string $query, ?int $fetchMode = null, mixed ...$fetchModeArgs): \PDOStatement
    {
        if ($fetchMode !== null) {
            return $this->getConnection()->query($query, $fetchMode, ...$fetchModeArgs);
        }
        return $this->getConnection()->query($query);
    }

    /**
     * Proxy PDO lastInsertId.
     */
    public function lastInsertId(?string $name = null): string|false
    {
        return $this->getConnection()->lastInsertId($name);
    }

    /**
     * Begin a database transaction.
     */
    public function beginTransaction(): bool
    {
        return $this->getConnection()->beginTransaction();
    }

    /**
     * Commit a database transaction.
     */
    public function commit(): bool
    {
        return $this->getConnection()->commit();
    }

    /**
     * Rollback a database transaction.
     */
    public function rollBack(): bool
    {
        $conn = $this->getConnection();
        if ($conn->inTransaction()) {
            return $conn->rollBack();
        }
        return false;
    }

    /**
     * Check if currently in transaction.
     */
    public function inTransaction(): bool
    {
        return $this->connection !== null && $this->connection->inTransaction();
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
        $conn = $this->getConnection();
        $this->beginTransaction();
        try {
            $result = $callback($conn);
            $this->commit();
            return $result;
        } catch (Throwable $e) {
            $this->rollBack();
            throw $e;
        }
    }

    /**
     * Magic proxy for any other PDO methods.
     */
    public function __call(string $method, array $args): mixed
    {
        return $this->getConnection()->$method(...$args);
    }
}
