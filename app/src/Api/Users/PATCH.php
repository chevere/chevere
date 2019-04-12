<?php

declare(strict_types=1);

namespace App\Api\Users;

use App\User;

class PATCH extends Resource
{
    protected static $description = 'Updates an user.';

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
