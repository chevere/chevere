<?php

declare(strict_types=1);
/*
 * This file is part of Chevereto\Core.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Chevereto\Core;

use LogicException;

/**
 * Routes takes a bunch of Routes and generates a routing table (php array).
 */
class Router
{
    /**
     * @var array An array containing Route members (objects, serialized)
     *            fileHandle => [id => @Route],
     */
    protected $routes;

    /** @var array An array containing the named routes [name => [id, fileHandle]] */
    protected $namedRoutes;

    public function addRoute(Route $route, string $basename)
    {
        $id = $route->getId();
        if ($name = $route->getName()) {
            $namedRoute = $this->getNamedRoutes()[$name] ?? null;
            if (isset($namedRoute)) {
                throw new LogicException(
                    (string) (new Message('The route name %s has been already taken by %r.'))
                        ->code('%s', $name)
                        ->code('%r', $namedRoute[0].'@'.$namedRoute[1])
                );
            }
            $this->namedRoutes[$name] = [$route->getId(), $basename];
        }
        if (isset($this->routes[$basename][$id])) {
            throw new LogicException(
                (string) (new Message('Route %r has been already declared.'))
                    ->code('%r', $id)
            );
        }
        $this->routes[$basename][$id] = $route;
    }

    public function getRoutes(): array
    {
        return $this->routes ?? [];
    }

    public function getNamedRoutes(): array
    {
        return $this->namedRoutes ?? [];
    }
}

// Route::get('id@<fileHandle>');
