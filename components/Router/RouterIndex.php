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

use Chevere\Components\Route\Interfaces\RouteInterface;
use Chevere\Components\Router\Interfaces\RouteIdentifierInterface;
use Chevere\Components\Router\Interfaces\RouterIndexInterface;
use Ds\Map;
use OutOfBoundsException;

final class RouterIndex implements RouterIndexInterface
{
    private RouterIdentifiers $routerIdentifiers;

    /** @var Map [<string>routeName => <string>routePath,] */
    private Map $index;

    public function __construct()
    {
        $this->routerIdentifiers = new RouterIdentifiers;
        $this->index = new Map;
    }

    public function withAdded(RouteInterface $route, string $group): RouterIndexInterface
    {
        $new = clone $this;
        $routeName = $route->name()->toString();
        $routePath = $route->path()->toString();
        $new->index = $new->index->copy();
        $new->index->put($routeName, $routePath);
        $new->routerIdentifiers->put(
            $routePath,
            new RouteIdentifier(
                $group,
                $route->name()->toString()
            )
        );

        return $new;
    }

    public function has(string $routeName): bool
    {
        return $this->index->hasKey($routeName);
    }

    /**
     * @throws OutOfBoundsException
     */
    public function get(string $routeName): RouteIdentifierInterface
    {
        return $this->routerIdentifiers->get($this->index->get($routeName));
    }

    public function toArray(): array
    {
        $array = [];
        /** @var RouteIdentifierInterface $routeIdentifier */
        foreach ($this->routerIdentifiers->map() as $pathKey => $routeIdentifier) {
            $array[$pathKey] = $routeIdentifier->toArray();
        }

        return $array;
    }
}
