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

namespace Chevere\Controllers\Api;

use const Chevere\CLI;
use Chevere\Console\Console;
use Chevere\Api\Api;
use Chevere\Message;
use Chevere\Controller\Controller;
use InvalidArgumentException;

/**
 * Exposes API endpoint options.
 */
final class OptionsController extends Controller
{
    protected static $description = 'Retrieve endpoint OPTIONS.';

    /** @var string */
    private $uri;

    /** @var string */
    private $endpoint;

    public function __invoke()
    {
        $route = $this->app->route;
        if (isset($route)) {
            $uri = $route->uri;
        }
        if (!isset($uri)) {
            $this->handleError();

            return;
        }
        $this->uri = $uri;
        $this->endpoint = ltrim($this->uri, '/');
        $this->process();
    }

    private function handleError()
    {
        $this->response->setStatusCode(400);
        $msg = 'Must provide a %s argument when running this callable without route context.';
        $message = (new Message($msg))->code('%s', '$uri')->toString();
        if (CLI) {
            Console::cli()->out->error($message);

            return;
        } else {
            throw new InvalidArgumentException($message);
        }
    }

    private function process()
    {
        $statusCode = 200;
        $endpoint = $this->app->api->endpoint($this->endpoint);
        if ($endpoint['OPTIONS']) {
            $this->app->response->addData('OPTIONS', $this->uri, $endpoint['OPTIONS']);
        } else {
            $statusCode = 404;
            // $json->setResponse("Endpoint doesn't exists", $statusCode);
        }
        $this->response->setStatusCode($statusCode);
    }
}
