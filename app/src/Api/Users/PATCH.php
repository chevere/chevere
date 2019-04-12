<?php

declare(strict_types=1);

namespace App\Api\Users;

use App\User;
use Chevereto\Core\Controller;

class PATCH extends Controller
{
    protected static $description = 'Updates an user.';
    protected static $resources = [
        'user' => User::class,
    ];
    protected static $parameters = [
        'email' => [
            'description' => 'User email.',
        ],
    ];

    public function __construct(User $user)
    {
    }

    public function __invoke()
    {
    }
}
