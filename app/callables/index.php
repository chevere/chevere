<?php
declare(strict_types=1);

namespace App;

use function Chevereto\Core\dd;

use Chevereto\Core\App;
use Chevereto\Core\CoreException;
use Chevereto\Core\ResponseData;
use Chevereto\Core\Json;
use Chevereto\Core\Data;

// use Symfony\Component\HttpFoundation\JsonResponse;

return new class extends Controller {
    public function __invoke() : ResponseData
    {
        throw new CoreException('SHiiieeet');
        $json = new Json();
        $json->setResponse('Hello, World!', 100);
        $json
            ->setDataKey('api', ['entry' => 'HTTP GET /api', 'description' => 'Retrieves the exposed API.'])
            ->setDataKey('cli', ['entry' => 'php app/console list', 'description' => 'Lists console commands.']);
        $json->setResponseKey('gg', '1313');
        return new ResponseData(400, $json);
        // return (new JsonResponse())->setContent($json)->setStatusCode(200);
    }
};
