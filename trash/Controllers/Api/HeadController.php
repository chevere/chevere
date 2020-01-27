<?php

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevere.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Chevere\Components\Controllers\Api;

use Chevere\Components\Controller\Controller;
use Chevere\Components\Http\Method;
use Chevere\Components\Route\Route;

/**
 * Identical to GET, but without any message-boby in the response.
 */
final class HeadController extends Controller
{
    protected static $description = 'GET without message-body.';

    /** @var Route */
    private $route;

    public function __invoke(?string $endpoint = null)
    {
        // if (isset($endpoint)) {
        //     $route = $this->app()->services()->router()->resolve($endpoint);
        // } else {
        //     $route = $this->app()->route();
        //     if (!isset($route)) {
        //         $msg = 'Must provide the %s argument when running this callable without route context.';
        //         $message = (new Message($msg))->code('%s', '$endpoint')->toString();
        //         if (CONSOLE) {
        //             console()->style()->error($message);

        //             return;
        //         } else {
        //             throw new InvalidArgumentException($message);
        //         }
        //     }
        // }

        if (!isset($route)) {
            // $this->app()->response()->withStatusCode(404);

            return;
        }

        $this->route = $route;

        $this->process();
    }

    private function process()
    {
        $controller = $this->route->controllerName(new Method('GET'))->toString();
        $this->app()->run($controller);
        // $this->app()->response()->setContent(null);
        // if (CLI) {
        //     Console::style()->block($this->app()->response()->statusString(), 'STATUS', 'fg=black;bg=green', ' ', true);
        // }
    }
}
