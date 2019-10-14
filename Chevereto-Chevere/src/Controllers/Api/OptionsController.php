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

use function console;

use InvalidArgumentException;
use Chevere\Message\Message;
use Chevere\Controller\Controller;

/**
 * Exposes API endpoint options.
 */
final class OptionsController extends Controller
{
    protected static $description = 'Retrieve endpoint OPTIONS.';

    /** @var string */
    private $path;

    /** @var string */
    private $endpoint;

    public function __invoke()
    {
        $route = $this->app()->route();
        if (isset($route)) {
            $path = $route->path();
        }
        if (!isset($path)) {
            $this->handleError();

            return;
        }
        $this->path = $path;
        $this->endpoint = ltrim($this->path, '/');
        $this->process();
    }

    private function handleError()
    {
        // $this->app()->response()->setStatusCode(400);
        $msg = 'Must provide a %s argument when running this callable without route context.';
        $message = (new Message($msg))->code('%s', '$path')->toString();
        if (CLI) {
            console()->style()->error($message);

            return;
        } else {
            throw new InvalidArgumentException($message);
        }
    }

    private function process()
    {
        // $statusCode = 200;
        // $endpoint = $this->app()->api()->endpoint($this->endpoint);
        // if ($endpoint['OPTIONS']) {
        //     $this->app()->response()->addData('OPTIONS', $this->path, $endpoint['OPTIONS']);
        // } else {
        //     $statusCode = 404;
        //     // $json->setResponse("Endpoint doesn't exists", $statusCode);
        // }
        // $this->app()->response()->setStatusCode($statusCode);
    }
}
