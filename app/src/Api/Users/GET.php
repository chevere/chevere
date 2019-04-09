<?php

declare(strict_types=1);

namespace App\Api\Users;

use App\User;
use Chevereto\Core\Controller;

class GET extends Controller
{
    // Route->setDescription('Obtiene un usuario.')
    const DESCRIPTION = 'Obtiene un usuario.';

    // Route->setType('user', User::class)
    const RESOURCES = [
        'user' => User::class,
    ];

    /** @var User The user entity */
    protected $user;

    public function __invoke() // NOTE: For all kind of Controllers: No args! (interface limitation)
    {
        dd('user', $this->user);
    }
}
