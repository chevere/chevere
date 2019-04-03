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
use Chevereto\Core\CoreException;
use Chevereto\Core\Message;
use Chevereto\Core\Controller;

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
        $route = $this->getApp()->getObject('route');
        if ($route) {
            $endpoint = $route->getKey();
        } else {
            if (!isset($endpoint)) {
                $message =
                    (new Message('Must provide a %s argument when running this callable without route context.'))
                        ->code('%s', '(string) $endpoint');
                if (CLI) {
                    Console::io()->error($message);

                    return;
                } else {
                    throw new CoreException($message);
                }
            }
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
