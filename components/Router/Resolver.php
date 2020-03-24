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

use Chevere\Components\Cache\CacheItem;
use Chevere\Components\Cache\CacheKey;
use Chevere\Components\Cache\Interfaces\CacheInterface;
use Chevere\Components\Message\Message;
use Chevere\Components\Route\Interfaces\RouteInterface;
use Chevere\Components\Router\Exceptions\RouteNotFoundException;
use Chevere\Components\Router\Exceptions\RouterException;
use Chevere\Components\Router\Interfaces\RoutedInterface;
use Chevere\Components\Router\Interfaces\RouterRegexInterface;
use Psr\Http\Message\UriInterface;
use Throwable;

final class Resolver
{
    private RouterRegexInterface $routerRegex;

    private CacheInterface $cache;

    public function __construct(
        RouterRegexInterface $routerRegex,
        CacheInterface $cache
    ) {
        $this->routerRegex = $routerRegex;
        $this->cache = $cache;
    }

    /**
     * Returns a RoutedInterface for the given UriInterface.
     *
     * @throws RouterException        if the router encounters any fatal error
     * @throws UnserializeException   if the route string object can't be unserialized
     * @throws TypeError              if the found route doesn't implement the RouteInterface
     * @throws RouteNotFoundException if no route resolves the given UriInterface
     */
    public function resolve(UriInterface $uri): RoutedInterface
    {
        try {
            if (preg_match($this->routerRegex->regex()->toString(), $uri->getPath(), $matches)) {
                return $this->resolver($matches);
            }
        } catch (Throwable $e) {
            throw new RouterException($e->getMessage(), $e->getCode(), $e);
        }
        throw new RouteNotFoundException(
            (new Message('No route found for %path%'))
                ->code('%path%', $uri->getPath())
                ->toString()
        );
    }

    /**
     * @throws RouteCacheNotFoundException
     */
    private function resolver(array $matches): RoutedInterface
    {
        $id = (string) $matches['MARK'];
        unset($matches['MARK']);
        array_shift($matches);
        $routeResolve = $this->cache->get(new CacheKey($id))->var();
        $routeName = $routeResolve->name();
        $routeWildcards = $routeResolve->wildcards();
        $wildcards = [];
        foreach ($routeWildcards as $pos => $val) {
            $wildcards[$routeWildcards->getPos($pos)->name()] = $val;
        }

        return new Routed($routeName, $wildcards);
    }
}
