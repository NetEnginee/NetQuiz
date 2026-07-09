<?php
declare(strict_types=1);

namespace App\Core;

use Attribute;

/**
 * Authorize Attribute to restrict access to Controllers or actions based on Roles.
 */
#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD)]
class Authorize
{
    /**
     * @var Role[]
     */
    public array $roles;

    /**
     * Authorize constructor.
     *
     * @param Role ...$roles Allowed roles for the endpoint.
     */
    public function __construct(Role ...$roles)
    {
        $this->roles = $roles;
    }
}
