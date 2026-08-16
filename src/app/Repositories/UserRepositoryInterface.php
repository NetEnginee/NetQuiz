<?php
declare(strict_types=1);

namespace App\Repositories;

/**
 * UserRepository Contract.
 */
interface UserRepositoryInterface
{
    public function findById(int $id): ?array;
    public function findByEmail(string $email): ?array;
    public function getAllUsers(): array;
    public function emailExists(string $email, ?int $excludeId = null): bool;
    public function usernameExists(string $username, ?int $excludeId = null): bool;
    public function create(string $username, string $email, string $password): int;
    public function updateProfile(int $id, string $username, string $email): bool;
    public function updatePassword(int $id, string $newPassword): bool;
    public function updateStatus(int $id, string $status): bool;
    public function delete(int $id): bool;
}
