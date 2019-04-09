<?php

declare(strict_types=1);

namespace App;

use Chevereto\Core\Controller;

return new class() extends Controller {
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
};
