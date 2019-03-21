<?php declare(strict_types=1);
/*
 * This file is part of Chevereto\Core.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Chevereto\Core\Controllers;

// use function Chevereto\Core\dd;
use Chevereto\Core\Console;
use Chevereto\Core\CoreException;
use Chevereto\Core\Message;
use Chevereto\Core\App;
use Chevereto\Core\Controller;
use Chevereto\Core\Json;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Identical to OPTIONS.
 */
class ApiOptions extends Controller
{
    const OPTIONS = [
      'description' => 'Retrieve endpoint OPTIONS.',
    ];
    /**
    //  * @param string $endpoint An API endpoint (needed when running from CLI).
     */
    public function __invoke(/*string $endpoint = null*/)
    {
        $app = $this->getApp();
        $route = $app->getRoute();
        $endpoint = $route->getKey();
        //
        if ($endpoint == null) {
            $message =
                (new Message('You have to provide the %s argument when running this callable without route context.'))
                    ->code('%s', 'endpoint');
            if (Console::isAvailable()) {
                Console::io()->error($message);
                exit;
            } else {
                throw new CoreException($message);
            }
        }
        //
        $statusCode = 200;
        $json = new Json();
        $apis = $app->getApis();
        if ($endpointData = $apis->getBaseOptions($endpoint) ?? $apis->getEndpoint($endpoint)) {
            $json->setResponse(sprintf('Endpoint %s OPTIONS exposed.', $endpoint), $statusCode);
            $json->setDataKey('OPTIONS', $endpointData['OPTIONS']);
        } else {
            $statusCode = 404;
            $json->setResponse("Endpoint doesn't exists", $statusCode);
        }
        return (new JsonResponse())->setContent($json)->setStatusCode($statusCode);
    }
}
