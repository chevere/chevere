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

use Chevere\Components\Router\Exceptions\RoutePathExistsException;
use Chevere\Components\Message\Message;
use Chevere\Components\Regex\Regex;
use Chevere\Components\Router\Exceptions\RouteKeyConflictException;
use Chevere\Components\Router\Exceptions\RouteNameConflictException;
use Chevere\Components\Router\Exceptions\RouterMakerException;
use Chevere\Components\Route\Interfaces\RouteInterface;
use Chevere\Components\Router\Interfaces\RouteableInterface;
use Chevere\Components\Router\Interfaces\RouteCacheInterface;
use Chevere\Components\Router\Interfaces\RouterCacheInterface;
use Chevere\Components\Router\Interfaces\RouterInterface;
use Chevere\Components\Router\Interfaces\RouterMakerInterface;
use Chevere\Components\Router\Interfaces\RouterRegexInterface;

/**
 * RouterMaker takes a bunch of routes and generates a cache-ready routing table.
 */
final class RouterMaker implements RouterMakerInterface
{
    private RouterCacheInterface $routerCache;

    private RouteCacheInterface $routeCache;

    private RouterInterface $router;

    /** @var array [(string) $pathUri => (int) $id] */
    private array $paths;

    /** @var array [(string) $pathUriKey => (int) $id] */
    private array $keys;

    /** @var array [(string) $name => (int) $id] */
    private array $named;

    /** @var array [(int) $id => (string) $regex,] */
    private array $regexes;

    /** @var array [(int) $id => $routeInterface] */
    private array $routes;

    private int $id = -1;

    public function __construct(RouterCacheInterface $routerCache)
    {
        $this->routerCache = $routerCache;
        $this->routeCache = $this->routerCache->routeCache();
        $this->router = (new Router($this->routeCache))
            ->withIndex(new RouterIndex())
            ->withGroups(new RouterGroups())
            ->withNamed(new RouterNamed());
    }

    public function withAddedRouteable(RouteableInterface $routeable, string $group): RouterMakerInterface
    {
        $new = clone $this;
        ++$new->id;
        $new->assertUniquePath($routeable->route());
        $new->assertUniqueKey($routeable->route());
        $name = '';
        $path = $routeable->route()->pathUri()->toString();
        $key = $routeable->route()->pathUri()->key();
        $regex = $routeable->route()->regex();
        $new->regexes[$new->id] = $regex;
        $new->paths[$path] = $new->id;
        $new->keys[$key] = $new->id;
        $new->router = $new->router
            ->withRegex($new->getRouterRegex())
            ->withGroups(
                $new->router()->groups()->withAdded($group, $new->id)
            );
        if ($routeable->route()->hasName()) {
            $new->assertUniqueName($routeable->route());
            $name = $routeable->route()->name()->toString();
            $new->named[$name] = $new->id;
            $new->router = $new->router
                ->withNamed(
                    $new->router()->named()->withAdded($name, $new->id)
                );
        }
        $new->router = $new->router
            ->withIndex(
                $new->router()->index()->withAdded(
                    $routeable->route()->pathUri(),
                    $new->id,
                    $group,
                    $name
                )
            );
        $new->routeCache->put($new->id, $routeable);
        $new->routes[$new->id] = $routeable->route();

        return $new;
    }

    public function router(): RouterInterface
    {
        return $this->router;
    }

    /**
     * @throws RouterMakerException if the regex pattern created is invalid
     */
    private function getRouterRegex(): RouterRegexInterface
    {
        $array = [];
        foreach ($this->regexes as $id => $string) {
            preg_match('#\^(.*)\$#', $string, $matches);
            $array[] = sprintf(RouterRegexInterface::TEMPLATE_ENTRY, $matches[1], $id);
        }
        $regex = new Regex(sprintf(RouterRegexInterface::TEMPLATE, implode('', $array)));

        return new RouterRegex($regex);
    }

    private function assertUniquePath(RouteInterface $route): void
    {
        if (!isset($this->routes)) {
            return;
        }
        $path = $route->pathUri()->toString();
        $knownId = $this->paths[$path] ?? null;
        if ($knownId === null) {
            return;
        }
        throw new RoutePathExistsException(
            (new Message('Unable to register route path %path% at %declare% (path already registered at %register%)'))
                ->code('%path%', $path)
                ->code('%declare%', $route->maker()['fileLine'])
                ->code('%register%', $this->routes[$knownId]->maker()['fileLine'])
                ->toString()
        );
    }

    private function assertUniqueKey(RouteInterface $route): void
    {
        if (!isset($this->routes)) {
            return;
        }
        $knownId = $this->keys[$route->pathUri()->key()] ?? null;
        if ($knownId === null) {
            return;
        }
        throw new RouteKeyConflictException(
            (new Message('Router conflict detected for %path% at %declare% (self-assigned internal key %key% is already reserved by %register%)'))
                ->code('%path%', $route->pathUri()->toString())
                ->code('%declare%', $route->maker()['fileLine'])
                ->code('%key%', $route->pathUri()->key())
                ->code('%register%', $this->routes[$knownId]->maker()['fileLine'])
                ->toString()
        );
    }

    private function assertUniqueName(RouteInterface $route): void
    {
        if (!isset($this->routes)) {
            return;
        }
        $knownId = $this->named[$route->name()->toString()] ?? null;
        if ($knownId !== null) {
            throw new RouteNameConflictException(
                (new Message('Unable to assign route name %name% for path %path% at %declare% (name assigned to %namedRoutePath% at %register%)'))
                    ->code('%name%', $route->name()->toString())
                    ->code('%path%', $route->pathUri()->toString())
                    ->code('%declare%', $route->maker()['fileLine'])
                    ->code('%namedRoutePath%', $this->routes[$knownId]->pathUri()->toString())
                    ->code('%register%', $this->routes[$knownId]->maker()['fileLine'])
                    ->toString()
            );
        }
    }
}
