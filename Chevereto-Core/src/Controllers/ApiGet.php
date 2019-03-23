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
 * Expose an API endpoint.
 */
class ApiGet extends Controller
{
    const OPTIONS = [
        'description' => 'Retrieve endpoint.',
    ];
    /**
     * @param string $endpoint An API endpoint (needed when running from CLI).
     */
    public function __invoke(string $endpoint = null)
    {
        $app = $this->getApp();
        $route = $app->getRoute();
        $endpoint = $route->getKey();
        //
        if ($endpoint == null) {
            $message =
                (new Message('You have to provide the %s argument when running this callable without route context.'))
                    ->code('%s', 'endpoint');
            if (Console::isRunning()) {
                Console::io()->error($message);
                exit;
            } else {
                throw new CoreException($message);
            }
        }
        $response = $this->getResponse();
        $statusCode = 200;
        // $json = new Json();
        if ($endpointData = $this->getApp()->getApis()->getEndpoint($endpoint)) {
            $response->setMeta(['api' => $endpoint]);
            foreach ($endpointData as $property => $data) {
                if ($property == 'wildcards') {
                    continue;
                }
                $response->addData('endpoint', $property, $data);
            }
        } else {
            $statusCode = 404;
            // $response->setResponse("Endpoint doesn't exists", $statusCode);
        }
        $response->setStatusCode($statusCode);
    }
}
