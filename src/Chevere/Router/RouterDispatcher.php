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

namespace Chevere\Router;

use Chevere\Controller\ControllerName;
use Chevere\Http\Exceptions\HttpMethodNotAllowedException;
use Chevere\Message\Message;
use Chevere\Router\Exceptions\RouteNotFoundException;
use Chevere\Router\Interfaces\RoutedInterface;
use Chevere\Router\Interfaces\RouterDispatcherInterface;
use FastRoute\Dispatcher\GroupCountBased as Dispatcher;
use FastRoute\RouteCollector;

final class RouterDispatcher implements RouterDispatcherInterface
{
    public function __construct(
        private RouteCollector $routeCollector
    ) {
    }

    public function dispatch(string $httpMethod, string $uri): RoutedInterface
    {
        $info = (new Dispatcher($this->routeCollector->getData()))
            ->dispatch($httpMethod, $uri);

        return match ($info[0]) {
            Dispatcher::NOT_FOUND =>
                throw new RouteNotFoundException(
                    (new Message('No route found for %uri%'))
                        ->code('%uri%', $uri)
                ),
            Dispatcher::FOUND => new Routed(new ControllerName($info[1]), $info[2]),
            Dispatcher::METHOD_NOT_ALLOWED =>
                throw new HttpMethodNotAllowedException(
                    (new Message('Method %method% is not in the list of allowed methods: %allowed%'))
                        ->code('%method%', $httpMethod)
                        ->code('%allowed%', implode(', ', $info[1]))
                )
        };
    }
}
