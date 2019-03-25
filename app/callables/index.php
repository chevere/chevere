<?php

declare(strict_types=1);

namespace App;

use Chevereto\Core\App;

return new class() extends Controller {
    public function __invoke()
    {
        $this->getResponse()
            ->setMeta(['Hello' => 'World!'])
            ->addData('info', 'api', ['entry' => 'HTTP GET /api', 'description' => 'Retrieves the exposed API.'])
            ->addData('info', 'cli', ['entry' => 'php app/console list', 'description' => 'Lists console commands.'])
            ->setStatusCode(200);
    }
};
