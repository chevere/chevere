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
use Chevere\Components\Router\RouteParsers\StrictStd;
use Chevere\Exceptions\Core\LogicException;
use Chevere\Exceptions\Router\RouterException;
use Chevere\Interfaces\Router\RoutableInterface;
use Chevere\Interfaces\Router\RoutablesInterface;
use Chevere\Interfaces\Router\RoutedInterface;
use Chevere\Interfaces\Router\RouterIndexInterface;
use Chevere\Interfaces\Router\RouterInterface;
use FastRoute\DataGenerator\GroupCountBased as DataGenerator;
use FastRoute\Dispatcher\GroupCountBased as Dispatcher;
use FastRoute\RouteCollector;

final class Router implements RouterInterface
{
    private RouterIndexInterface $index;

    private RoutablesInterface $routables;

    private RouteCollector $routeCollector;

    public function __construct()
    {
        $this->index = new RouterIndex;
        $this->routables = new Routables;
        $this->routeCollector = new RouteCollector(new StrictStd, new DataGenerator);
    }

    public function withAddedRoutable(RoutableInterface $routable, string $group): RouterInterface
    {
        $new = clone $this;
        $route = $routable->route();
        $new->index = $new->index->withAdded($routable, $group);
        $new->routables = $new->routables->withPut($routable);
        foreach ($route->endpoints()->getGenerator() as $endpoint) {
            $new->routeCollector->addRoute(
                $endpoint->method()::name(),
                $route->path()->toString(),
                get_class($endpoint->controller())
            );
        }

        return $new;
    }

    public function index(): RouterIndexInterface
    {
        return $this->index;
    }

    public function routables(): RoutablesInterface
    {
        return $this->routables;
    }

    public function dispatch(string $httpMethod, string $uri): RoutedInterface
    {
        $info = (new Dispatcher($this->routeCollector->getData()))
            ->dispatch($httpMethod, $uri);
        switch ($info[0]) {
            case Dispatcher::NOT_FOUND:
                throw new RouterException(
                    (new Message('Not found')),
                    Dispatcher::NOT_FOUND
                );
                break;
            case Dispatcher::METHOD_NOT_ALLOWED:
                throw new RouterException(
                    (new Message('Method %method% is not in the list of allowed methods: %allowed%'))
                        ->code('%method%', $httpMethod)
                        ->code('%allowed%', implode(', ', $info[1])),
                    Dispatcher::METHOD_NOT_ALLOWED
                );
                break;
            case Dispatcher::FOUND:
                return new Routed(new ControllerName($info[1]), $info[2]);
                break;
        }
        throw new LogicException(
            (new Message('Unexpected response code %code% from route dispatcher'))
                ->code('%code%', $info[0])
        );
    }
}
