<?php

declare(strict_types=1);

namespace Chevereto\Core;

use App\User;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

return new class() extends Controller {
    public function __invoke(User $user = null)
    {
        return (new HttpResponse())->setContent($user)->setStatusCode(300);
    }

    const OPTIONS = [
        '/' => [
            'description' => 'Obtiene usuarios.',
        ],
        '*' => [
            'description' => 'Obtiene un usuario.',
        ],
    ];
};
