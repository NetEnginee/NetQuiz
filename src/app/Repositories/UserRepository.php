<?php
declare(strict_types=1);

namespace App\Repositories;

use PDO;
use App\Core\Database;

/**
 * UserRepository - Secure User Database Access Implementation.
 */
class UserRepository implements UserRepositoryInterface
{
    private PDO $db;

    public function __construct(?Database $database = null)
    {
        $this->db = ($database ?? Database::getInstance())->getConnection();
    }

    /**
     * Find user by ID.
     */
    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT id, username, email, status, created_at FROM users WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        $user = $stmt->fetch();
        return $user ?: null;
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
     * Fetch all non-admin registered users.
     */
    public function getAllUsers(): array
    {
        $stmt = $this->db->query("SELECT id, username, email, status, created_at FROM users WHERE LOWER(TRIM(email)) != 'admin@routerosquiz.academy' ORDER BY id DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Check if email exists.
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
     * Check if username exists.
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
     * Create new user with Argon2ID/Bcrypt hashed password.
     */
    public function create(string $username, string $email, string $password): int
    {
        $hashedPassword = password_hash($password, PASSWORD_ARGON2ID);
        if ($hashedPassword === false || $hashedPassword === null) {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        }

        $stmt = $this->db->prepare("INSERT INTO users (username, email, password, status) VALUES (:username, :email, :password, 'Aktif')");
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
            'id' => $id,
            'username' => $username,
            'email' => $email
        ]);
    }

    /**
     * Update user password securely.
     */
    public function updatePassword(int $id, string $newPassword): bool
    {
        $hashedPassword = password_hash($newPassword, PASSWORD_ARGON2ID);
        if ($hashedPassword === false || $hashedPassword === null) {
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        }

        $stmt = $this->db->prepare("UPDATE users SET password = :password WHERE id = :id");
        return $stmt->execute([
            'id' => $id,
            'password' => $hashedPassword
        ]);
    }

    /**
     * Update user active / suspended status.
     */
    public function updateStatus(int $id, string $status): bool
    {
        $stmt = $this->db->prepare("UPDATE users SET status = :status WHERE id = :id");
        return $stmt->execute([
            'id' => $id,
            'status' => $status
        ]);
    }

    /**
     * Delete user and cascade relations.
     */
    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM users WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
}
