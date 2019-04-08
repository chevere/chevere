<?php

declare(strict_types=1);

namespace App;

use Chevereto\Core\Controller;

return new class() extends Controller {
    const DESCRIPTION = 'Obtiene usuarios.';

    public function __construct($input)
    {
        $this->user = new User($input);
    }

    public function __invoke()
    {
    }
};
