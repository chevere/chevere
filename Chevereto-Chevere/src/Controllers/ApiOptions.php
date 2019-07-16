<?php

declare(strict_types=1);
/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Chevereto\Chevere\Controllers;

use function Chevereto\Chevere\dd;
use const Chevereto\Chevere\CLI;
use Chevereto\Chevere\Console;
use Chevereto\Chevere\Message;
use Chevereto\Chevere\Controller;
use InvalidArgumentException;

/**
 * Identical to OPTIONS.
 */
class ApiOptions extends Controller
{
    protected static $description = 'Retrieve endpoint OPTIONS.';

    /** @var string */
    private $endpoint;

    /**
     * @param string $endpoint an API endpoint (/api)
     */
    public function __invoke()
    {
        $route = $this->getApp()->route;
        if (isset($route)) {
            $endpoint = $route->getUri();
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
        $api = $this->getApi();
        $apiKey = $api->getEndpointApiKey($this->endpoint);
        dd($apiKey);
        $endpointData = $api->getBaseOptions($this->endpoint) ?? $api->getEndpoint($this->endpoint);
        if ($endpointData) {
            // $this->getResponse()->setMeta(['api' => $apiKey]);
            $this->getResponse()->addData('OPTIONS', $this->endpoint, $endpointData['OPTIONS']);
        } else {
            $statusCode = 404;
            // $json->setResponse("Endpoint doesn't exists", $statusCode);
        }
        $this->getResponse()->setStatusCode($statusCode);
    }
}
