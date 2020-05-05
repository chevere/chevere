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

use Chevere\Components\Message\Message;
use Chevere\Components\Router\Exceptions\RouteNotFoundException;
use Chevere\Components\Router\Exceptions\RouterException;
use Chevere\Components\Router\Interfaces\ResolverInterface;
use Chevere\Components\Router\Interfaces\RoutedInterface;
use Chevere\Components\Router\Interfaces\RouteResolvesCacheInterface;
use Chevere\Components\Router\Interfaces\RouterRegexInterface;
use OutOfBoundsException;
use Psr\Http\Message\UriInterface;
use Throwable;

//
final class Resolver implements ResolverInterface
{
    private RouterRegexInterface $routerRegex;

    private RouteResolvesCacheInterface $resolverCache;

    public function __construct(
        RouterRegexInterface $routerRegex,
        RouteResolvesCacheInterface $routeResolveCache
    ) {
        $this->routerRegex = $routerRegex;
        $this->resolverCache = $routeResolveCache;
    }

    /**
     * Returns a RoutedInterface for the given UriInterface.
     *
     * @throws RouterException        if the router encounters any fatal error (UnserializeException, TypeError, etc)
     * @throws RouteNotFoundException if no route resolves the given UriInterface
     */
    public function resolve(UriInterface $uri): RoutedInterface
    {
        try {
            if (preg_match($this->routerRegex->regex()->toString(), $uri->getPath(), $matches)) {
                return $this->resolver($matches);
            }
        } catch (Throwable $e) {
            throw new RouterException(new Message($e->getMessage()), $e->getCode(), $e);
        }
        throw new RouteNotFoundException(
            (new Message('No route found for %uriPath%'))
                ->code('%uriPath%', $uri->getPath())
        );
    }

    /**
     * @throws OutOfBoundsException if no cache for matched tag id
     * @throws TypeError if the cache var doesn't match the expected type
     */
    private function resolver(array $matches): RoutedInterface
    {
        $idString = (string) $matches['MARK'];
        $idInt = (int) $idString;
        unset($matches['MARK']);
        array_shift($matches);
        if (!$this->resolverCache->has($idInt)) {
            throw new OutOfBoundsException(
                (new Message('No cache for regex tag id %id%'))
                    ->code('%id%', $idString)
                    ->toString()
            );
        }
        $routeResolve = $this->getRouteResolve($idInt);
        $arguments = [];
        foreach ($matches as $pos => $val) {
            $arguments[$routeResolve->wildcards()->getPos($pos)->name()] = $val;
        }

        return new Routed($routeResolve->name(), $arguments);
    }

    private function getRouteResolve(int $id): RouteResolve
    {
        return $this->resolverCache->get($id);
    }
}
