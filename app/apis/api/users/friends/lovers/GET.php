<?php

declare(strict_types=1);

namespace Chevereto\Core;

return new class() extends Controller {
    public function __invoke(\App\User $user, \App\User $friend, \App\User $lover = null)
    {
        dump('ARGS>', func_get_args());

        return (object) ['user' => 'dummy', 'foo' => 'bar'];
    }

    const OPTIONS = [
        '/' => [
            'description' => 'Retrieves an user+friend lovers.',
        ],
        '*' => [
            'description' => 'Retrieves a user+friend lover.',
        ],
    ];
};
