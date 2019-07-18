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
    private $uri;

    /** @var string */
    private $endpoint;

    public function __invoke()
    {
        $route = $this->getApp()->route;
        if (isset($route)) {
            $uri = $route->getUri();
        }
        if (!isset($uri)) {
            $this->handleError();

            return;
        }
        $this->uri = $uri;
        $this->endpoint = ltrim($this->uri, '/');
        $this->process();
    }

    protected function handleError()
    {
        $this->getResponse()->setStatusCode(400);
        $message =
            (string)
                (new Message('Must provide a %s argument when running this callable without route context.'))
                    ->code('%s', '$uri');
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
        $endpoint = $api->getEndpoint($this->endpoint);
        if ($endpoint['OPTIONS']) {
            $this->getResponse()->addData('OPTIONS', $this->uri, $endpoint['OPTIONS']);
        } else {
            $statusCode = 404;
            // $json->setResponse("Endpoint doesn't exists", $statusCode);
        }
        $this->getResponse()->setStatusCode($statusCode);
    }
}
