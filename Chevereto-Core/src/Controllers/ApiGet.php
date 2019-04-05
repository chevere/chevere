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
use InvalidArgumentException;

/**
 * Expose an API endpoint.
 */
class ApiGet extends Controller
{
    const OPTIONS = [
        'description' => 'Retrieve endpoint.',
    ];

    private $endpoint;

    /**
     * @param string $endpoint an API endpoint (/api)
     */
    public function __invoke(string $endpoint = null)
    {
        if (isset($endpoint)) {
            $route = $this->getApp()->getRouter()->resolve($endpoint);
        } else {
            if ($this->getApp()->hasRoute()) {
                $route = $this->getApp()->getRoute();
                $endpoint = $route->getKey();
            } else {
                $message =
                    (string)
                        (new Message('Must provide the %s argument when running this callable without route context.'))
                            ->code('%s', '$endpoint');
                if (CLI) {
                    Console::io()->error($message);

                    return;
                }
                throw new InvalidArgumentException($message);
            }
        }

        if (!isset($route)) {
            $this->getResponse()->setStatusCode(404);

            return;
        }

        $this->endpoint = $endpoint;

        $this->process();
    }

    private function process()
    {
        $endpointData = $this->getApis()->getEndpoint($this->endpoint);
        if ($endpointData) {
            $this->getResponse()->setMeta(['api' => $this->endpoint]);
            foreach ($endpointData as $property => $data) {
                if ($property == 'wildcards') {
                    continue;
                }
                $this->getResponse()->addData('endpoint', $property, $data);
            }
        }
        $this->getResponse()->setStatusCode(200);
    }
}
