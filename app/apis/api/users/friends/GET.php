<?php
declare(strict_types=1);

namespace Chevereto\Core;

use Symfony\Component\HttpFoundation\Response;

return new class extends Controller {
    // public function __invoke(\App\User $user, \App\User $friend = null)
    public function __invoke(\App\User $user, \App\User $friend = null)
    {
        return (new Response())->setContent('$user')->setStatusCode(303);
    }
    const OPTIONS = [
        // own root
        '/' => [
            'description' => 'Retrieves user friends.',
        ],
        // /*
        '*' => [
            'description' => 'Retrieves user friend.',
        ]
    ];
};
/**
 * TODO: No registra las siguientes:
 * - (API) users/friends
 * - (API) users/{user}/friends
 */