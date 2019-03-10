<?php
declare(strict_types=1);

namespace Chevereto\Core;

use App\User;
use Chevereto\Core\Http\Response;

return new class extends Controller {
    public function __invoke(User $user = null)
    {
        return (new Response())->setContent($user)->setStatusCode(300);
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
