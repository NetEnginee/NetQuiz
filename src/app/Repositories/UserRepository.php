<?php
declare(strict_types=1);

namespace App\Repositories;

use PDO;
use App\Core\Database;

/**
 * UserRepository - Handles database operations for User entity securely.
 */
class UserRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Find user by email address.
     */
    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    /**
     * Find user by ID (excludes password details for security).
     */
    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT id, username, email, created_at FROM users WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    /**
     * Check if email is registered.
     */
    public function emailExists(string $email, ?int $excludeId = null): bool
    {
        $sql = "SELECT id FROM users WHERE email = :email";
        $params = ['email' => $email];

        if ($excludeId !== null) {
            $sql .= " AND id != :exclude_id";
            $params['exclude_id'] = $excludeId;
        }

        $sql .= " LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch() !== false;
    }

    /**
     * Check if username is registered.
     */
    public function usernameExists(string $username, ?int $excludeId = null): bool
    {
        $sql = "SELECT id FROM users WHERE username = :username";
        $params = ['username' => $username];

        if ($excludeId !== null) {
            $sql .= " AND id != :exclude_id";
            $params['exclude_id'] = $excludeId;
        }

        $sql .= " LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch() !== false;
    }

    /**
     * Create a new user. Plaintext password is NEVER stored.
     */
    public function create(string $username, string $email, string $password): int
    {
        $hashedPassword = password_hash($password, PASSWORD_ARGON2ID);
        
        // Fallback to BCRYPT if ARGON2ID is not supported in the PHP environment
        if ($hashedPassword === false || $hashedPassword === null) {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        }

        $stmt = $this->db->prepare("INSERT INTO users (username, email, password) VALUES (:username, :email, :password)");
        $stmt->execute([
            'username' => $username,
            'email' => $email,
            'password' => $hashedPassword
        ]);

        return (int)$this->db->lastInsertId();
    }

    /**
     * Update user profile information.
     */
    public function updateProfile(int $id, string $username, string $email): bool
    {
        $stmt = $this->db->prepare("UPDATE users SET username = :username, email = :email WHERE id = :id");
        return $stmt->execute([
            'username' => $username,
            'email' => $email,
            'id' => $id
        ]);
    }

    /**
     * Update user password. Plaintext password is NEVER stored.
     */
    public function updatePassword(int $id, string $newPassword): bool
    {
        $hashedPassword = password_hash($newPassword, PASSWORD_ARGON2ID);
        if ($hashedPassword === false || $hashedPassword === null) {
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        }

        $stmt = $this->db->prepare("UPDATE users SET password = :password WHERE id = :id");
        return $stmt->execute([
            'password' => $hashedPassword,
            'id' => $id
        ]);
    }

    /**
     * Delete user account (Admin use only).
     */
    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM users WHERE id = :id AND email != 'admin@routerosquiz.academy'");
        return $stmt->execute(['id' => $id]);
    }

    /**
     * Fetch all users excluding the system administrator.
     */
    public function getAllUsers(): array
    {
        $stmt = $this->db->prepare("SELECT id, username, email, created_at FROM users WHERE email != 'admin@routerosquiz.academy' ORDER BY id DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
