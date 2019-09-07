<?php

declare(strict_types=1);

namespace App\Api\Users;

class GET extends Resource
{
    /** @var string Controller description */
    protected static $description = 'Obtiene un usuario.';

    public function __invoke() // NOTE: No args! (interface limitation)
    {
        dd('user', $this->user);
    }
}
