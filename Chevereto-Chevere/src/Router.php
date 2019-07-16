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

namespace Chevereto\Chevere;

use LogicException;

/**
 * Routes takes a bunch of Routes and generates a routing table (php array).
 */
class Router
{
    const PRIORITY_ORDER = [Route::TYPE_STATIC, Route::TYPE_MIXED, Route::TYPE_DYNAMIC];

    const ID = 'id';
    const SET = 'set';

    /**
     * @var ?array An array containing Route members (objects, serialized) fileHandle => [id => @Route],
     */
    public $routes;

    /** @var ?array Contains ['/route/key' => [id, 'route/key']] */
    public $routeKeys;

    /** @var ?array An array containing the named routes [name => [id, fileHandle]] */
    public $namedRoutes;

    /** @var ?array An array containing a mapped representation, used when resolving routing. */
    public $routing;

    /** @var ?array Arguments taken from wildcard matches. */
    public $arguments;

    public function addRoute(Route $route, string $basename)
    {
        $route->fill();
        $id = $route->getId();
        $uri = $route->getUri();
        $this->handleRouteKey($uri);
        $pointer = [$id, $basename];
        $name = $route->getName();
        $this->handleRouteName($name, $pointer);
        $this->routes[$basename][$id] = $route;
        $powerSet = $route->getPowerSet();
        if (isset($powerSet)) {
            foreach ($powerSet as $k => $wildcardsIndex) {
                // n => .. => regex => [route, wildcards]
                $this->routing($pointer, $route, $k); // $route->regex($k)
            }
        } else {
            // n => .. => regex => route
            $this->routing($pointer, $route);
        }
        ksort($this->routing);
        $this->routeKeys[$uri] = $pointer;
    }

    protected function handleRouteKey(string $key)
    {
        $keyedRoute = $this->routeKeys[$key] ?? null;
        if (isset($keyedRoute)) {
            throw new LogicException(
                (string) (new Message('Route key %s has been already declared by %r.'))
                    ->code('%s', $key)
                    ->code('%r', $keyedRoute[0].'@'.$keyedRoute[1])
            );
        }
    }

    protected function handleRouteName(?string $name, array $pointer)
    {
        if (isset($name)) {
            $namedRoute = $this->namedRoutes[$name] ?? null;
            if (isset($namedRoute)) {
                throw new LogicException(
                    (string) (new Message('Route name %s has been already taken by %r.'))
                        ->code('%s', $name)
                        ->code('%r', $namedRoute[0].'@'.$namedRoute[1])
                );
            }
            $this->namedRoutes[$name] = $pointer;
        }
    }

    /**
     * Group a Route into the routing table.
     *
     * @param array  $pointer  Route pointer [id, handle]
     * @param Route  $route    route object
     * @param string $routeSet route set, used when dealing with a powerSet
     */
    protected function routing(array $pointer, Route $route, ?string $routeSet = null): void
    {
        $routing = new Routing($route);
        $var = [static::ID => $pointer];
        if ($routeSet) {
            $var[static::SET] = $routing->routeSetHandle;
        }
        $this->routing[$routing->count][$routing->type][$routing->regex] = $var;
    }

    /**
     * Resolve routing for the given path info.
     *
     * @param string $pathInfo request path
     */
    public function resolve(string $pathInfo): ?Route
    {
        $requestTrim = ltrim($pathInfo, '/');
        $components = $this->getComponents($requestTrim);
        $componentsCount = count($components);
        foreach (static::PRIORITY_ORDER as $priority) {
            $routesTable = $this->getRoutesTable($componentsCount, $priority);
            if (null === $routesTable) {
                continue;
            }
            foreach ($routesTable as $regex => $prop) {
                if (preg_match("#$regex#", $pathInfo, $matches)) {
                    array_shift($matches);
                    $this->arguments = $matches;
                    $pointer = $prop[static::ID];
                    $routeSome = $this->routes[$pointer[1]][$pointer[0]] ?? null;
                    $routerResolver = new RouterResolver($routeSome, $pointer);
                    if ($routerResolver->isUnserialized) {
                        $this->routes[$pointer[1]][$pointer[0]] = $routerResolver->get();
                    }

                    return $routerResolver->get();
                }
            }
        }

        return null;
    }

    protected function getComponents(string $requestTrim): array
    {
        return null == $requestTrim ? [] : explode('/', $requestTrim);
    }

    protected function getRoutesTable(int $key, string $priority): ?array
    {
        return $this->routing[$key][$priority] ?? null;
    }
}
