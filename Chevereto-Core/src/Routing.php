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

use Exception;
use Symfony\Component\HttpFoundation\Request;

/**
 * This class is part of the Router thing and is used to enable a OOP way to access the Routing table.
 */
class Routing
{
    const PRIORITY_ORDER = [Route::TYPE_STATIC, Route::TYPE_MIXED, Route::TYPE_DYNAMIC];

    protected $route;
    protected $routes;
    protected $id;
    protected $matches = [];
    protected $set;

    public function getRoutes(): Routes
    {
        return $this->routes;
    }

    public function getRoute(): Route
    {
        return $this->route;
    }

    /**
     * Creates a new Routing object.
     *
     * @param Routes $routes a Routes object
     */
    public function __construct(Routes $routes)
    {
        $this->routes = $routes;
    }

    /**
     * Get a routing group.
     *
     * @param int $id the group id registered in the routing group table
     */
    protected function getGroup(int $id): ?array
    {
        $group = $this->getRoutes()->getRouting()[$id];
        if ($group == null) {
            throw new RoutingException(
                (new Message("Routing group %s doesn't exists in %c."))
                    ->code('%s', $id)
                    ->code('%c', __CLASS__)
            );
        }

        return $group;
    }

    /**
     * Get a route by its id.
     *
     * @param int $id the Route id registered in the routing flat table
     */
    protected function getRouteById(int $id): Route
    {
        $routeSome = $this->getRoutes()->getRoutes()[$id];
        $route = is_object($routeSome) ? $routeSome : unserialize($routeSome, ['allowed_classes' => [__NAMESPACE__.'\Route']]);
        if ($route instanceof Route) {
            return $route;
        } else {
            throw new RoutingException(
                (new Message("Route identified by id %s doesn't exists in %c."))
                    ->code('%s', $id)
                    ->code('%c', __CLASS__)
            );
        }
    }

    /**
     * Resolve routing for the given path info.
     *
     * @param string $pathInfo      request path
     * @param array  $priorityOrder resolution priority order
     */
    protected function resolve(string $pathInfo, array $priorityOrder): ?Route
    {
        $requestTrim = ltrim($pathInfo, '/');
        $components = $requestTrim == null ? [] : explode('/', $requestTrim);
        $componentsCount = count($components);
        foreach ($priorityOrder as $type) {
            $RoutesTable = $this->getRoutes()->getRouting()[$componentsCount][$type] ?? null;
            if ($RoutesTable === null) {
                continue;
            }
            foreach ($RoutesTable as $regex => $prop) {
                if (preg_match("#$regex#", $pathInfo, $matches)) {
                    array_shift($matches);
                    $this->matches = $matches ?? null;
                    $this->id = $prop[Routes::KEY_ROUTE_ID] ?? null;
                    $this->set = $prop[Routes::KEY_ROUTE_SET] ?? null;
                    $this->route = $this->getRouteById($this->id);

                    return $this->route;
                }
            }
        }

        return null;
    }

    /**
     * Get wildcard matches.
     */
    protected function getWildcardMatches(): ?array
    {
        if ($this->route == null || $this->matches == null || $this->route->getWildcards() == null) {
            return null;
        }
        if ($this->set !== null) {
            $routePowerSet = $this->route->getPowerSet();
            if ($routePowerSet == null) {
                throw new RoutingException(
                    (new Message('Unable to handle a %s since %p is null.'))
                        ->code('%s', $this->set)
                        ->code('%p', 'powerSet')
                );
            }
            $map = $routePowerSet[$this->set];
        } else {
            $map = array_keys($this->route->getWildcards() ?? []);
        }
        $return = array_flip($this->route->getWildcards());
        $return = array_fill_keys(array_keys($return), null);
        foreach ($this->matches as $k => $v) {
            $return[$this->route->getWildcards()[$map[$k]]] = $v;
        }

        return $return;
    }

    /**
     * Get controller filepath who satisfy the Request from the Routes.
     *
     * @param Request $request request object
     */
    public function getController(Request $request): ?string
    {
        $pathInfo = $request->getPathInfo();
        // Fix pathInfo extra slashes
        $pathInfoTrim = rtrim($pathInfo, '/') ?: '/';
        // TODO: Find a way to set canonical header
        // if ($pathInfo !== $pathInfoTrim) {
        //     if (headers_sent() == false) {
        //         // header('Link: <http://127.0.0.1/Core/>; rel="canonical"');
        //     }
        // }
        try {
            if ($route = $this->resolve($pathInfoTrim, static::PRIORITY_ORDER)) {
                return $route->getCallable($request->getMethod());
            } else {
                throw new Exception();
            }
        } catch (Exception $e) {
            // Wrap these 404
            throw new RouterException('Not found', 404);
        }
    }

    /**
     * Get controller arguments [name => value].
     * TODO: Reflect controller, determine the actual passed values.
     */
    public function getArguments(): ?array
    {
        return $this->getWildcardMatches();
    }

    // public function getRouting() : ?self
    // {
    //     return $this;
    // }
}
class RoutingException extends Exception
{
}
