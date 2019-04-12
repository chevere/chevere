<?php

declare(strict_types=1);

namespace App\Api\Users;

use Chevereto\Core\Controller;

class _POST extends Controller
{
    protected static $description = 'Creates an user.';
    protected static $parameters = [
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
