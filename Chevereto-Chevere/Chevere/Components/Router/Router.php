<?php

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Chevere\Components\Router;

use Chevere\Components\Cache\Cache;
use Chevere\Components\Cache\Exceptions\CacheNotFoundException;
use Chevere\Components\File\Exceptions\FileNotFoundException;
use Chevere\Components\Message\Message;
use Chevere\Components\Router\Exception\RegexPropertyRequiredException;
use Chevere\Components\Router\Exception\RouteNotFoundException;
use Chevere\Contracts\Route\RouteContract;
use Chevere\Contracts\Router\MakerContract;
use Chevere\Contracts\Router\RouterContract;

/**s
 * Routes takes a bunch of Routes and generates a routing table (php array).
 */
final class Router implements RouterContract
{
    /** @var string Regex representation, used when resolving routing */
    private $regex;

    /** @var array Route members (objects, serialized) [id => Route] */
    private $routes;

    /** @var array Contains ['/path' => [id, 'route/key']] */
    private $routesIndex;

    /** @var array Arguments taken from wildcard matches */
    private $arguments;

    /** @var Cache */
    private $cache;

    /** @var MakerContract */
    private $maker;

    public function withMaker(MakerContract $maker): RouterContract
    {
        $new = clone $this;
        $new->maker = $maker;
        $new->regex = $new->maker->regex();
        $new->routes = $new->maker->routes();
        $new->routesIndex = $new->maker->routesIndex();

        return $new;
    }

    public function withCache(Cache $cache): RouterContract
    {
        $new = clone $this;
        $new->cache = $cache;
        try {
            $new->regex = $new->cache->get(CacheKeys::REGEX)->return();
            $new->routes = $new->cache->get(CacheKeys::ROUTES)->return();
            $new->routesIndex = $new->cache->get(CacheKeys::ROUTES_INDEX)->return();
        } catch (FileNotFoundException $e) {
            throw new CacheNotFoundException($e->getMessage(), $e->getCode(), $e);
        }
        return $new;
    }

    public function hasMaker(): bool
    {
        return isset($this->maker);
    }

    public function hasCache(): bool
    {
        return isset($this->cache);
    }

    public function maker(): MakerContract
    {
        return $this->maker;
    }

    public function cache(): Cache
    {
        return $this->cache;
    }

    public function arguments(): array
    {
        return $this->arguments ?? [];
    }

    public function canResolve(): bool
    {
        return isset($this->regex);
    }

    public function resolve(string $pathInfo): RouteContract
    {
        if (!$this->canResolve()) {
            throw new RegexPropertyRequiredException(
                (new Message('Instance of %className% requires a %property% property when calling %method%'))
                    ->code('%className%', __CLASS__)
                    ->code('%property%', 'regex')
                    ->code('%method%', __METHOD__)
                    ->toString()
            );
        }
        if (preg_match($this->regex, $pathInfo, $matches)) {
            return $this->resolver($matches);
        }
        throw new RouteNotFoundException(
            (new Message('No route defined for %path%'))
                ->code('%path%', $pathInfo != '' ? $pathInfo : '(empty string)')
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
            $route = $resolver->route();
            $this->routes[$id] = $route;
        }
        $this->arguments = [];
        if (isset($set)) {
            foreach ($matches as $k => $v) {
                dd(__LINE__);
                // $wildcardId = $route->keyPowerSet()[$set][$k];
                $wildcardName = $route->wildcardName($wildcardId);
                $this->arguments[$wildcardName] = $v;
            }
        }

        return $route;
    }
}
