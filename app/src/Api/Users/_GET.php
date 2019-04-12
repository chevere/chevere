<?php

declare(strict_types=1);

namespace App\Api\Users;

use Chevereto\Core\Controller;

class _GET extends Controller
{
    protected static $description = 'Obtiene usuarios.';

    public function __construct($input)
    {
        $this->user = new User($input);
    }

    public function __invoke()
    {
    }
}
