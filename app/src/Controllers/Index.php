<?php

declare(strict_types=1);

namespace App\Controllers;

use Chevereto\Core\Controller;

class Index extends Controller
{
    public function __invoke()
    {
        $this->getResponse()
            ->setMeta(['Hello' => 'World!'])
            ->addData('info', 'api', ['entry' => 'HTTP GET /api', 'description' => 'Retrieves the exposed API.'])
            ->addData('info', 'cli', ['entry' => 'php app/console list', 'description' => 'Lists console commands.'])
            ->setStatusCode(200);
    }
}
