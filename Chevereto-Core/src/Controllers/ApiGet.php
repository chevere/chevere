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
 * Expose an API endpoint.
 */
class ApiGet extends Controller
{
    const OPTIONS = [
        'description' => 'Retrieve endpoint.',
    ];

    /**
     * @param string $endpoint an API endpoint (/api)
     */
    public function __invoke(string $endpoint = null)
    {
        if (isset($endpoint)) {
            $route = $this->getApp()->getRouter()->resolve($endpoint);
        } else {
            $route = $this->getApp()->getObject('route');
            if (isset($route)) {
                $endpoint = $route->getKey();
            } else {
                $message =
                    (string) (new Message('Must provide the %s argument when running this callable without route context.'))
                        ->code('%s', '$endpoint');
                if (CLI) {
                    Console::io()->error($message);

                    return;
                } else {
                    throw new Exception($message);
                }
            }
        }

        if (!isset($route)) {
            $this->getResponse()->setStatusCode(404);

            return;
        }

        $response = $this->getResponse();
        $statusCode = 200;
        $endpointData = $this->getApis()->getEndpoint($endpoint);
        if ($endpointData) {
            $response->setMeta(['api' => $endpoint]);
            foreach ($endpointData as $property => $data) {
                if ($property == 'wildcards') {
                    continue;
                }
                $response->addData('endpoint', $property, $data);
            }
        } else {
            $statusCode = 404;
        }
        $response->setStatusCode($statusCode);
    }
}
