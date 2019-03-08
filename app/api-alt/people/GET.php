<?php
declare(strict_types=1);

namespace Chevereto\Core;

use App\User;

return new class extends Controller {
    public function __invoke(User $user = null) : array
    {
        return [$user, 'dummy', 'data'];
    }
    const OPTIONS = [
        '/' => [
            'description' => 'Obtiene usuarios.',
        ],
        '*' => [
            'description' => 'Obtiene un usuario.',
        ]
    ];
};
