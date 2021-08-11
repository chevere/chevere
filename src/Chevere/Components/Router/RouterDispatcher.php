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

namespace Chevere\Components\Router;

use Chevere\Components\Controller\ControllerName;
use Chevere\Components\Message\Message;
use Chevere\Exceptions\Core\LogicException;
use Chevere\Exceptions\Http\HttpMethodNotAllowedException;
use Chevere\Exceptions\Router\RouteNotFoundException;
use Chevere\Interfaces\Router\RoutedInterface;
use Chevere\Interfaces\Router\RouterDispatcherInterface;
use FastRoute\Dispatcher\GroupCountBased as Dispatcher;
use FastRoute\RouteCollector;

/**
 * @codeCoverageIgnore
 */
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
        switch ($info[0]) {
            default:
                throw new LogicException(
                    (new Message('Unexpected response code %code% from route dispatcher'))
                        ->code('%code%', $info[0])
                );
            case Dispatcher::NOT_FOUND:
                throw new RouteNotFoundException(
                    (new Message('No route found for %uri%'))
                        ->code('%uri%', $uri)
                );

                break;
            case Dispatcher::METHOD_NOT_ALLOWED:
                throw new HttpMethodNotAllowedException(
                    (new Message('Method %method% is not in the list of allowed methods: %allowed%'))
                        ->code('%method%', $httpMethod)
                        ->code('%allowed%', implode(', ', $info[1]))
                );

                break;
            case Dispatcher::FOUND:
                return new Routed(new ControllerName($info[1]), $info[2]);
        }
    }
}
