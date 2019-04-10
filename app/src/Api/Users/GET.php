<?php

declare(strict_types=1);

namespace App\Api\Users;

use App\User;
use Chevereto\Core\Controller;

class GET extends Controller
{
    /** @var string Controller description */
    const DESCRIPTION = 'Obtiene un usuario.';

    /** @var array Controller resources [propName => className] */
    const RESOURCES = [
        'user' => User::class,
    ];

    /** @var User The user entity resource */
    protected $user;

    public function __invoke() // NOTE: No args! (interface limitation)
    {
        dd('user', $this->user);
    }
}
