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
use Chevere\Interfaces\Regex\RegexInterface;
use Chevere\Components\Regex\Regex;
use Chevere\Interfaces\Route\RouteInterface;
use Chevere\Exceptions\Router\RouteKeyConflictException;
use Chevere\Exceptions\Router\RouteNameConflictException;
use Chevere\Exceptions\Router\RoutePathExistsException;
use Chevere\Exceptions\Router\RouterMakerException;
use Chevere\Interfaces\Router\RoutableInterface;
use Chevere\Interfaces\Router\RouterInterface;
use Chevere\Interfaces\Router\RouterMakerInterface;
use Chevere\Interfaces\Router\RouterRegexInterface;
use Ds\Map;

/**
 * RouterMaker takes routables and generates a Router.
 */
final class RouterMaker implements RouterMakerInterface
{
    private RouterInterface $router;

    private Routables $routables;

    /** @var Map [<string>routePath => <int>id] */
    private Map $paths;

    /** @var Map [<string>routePathKey => <int>id] */
    private Map $keys;

    /** @var Map [<string>name => <int>id] */
    private Map $regexIndex;

    /** @var Map [<int>id => [<string>regex,]] */
    private Map $regexMap;

    private int $pos = -1;

    public function __construct()
    {
        $this->router = new Router;
        $this->routables = new Routables;
        $this->paths = new Map;
        $this->keys = new Map;
        $this->regexIndex = new Map;
        $this->regexMap = new Map;
    }

    public function withAddedRoutable(RoutableInterface $routable, string $group): RouterMakerInterface
    {
        $new = clone $this;
        ++$new->pos;
        $route = $routable->route();
        $new->assertUniquePath($route);
        $new->assertUniqueName($route);
        $new->assertUniqueRoutePathKey($route);
        $routeName = $route->name()->toString();
        $new->routables->put($routable);
        /** @var \Ds\TValue $routeNameValue */
        $routeNameValue = $routeName;
        /** @var \Ds\TKey $regexKey */
        $regexKey = $new->pos;
        /** @var \Ds\TValue $regexValue */
        $regexValue = $route->path()->regex();
        $new->regexMap->put($regexKey, $regexValue);
        $new->regexIndex->put($regexKey, $routeNameValue);
        /** @var \Ds\TKey $pathKey */
        $pathKey = $route->path()->toString();
        $new->paths->put($pathKey, $routeNameValue);
        /** @var \Ds\TKey $pathKeyKey */
        $pathKeyKey = $route->path()->key();
        $new->keys->put($pathKeyKey, $routeNameValue);
        $new->router = $new->router
            ->withRoutables($new->routables)
            ->withRegex($new->getRouterRegex())
            ->withIndex(
                $new->router()->index()->withAdded($routable, $group)
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
         * @var RegexInterface $regex
         */
        foreach ($this->regexMap as $pos => $regex) {
            preg_match('#\^(.*)\$#', $regex->toString(), $matches);
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
        /**
         * @var \Ds\TKey $key
         */
        $key = $path;
        if ($this->paths->hasKey($key)) {
            /** @var string $knownName */
            $knownName = $this->paths->get(/** @scrutinizer ignore-type */ $path);
            $knownRoute = $this->routables->get($knownName)->route();
            throw new RoutePathExistsException(
                (new Message('Unable to register route path %path% at %declare% (path already registered at %register%)'))
                    ->code('%path%', $path)
                    ->code('%declare%', $this->getFileLine($route->maker()))
                    ->code('%register%', $this->getFileLine($knownRoute->maker()))
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
            $knownRoute = $this->routables->get($knownName)->route();
            throw new RouteKeyConflictException(
                (new Message('Router conflict detected for key %path% at %declare% (self-assigned internal key %key% is already reserved by %register%)'))
                    ->code('%path%', $route->path()->toString())
                    ->code('%declare%', $this->getFileLine($route->maker()))
                    ->code('%key%', $route->path()->key())
                    ->code('%register%', $this->getFileLine($knownRoute->maker()))
            );
        }
    }

    private function assertUniqueName(RouteInterface $route): void
    {
        if ($this->routables->hasKey($route->name()->toString())) {
            $routeName = $route->name()->toString();
            $knownRoute = $this->routables->get($routeName)->route();
            throw new RouteNameConflictException(
                (new Message('Unable to re-assign route name %routeName% for path %path% at %declare% (name assigned to %namedRoutePath% at %register%)'))
                    ->code('%routeName%', $route->name()->toString())
                    ->code('%path%', $route->path()->toString())
                    ->code('%declare%', $this->getFileLine($route->maker()))
                    ->code('%namedRoutePath%', $knownRoute->path()->toString())
                    ->code('%register%', $this->getFileLine($knownRoute->maker()))
            );
        }
    }

    private function getFileLine(array $maker): string
    {
        return $maker['file'] . ':' . $maker['line'];
    }
}
