<?php

declare(strict_types=1);

namespace Chevereto\Core;

return new class() extends Controller {
    public function __invoke(\App\User $user = null)
    {
        dump('users:update', $parameters);

        return (object) ['user' => 'dummy', 'foo' => 'bar'];
    }

    // Options are stablished, then hooked later
    const OPTIONS = [
        'description' => 'Updates an user.',
        'parameters' => [
            'email' => [
                'description' => 'User email.',
            ],
        ],
    ];
};
