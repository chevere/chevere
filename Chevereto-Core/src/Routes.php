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

/**
 * Routes is an auxiliar Route class which stores the constructed Route objects.
 * Used to generate the routing array (simple array) for faster routing process.
 */
class Routes
{
    use Traits\InstanceTrait;

    const KEY_ROUTE_ID = 'id';
    const KEY_ROUTE_SET = 'set';

    // Properties used to collect the collection
    protected $routes = []; // [id => (serialized) Route,]
    protected $uniques = []; // [route => id,]
    protected $named = []; // [name => id,]
    protected $routing = []; // The whole thing, used when resolving routing.
    // protected $routesSerialized = []; // Same as #routes, but serialized for cache.

    public function __construct()
    {
        // dd(\debug_backtrace());
        static::$instance = $this;
    }

    /**
     * Get Route objects.
     *
     * @return array Array containing Route objects [id => Route,]
     */
    public function getRoutes(): array
    {
        return $this->routes;
    }

    /**
     * Get uniques table.
     *
     * @return array Array containing Route objects [route => id,]
     */
    public function getUniques(): array
    {
        return $this->uniques;
    }

    /**
     * Get named table.
     *
     * @return mixed Array containing named routes [name => route]
     */
    public function getNamed()
    {
        return $this->named;
    }

    /**
     * Get routing table.
     *
     * @return array array containing routing table
     */
    public function getRouting(): ?array
    {
        return $this->routing;
    }

    /**
     * Clears the Routes static properties.
     */
    public function clear(): void
    {
        $this->routes = [];
        $this->uniques = [];
        $this->named = [];
        $this->routing = [];
    }

    /**
     * Allocate Route object in the Routes instance.
     */
    public function allocate(Route $route): void
    {
        if ($route->getId() === null) {
            $this->routes[] = $route;
            end($this->routes);
            $route->setId(key($this->routes));
            $this->uniques[$route->getKey()] = $route->getId();
        } else {
            $this->routes[$route->getId()] = $route;
        }
        if (null != $route->getName()) {
            $this->named[$route->getName()] = $route->getId();
        }
    }

    /**
     * Generate routing and routesSerialized arrays.
     */
    public function process(): void
    {
        if ($this->routes == false) {
            throw new RoutesException(
                (new Message('Unable to process routing due to empty %s table.'))
                    ->code('%s', __CLASS__)
            );
        }
        foreach ($this->routes as $r => $route) {
            // Make sure to fill all the emptyness
            $route->fill();
            // Use $route->powerSet when needed
            if ($route->getPowerSet() != null) {
                foreach ($route->getPowerSet() as $k => $wildcardsIndex) {
                    // n => .. => regex => [route, wildcards]
                    $this->group($route, $k); // $route->regex($k)
                }
            } else {
                // n => .. => regex => route
                $this->group($route);
            }
        }
        ksort($this->routing);
    }

    /**
     * Group a Route into the routing table.
     *
     * @param Route  $route    route instance
     * @param string $routeSet route set, used when dealing with powerSet
     */
    protected function group(Route $route, string $routeSet = null): void
    {
        if ($routeSet) {
            $routeSetHandle = $routeSet;
            $regex = $route->regex($routeSetHandle);
        } else {
            $routeSetHandle = $route->getSet() ?? $route->getKey();
            $regex = $route->regex();
        }
        // Determine grouping type (static, mixed, dynamic)
        if ($route->getSet() == null) {
            $type = Route::TYPE_STATIC;
        } else {
            if ($routeSetHandle != null) {
                $pregReplace = preg_replace('/{[0-9]+}/', '', $routeSetHandle);
                if ($pregReplace != null) {
                    $pregReplace = trim(Path::normalize($pregReplace), '/');
                }
            }
            $type = isset($pregReplace) ? Route::TYPE_MIXED : Route::TYPE_DYNAMIC;
        }
        if ($routeSetHandle != null) {
            $routeSetHandleTrim = ltrim($routeSetHandle, '/');
            $explode = explode('/', $routeSetHandleTrim);
            $count = $route->getKey() == '/' ? 0 : count($explode);
        } else {
            $count = 0;
        }
        $var = [static::KEY_ROUTE_ID => $route->getId()];
        if ($routeSet) {
            $var[static::KEY_ROUTE_SET] = $routeSetHandle;
        }
        $this->routing[$count][$type][$regex] = $var;
    }

    /**
     * Get a route object from its id.
     *
     * @param mixed $identifier   Int Route Id; String Route name, (opt Route string)
     * @param bool  $searchUnique true to search for route object on $this->uniques
     */
    protected function getRoute($identifier, bool $searchUnique = false): Route
    {
        // Determine the object id
        if (is_numeric($identifier)) {
            $id = $identifier;
        } else {
            $search = $searchUnique ? $this->uniques : $this->named;
            $id = $search[$identifier] ?? null;
            if ($id === null) {
                throw new RoutesException(
                    (new Message("Object specified by %s doesn't exists in the collection."))
                        ->code('%s', $identifier)
                );
            }
        }

        return $this->routes[$id];
    }

    /**
     * Removes a route from the collection.
     *
     * @param mixed $identifier int Route Id; String Route name
     *
     * @throws RoutesException if route doesn't exists
     */
    protected function removeRoute($identifier, bool $searchUnique = false): bool
    {
        $route = $this->getRoute(...func_get_args());
        unset(
            $this->routes[$route->getId()],
            $this->uniques[$route->getKey()],
            $this->named[$route->getName()]
        );
        // Re-index needed
        foreach ($this->routes as $k => &$route) {
            if ($k !== $route->getId()) {
                $route->setId($k);
            }
        }

        return true;
    }
}
class RoutesException extends Exception
{
}
