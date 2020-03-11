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
use Chevere\Components\Regex\Regex;
use Chevere\Components\Route\Interfaces\RouteInterface;
use Chevere\Components\Router\Exceptions\RouteKeyConflictException;
use Chevere\Components\Router\Exceptions\RouteNameConflictException;
use Chevere\Components\Router\Exceptions\RoutePathExistsException;
use Chevere\Components\Router\Exceptions\RouterMakerException;
use Chevere\Components\Router\Interfaces\RouteableInterface;
use Chevere\Components\Router\Interfaces\RouterCacheInterface;
use Chevere\Components\Router\Interfaces\RouterInterface;
use Chevere\Components\Router\Interfaces\RouterMakerInterface;
use Chevere\Components\Router\Interfaces\RouterRegexInterface;
use Chevere\Components\Router\Interfaces\RoutesCacheInterface;

/**
 * RouterMaker takes a bunch of routes and generates a cache-ready routing table.
 */
final class RouterMaker implements RouterMakerInterface
{
    private RouterCacheInterface $routerCache;

    private RoutesCacheInterface $routesCache;

    private RouterInterface $router;

    /** @var array [(string) $routePath => (int) $id] */
    private array $paths;

    /** @var array [(string) $routePathKey => (int) $id] */
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
        $this->routesCache = $this->routerCache->routesCache();
        $this->router = (new Router($this->routesCache))
            ->withIndex(new RouterIndex())
            ->withNamed(new RouterNamed())
            ->withGroups(new RouterGroups());
    }

    public function withAddedRouteable(RouteableInterface $routeable, string $group): RouterMakerInterface
    {
        $new = clone $this;
        ++$new->id;
        $new->assertUniquePath($routeable->route());
        $new->assertUniqueKey($routeable->route());
        $path = $routeable->route()->path()->toString();
        $key = $routeable->route()->path()->key();
        $regex = $routeable->route()->path()->regex();
        $new->regexes[$new->id] = $regex;
        $new->paths[$path] = $new->id;
        $new->keys[$key] = $new->id;
        $new->router = $new->router
            ->withRegex($new->getRouterRegex())
            ->withGroups(
                $new->router()->groups()->withAdded($group, $new->id)
            );
        $new->assertUniqueName($routeable->route());
        $name = $routeable->route()->name()->toString();
        $new->named[$name] = $new->id;
        $new->router = $new->router
            ->withNamed(
                $new->router()->named()->withAdded($name, $new->id)
            );
        $new->router = $new->router
            ->withIndex(
                $new->router()->index()->withAdded(
                    $routeable->route(),
                    $new->id,
                    $group
                )
            );
        $new->routesCache->put($new->id, $routeable);
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
        $path = $route->path()->toString();
        $knownId = $this->paths[$path] ?? null;
        if ($knownId === null) {
            return;
        }
        throw new RoutePathExistsException(
            (new Message('Unable to register route path %path% at %declare% (path already registered at %register%)'))
                ->code('%path%', $path)
                ->code('%declare%', $this->getFileLine($route->maker()))
                ->code('%register%', $this->getFileLine($this->routes[$knownId]->maker()))
                ->toString()
        );
    }

    private function assertUniqueKey(RouteInterface $route): void
    {
        if (!isset($this->routes)) {
            return;
        }
        $knownId = $this->keys[$route->path()->key()] ?? null;
        if ($knownId === null) {
            return;
        }
        throw new RouteKeyConflictException(
            (new Message('Router conflict detected for %path% at %declare% (self-assigned internal key %key% is already reserved by %register%)'))
                ->code('%path%', $route->path()->toString())
                ->code('%declare%', $this->getFileLine($route->maker()))
                ->code('%key%', $route->path()->key())
                ->code('%register%', $this->getFileLine($this->routes[$knownId]->maker()))
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
                    ->code('%path%', $route->path()->toString())
                    ->code('%declare%', $this->getFileLine($route->maker()))
                    ->code('%namedRoutePath%', $this->routes[$knownId]->path()->toString())
                    ->code('%register%', $this->getFileLine($this->routes[$knownId]->maker()))
                    ->toString()
            );
        }
    }

    private function getFileLine(array $maker): string
    {
        return $maker['file'] . ':' . $maker['line'];
    }
}
