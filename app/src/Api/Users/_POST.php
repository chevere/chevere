<?php

declare(strict_types=1);

namespace App\Api\Users;

use Chevereto\Core\Controller;

class _POST extends Controller
{
    const DESCRIPTION = 'Creates an user.';
    const PARAMETERS = [
        'username' => [
            'description' => 'Username.',
        ],
        'email' => [
            'description' => 'User email.',
        ],
    ];

    public function __invoke()
    {
        return 'post user';
    }
}
