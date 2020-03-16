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

namespace Chevere\Components\Route;

use Chevere\Components\Http\Exceptions\MethodNotFoundException;
use Chevere\Components\Http\Interfaces\MethodControllerInterface;
use Chevere\Components\Http\Interfaces\MethodInterface;
use Chevere\Components\Message\Message;
use Chevere\Components\Route\Interfaces\RouteEndpointInterface;
use Chevere\Components\Route\Interfaces\RouteEndpointsInterface;
use SplObjectStorage;

final class RouteEndpoints implements RouteEndpointsInterface
{
    private SplObjectStorage $objects;

    /** @param array ['METHOD' => key,]*/
    private array $index = [];

    private int $count = -1;

    public function __construct(RouteEndpointInterface ...$routeEndpoint)
    {
        $this->objects = new SplObjectStorage();
        foreach ($routeEndpoint as $object) {
            $this->storeRouteEndpoint($object);
        }
    }

    public function withAddedRouteEndpoint(RouteEndpointInterface $routeEndpoint): RouteEndpointsInterface
    {
        $new = clone $this;
        $new->storeRouteEndpoint($routeEndpoint);

        return $new;
    }

    // public function getMethod(MethodInterface $method): MethodControllerInterface
    // {
    //     $pos = array_search($method::name(), $this->index);
    //     if (false === $pos) {
    //         throw new MethodNotFoundException(
    //             (new Message('Method %method% not found'))
    //                 ->code('%method%', $method::name())
    //                 ->toString()
    //         );
    //     }
    //     $this->objects->rewind();
    //     for ($i = 0; $i < $pos; $i++) {
    //         $this->objects->next();
    //     }

    //     return $this->objects->current();
    // }

    public function objects(): RouteEndpointObjectsRead
    {
        return new RouteEndpointObjectsRead($this->objects);
    }

    private function storeRouteEndpoint(RouteEndpointInterface $routeEndpoint): void
    {
        $name = $routeEndpoint->method()::name();
        $pos = array_search($name, $this->index);
        if (false !== $pos) {
            $this->objects->attach(
                $routeEndpoint,
                $pos
            );
            $this->index[$pos] = $name;

            return;
        }
        $this->count++;
        $this->objects->attach($routeEndpoint, $this->count);
        $this->index[$this->count] = $name;
    }
}
