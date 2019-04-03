<?php

declare(strict_types=1);
/*
 * This file is part of Chevereto\Core.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Chevereto\Core\Controllers;

use const Chevereto\Core\CLI;
use Chevereto\Core\Console;
use Chevereto\Core\Message;
use Chevereto\Core\Controller;
use Exception;

/**
 * Identical to OPTIONS.
 */
class ApiOptions extends Controller
{
    const OPTIONS = [
      'description' => 'Retrieve endpoint OPTIONS.',
    ];

    /**
     * @param string $endpoint an API endpoint (/api)
     */
    public function __invoke(string $endpoint = null)
    {
        $route = $this->getApp()->getObject('route');
        if ($route) {
            $endpoint = $route->getKey();
        }
        if ($endpoint == null) {
            $message =
                (string) (new Message('Must provide a %s argument when running this callable without route context.'))
            ->code('%s', '$endpoint');
            if (CLI) {
                Console::io()->error($message);

                return;
            } else {
                throw new Exception($message);
            }
        }
        $response = $this->getResponse();
        $statusCode = 200;
        $apis = $this->getApis();
        $apiKey = $apis->getEndpointApiKey($endpoint);
        $endpointData = $apis->getBaseOptions($endpoint) ?? $apis->getEndpoint($endpoint);
        if ($endpointData) {
            $response->setMeta(['api' => $apiKey]);
            $response->addData('OPTIONS', $endpoint, $endpointData['OPTIONS']);
        } else {
            $statusCode = 404;
            // $json->setResponse("Endpoint doesn't exists", $statusCode);
        }
        $response->setStatusCode($statusCode);
    }
}
