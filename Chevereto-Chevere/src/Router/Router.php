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

namespace Chevere\Router;

use Chevere\Message;
use Chevere\Cache\Cache;
use Chevere\Contracts\Route\RouteContract;
use Chevere\Contracts\Router\RouterContract;
use Chevere\Router\Exception\RouteNotFoundException;

/**s
 * Routes takes a bunch of Routes and generates a routing table (php array).
 */
final class Router implements RouterContract
{
    const REGEX_TEPLATE = '#^(?%s)$#x';

    /** @var string Regex representation, used when resolving routing */
    private $regex;

    /** @var array Route members (objects, serialized) [id => Route] */
    private $routes;

    /** @var array Contains ['/path' => [id, 'route/key']] */
    private $routesIndex;

    /** @var array Arguments taken from wildcard matches */
    private $arguments;

    public function __construct(Maker $maker = null)
    {
        if (isset($maker)) {
            $this->regex = $maker->regex();
            $this->routes = $maker->routes();
            $this->routesIndex = $maker->routesIndex();
            $maker->setcache();
        } else {
            $cache = new Cache('router');
            $this->regex = $cache->get('regex')->raw();
            $this->routes = $cache->get('routes')->raw();
            $this->routesIndex = $cache->get('routesIndex')->raw();
        }
    }

    public function arguments(): array
    {
        return $this->arguments ?? [];
    }

    /**
     * {@inheritdoc}
     */
    public function resolve(string $pathInfo): RouteContract
    {
        if (preg_match($this->regex, $pathInfo, $matches)) {
            return $this->resolver($matches);
        }
        throw new RouteNotFoundException(
            (new Message('No route defined for %s'))
                ->code('%s', $pathInfo)
                ->toString()
        );
    }

    private function resolver(array $matches): RouteContract
    {
        $id = $matches['MARK'];
        unset($matches['MARK']);
        array_shift($matches);
        $route = $this->routes[$id];
        // Array when the route is a powerSet [id, set]
        if (is_array($route)) {
            $set = $route[1];
            $route = $this->routes[$route[0]];
        }
        if (is_string($route)) {
            $resolver = new Resolver($route);
            $route = $resolver->get();
            $this->routes[$id] = $route;
        }
        $this->arguments = [];
        if (isset($set)) {
            foreach ($matches as $k => $v) {
                $wildcardId = $route->keyPowerSet()[$set][$k];
                $wildcardName = $route->wildcardName($wildcardId);
                $this->arguments[$wildcardName] = $v;
            }
        }

        return $route;
    }
}
