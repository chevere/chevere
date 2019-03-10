<?php
declare(strict_types=1);

namespace Chevereto\Core;

return new class extends Controller {
    public function __invoke()
    {
        dump('users:create', $parameters);
        return 'post user';
    }
    // Options (GET) exposed
    const OPTIONS = [
        'description' => 'Creates an user.',
        'parameters' => [
            'username' => [
                'description' => 'Username.',
            ],
            'email' => [
                'description' => 'User email.',
            ]
        ]
    ];
};
