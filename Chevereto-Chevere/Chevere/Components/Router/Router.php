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

use Chevere\Components\Cache\CacheKey;
use Chevere\Components\Cache\Exceptions\CacheNotFoundException;
use Chevere\Components\File\Exceptions\FileNotFoundException;
use Chevere\Components\Message\Message;
use Chevere\Components\Router\Exception\RegexPropertyRequiredException;
use Chevere\Components\Router\Exception\RouteNotFoundException;
use Chevere\Components\Serialize\Unserialize;
use Chevere\Contracts\Cache\CacheContract;
use Chevere\Contracts\Route\RouteContract;
use Chevere\Contracts\Router\CacheKeysContract;
use Chevere\Contracts\Router\MakerContract;
use Chevere\Contracts\Router\RouterContract;
use TypeError;

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

    /** @var CacheContract */
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

    public function withCache(CacheContract $cache): RouterContract
    {
        $new = clone $this;
        $new->cache = $cache;
        try {
            $new->regex = $new->cache
                ->get(new CacheKey(CacheKeysContract::REGEX))
                ->raw();
            $new->routes = $new->cache
                ->get(new CacheKey(CacheKeysContract::ROUTES))
                ->raw();
            $new->routesIndex = $new->cache
                ->get(new CacheKey(CacheKeysContract::INDEX))
                ->raw();
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

    public function cache(): CacheContract
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
                ->code('%path%', '' != $pathInfo ? $pathInfo : '(empty string)')
                ->toString()
        );
    }

    private function resolver(array $matches): RouteContract
    {
        $id = $matches['MARK'];
        unset($matches['MARK']);
        array_shift($matches);
        $route = $this->routes[$id];
        // is string when the route is cached
        if (is_string($route)) {
            $unserialize = new Unserialize($route);
            $route = $unserialize->var();
            if (!($route instanceof RouteContract)) {
                throw new TypeError(
                    (new Message("Serialized variable doesn't implements %contract%, type %provided% provided"))
                        ->code('%contract%', RouteContract::class)
                        ->code('%provided%', $unserialize->type()->typeHinting())
                        ->toString()
                );
            }
            $this->routes[$id] = $route;
        }
        $this->arguments = [];
        if ($route->hasWildcardCollection()) {
            foreach ($matches as $pos => $val) {
                $wildcard = $route->wildcardCollection()->getPos($pos);
                $this->arguments[$wildcard->name()] = $val;
            }
        }

        return $route;
    }
}
