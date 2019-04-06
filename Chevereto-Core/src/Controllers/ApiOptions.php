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
 * Identical to OPTIONS.
 */
class ApiOptions extends Controller
{
    const OPTIONS = [
        'description' => 'Retrieve endpoint OPTIONS.',
    ];

    /** @var string */
    private $endpoint;

    /**
     * @param string $endpoint an API endpoint (/api)
     */
    public function __invoke(string $endpoint = null)
    {
        $route = $this->getApp()->getRoute();
        if (isset($route)) {
            $endpoint = $route->getKey();
        }
        if (!isset($endpoint)) {
            $this->handleError();

            return;
        }
        $this->endpoint = $endpoint;
        $this->process();
    }

    protected function handleError()
    {
        $this->getResponse()->setStatusCode(400);
        $message =
                (string)
                    (new Message('Must provide a %s argument when running this callable without route context.'))
                        ->code('%s', '$endpoint');
        if (CLI) {
            Console::io()->error($message);

            return;
        } else {
            throw new InvalidArgumentException($message);
        }
    }

    private function process()
    {
        $statusCode = 200;
        $apis = $this->getApis();
        $apiKey = $apis->getEndpointApiKey($this->endpoint);
        $endpointData = $apis->getBaseOptions($this->endpoint) ?? $apis->getEndpoint($this->endpoint);
        if ($endpointData) {
            $this->getResponse()->setMeta(['api' => $apiKey]);
            $this->getResponse()->addData('OPTIONS', $this->endpoint, $endpointData['OPTIONS']);
        } else {
            $statusCode = 404;
            // $json->setResponse("Endpoint doesn't exists", $statusCode);
        }
        $this->getResponse()->setStatusCode($statusCode);
    }
}
