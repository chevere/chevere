<?php

declare(strict_types=1);

namespace Chevereto\Core;

use App\User;

return new class() extends Controller {
    const OPTIONS = [
        '/' => [
            'description' => 'Obtiene usuarios.',
        ],
        '*' => [
            'description' => 'Obtiene un usuario.',
        ],
    ];

    public function __invoke(User $user = null)
    {
    }
};
