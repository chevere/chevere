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

use Chevere\Components\Cache\CacheKey;
use Chevere\Components\Message\Message;
use Chevere\Components\Router\Exceptions\RouteNotFoundException;
use Chevere\Components\Router\Exceptions\RouterException;
use Chevere\Components\Router\Interfaces\RoutedInterface;
use Chevere\Components\Router\Interfaces\RouterRegexInterface;
use OutOfBoundsException;
use Psr\Http\Message\UriInterface;
use Throwable;

final class Resolver
{
    private RouterRegexInterface $routerRegex;

    private ResolveCache $resolveCache;

    public function __construct(
        RouterRegexInterface $routerRegex,
        ResolveCache $resolveCache
    ) {
        $this->routerRegex = $routerRegex;
        $this->resolveCache = $resolveCache;
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
     * @throws OutOfBoundsException if no cache for matched tag id
     * @throws TypeError if the cache var doesn't match the expected type
     */
    private function resolver(array $matches): RoutedInterface
    {
        $id = $matches['MARK'];
        unset($matches['MARK']);
        array_shift($matches);
        if (!$this->resolveCache->has($id)) {
            throw new OutOfBoundsException(
                (new Message('No cache for regex tag id %id%'))
                    ->code('%id%', (string) $id)
                    ->toString()
            );
        }
        $routeResolve = $this->getRouteResolve($id);
        $name = $routeResolve->name();
        $routeWildcards = $routeResolve->routeWildcards();
        $arguments = [];
        foreach ($matches as $pos => $val) {
            $arguments[$routeWildcards->getPos($pos)->name()] = $val;
        }

        return new Routed($name, $arguments);
    }

    private function getRouteResolve(int $id): RouteResolve
    {
        return $this->resolveCache->get($id);
    }
}
