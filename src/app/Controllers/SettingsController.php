<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Authorize;
use App\Core\Role;
use App\Core\Response;

#[Authorize(Role::USER, Role::ADMIN)]
class SettingsController extends Controller
{
    public function index(): Response
    {
        return $this->redirect(BASE_URL . '/');
    }
}
