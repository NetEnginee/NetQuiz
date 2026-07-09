<?php
declare(strict_types=1);

namespace App\Core;

/**
 * Role Enum representing user privileges.
 */
enum Role: string
{
    case ADMIN = 'admin';
    case USER = 'user';
    case GUEST = 'guest';

    /**
     * Get the description of the role.
     */
    public function label(): string
    {
        return match ($this) {
            self::ADMIN => 'Administrator',
            self::USER => 'Siswa / Member',
            self::GUEST => 'Pengunjung',
        };
    }
}
