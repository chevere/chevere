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
use Chevere\Components\Router\Interfaces\RouterInterface;
use Chevere\Components\Router\Interfaces\RouterMakerInterface;
use Chevere\Components\Router\Interfaces\RouterRegexInterface;
use Ds\Map;

/**
 * RouterMaker takes a bunch of routes and generates a cache-ready routing table.
 */
final class RouterMaker implements RouterMakerInterface
{
    private RouterInterface $router;

    private Routeables $routeables;

    /** @var Map [<string>routePath => <int>id] */
    private Map $paths;

    /** @var Map [<string>routePathKey => <int>id] */
    private Map $keys;

    /** @var Map [<string>name => <int>id] */
    private Map $regexIndex;

    /** @var Map [(<int>id => [<string>regex,]] */
    private Map $regexes;

    private int $pos = -1;

    public function __construct()
    {
        $this->router = (new Router)->withIndex(new RouterIndex);
        $this->routeables = new Routeables;
        $this->paths = new Map;
        $this->keys = new Map;
        $this->regexIndex = new Map;
        $this->regexes = new Map;
    }

    public function withAddedRouteable(RouteableInterface $routeable, string $group): RouterMakerInterface
    {
        $new = clone $this;
        ++$new->pos;
        $route = $routeable->route();
        $new->assertUniquePath($route);
        $new->assertUniqueName($route);
        $new->assertUniqueRoutePathKey($route);
        $routeName = $route->name()->toString();
        $new->routeables->put($routeable);
        /** @var \Ds\TValue $routeNameValue */
        $routeNameValue = $routeName;
        /** @var \Ds\TKey $regexKey */
        $regexKey = $new->pos;
        /** @var \Ds\TValue $regexValue */
        $regexValue = $route->path()->regex();
        $new->regexes->put($regexKey, $regexValue);
        $new->regexIndex->put($regexKey, $routeName);
        /** @var \Ds\TKey $pathKey */
        $pathKey = $route->path()->toString();
        $new->paths->put($pathKey, $routeNameValue);
        /** @var \Ds\TKey $pathKeyKey */
        $pathKeyKey = $route->path()->key();
        $new->keys->put($pathKeyKey, $routeNameValue);
        $new->router = $new->router
            ->withRouteables($new->routeables)
            ->withRegex($new->getRouterRegex())
            ->withIndex(
                $new->router()->index()->withAdded($routeable, $group)
            );

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
        /**
         * @var int $pos
         * @var string $regex
         */
        foreach ($this->regexes as $pos => $regex) {
            preg_match('#\^(.*)\$#', $regex, $matches);
            $array[] = sprintf(
                RouterRegexInterface::TEMPLATE_ENTRY,
                $matches[1],
                $pos
            );
        }

        return new RouterRegex(
            new Regex(
                sprintf(RouterRegexInterface::TEMPLATE, implode('', $array))
            )
        );
    }

    private function assertUniquePath(RouteInterface $route): void
    {
        $path = $route->path()->toString();
        if ($this->paths->hasKey($path)) {
            /** @var string $knownName */
            $knownName = $this->paths->get(/** @scrutinizer ignore-type */ $path);
            $knownRoute = $this->routeables->get($knownName)->route();
            throw new RoutePathExistsException(
                (new Message('Unable to register route path %path% at %declare% (path already registered at %register%)'))
                    ->code('%path%', $path)
                    ->code('%declare%', $this->getFileLine($route->maker()))
                    ->code('%register%', $this->getFileLine($knownRoute->maker()))
                    ->toString()
            );
        }
    }

    private function assertUniqueRoutePathKey(RouteInterface $route): void
    {
        /** @var \Ds\TKey $key */
        $key = $route->path()->key();
        if ($this->keys->hasKey($key)) {
            /** @var string $knownName */
            $knownName = $this->keys->get($key);
            $knownRoute = $this->routeables->get($knownName)->route();
            throw new RouteKeyConflictException(
                (new Message('Router conflict detected for key %path% at %declare% (self-assigned internal key %key% is already reserved by %register%)'))
                    ->code('%path%', $route->path()->toString())
                    ->code('%declare%', $this->getFileLine($route->maker()))
                    ->code('%key%', $route->path()->key())
                    ->code('%register%', $this->getFileLine($knownRoute->maker()))
                    ->toString()
            );
        }
    }

    private function assertUniqueName(RouteInterface $route): void
    {
        if ($this->routeables->hasKey($route->name()->toString())) {
            $routeName = $route->name()->toString();
            $knownRoute = $this->routeables->get($routeName)->route();
            throw new RouteNameConflictException(
                (new Message('Unable to re-assign route name %routeName% for path %routePath% at %declare% (name assigned to %namedRoutePath% at %register%)'))
                    ->code('%routeName%', $route->name()->toString())
                    ->code('%path%', $route->path()->toString())
                    ->code('%declare%', $this->getFileLine($route->maker()))
                    ->code('%namedRoutePath%', $knownRoute->path()->toString())
                    ->code('%register%', $this->getFileLine($knownRoute->maker()))
                    ->toString()
            );
        }
    }

    private function getFileLine(array $maker): string
    {
        return $maker['file'] . ':' . $maker['line'];
    }
}
