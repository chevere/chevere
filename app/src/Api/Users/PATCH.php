<?php

declare(strict_types=1);

namespace App\Api\Users;

class PATCH extends Resource
{
    protected static $description = 'Updates an user.';

    protected static $parameters = [
        'email' => [
            'description' => 'User email.',
        ],
    ];
}
