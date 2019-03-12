<?php
declare(strict_types=1);

namespace App;

use function Chevereto\Core\dd;
use Chevereto\Core\App;
use Chevereto\Core\Console;
use Chevereto\Core\Json;
use Symfony\Component\HttpFoundation\JsonResponse;

return new class extends Controller {
    public function __invoke()
    {
        // Console::writeln(['Running ' . __FILE__, 'EXIT']);
        // exit;
        $json = new Json();
        $json->setResponse('Hello, World!', 100);
        $json
            ->addData('api', ['entry' => 'HTTP GET /api', 'description' => 'Retrieves the exposed API.'])
            ->addData('cli', ['entry' => 'php app/console list', 'description' => 'Lists console commands.']);
        
        return (new JsonResponse())->setContent($json)->setStatusCode(200);
    }
};