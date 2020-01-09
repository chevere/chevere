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

use Chevere\Components\Router\Exceptions\RoutePathExistsException;
use Chevere\Components\Message\Message;
use Chevere\Components\Router\Exceptions\RouteKeyConflictException;
use Chevere\Components\Router\Exceptions\RouteNameConflictException;
use Chevere\Components\Router\Exceptions\RouterMakerException;
use Chevere\Components\Variable\VariableExport;
use Chevere\Components\Route\Contracts\RouteContract;
use Chevere\Components\Router\Contracts\Properties\RegexPropertyContract;
use Chevere\Components\Router\Contracts\RouteableContract;
use Chevere\Components\Router\Contracts\RouterMakerContract;
use Chevere\Components\Router\Contracts\RouterPropertiesContract;

/**
 * RouterMaker takes a bunch of routes and generates a cache-ready routing table.
 */
final class RouterMaker implements RouterMakerContract
{
    private RouterPropertiesContract $properties;

    /** @var array [RouteContract regex => $id]. */
    private array $regexes;

    /** @var array [RouteContract key => $id] */
    private array $keys;

    /**
     * Creates a new instance.
     */
    public function __construct()
    {
        $this->properties = new RouterProperties();
    }

    /**
     * {@inheritdoc}
     */
    public function properties(): RouterPropertiesContract
    {
        return $this->properties;
    }

    /**
     * {@inheritdoc}
     */
    public function withAddedRouteable(RouteableContract $routeable, string $group): RouterMakerContract
    {
        $new = clone $this;
        $route = $routeable->route();
        $new->assertUniquePath($route);
        $new->assertUniqueKey($route);
        $routes = $new->properties->routes();
        $index = $new->properties->index();
        $groups = $new->properties->groups();
        $named = $new->properties->named();
        $id = empty($routes) ? 0 : (array_key_last($routes) + 1);
        $routes[$id] = serialize($route);
        $new->regexes[$route->regex()] = $id;
        $groups[$group][] = $id;
        $new->keys[$route->pathUri()->key()] = $id;
        $routeDetails = [
            'id' => $id,
            'group' => $group,
            'name' => null,
        ];
        if ($route->hasName()) {
            $new->assertUniqueName($route);
            $routeName = $route->name()->toString();
            $routeDetails['name'] = $routeName;
            $named[$routeName] = $id;
        }
        $index[$route->pathUri()->toString()] = $routeDetails;
        $new->properties = $new->properties
            ->withRegex($new->getRegex())
            ->withRoutes($routes)
            ->withIndex($index)
            ->withGroups($groups)
            ->withNamed($named);

        $new->properties->assert();

        return $new;
    }

    /**
     * @throws RouterMakerException if the regex pattern created is invalid
     */
    private function getRegex(): string
    {
        $regex = [];
        foreach ($this->regexes as $key => $id) {
            preg_match('#\^(.*)\$#', $key, $matches);
            $regex[] = sprintf(RegexPropertyContract::REGEX_ENTRY_TEMPLATE, $matches[1], $id);
        }

        return sprintf(RegexPropertyContract::REGEX_TEPLATE, implode('', $regex));
    }

    private function assertUniquePath(RouteContract $route): void
    {
        $path = $route->pathUri()->toString();
        $routeIndex = $this->properties->index()[$path] ?? null;
        if (isset($routeIndex)) {
            $routeIndexed = $this->properties->routes()[$routeIndex['id']];
            throw new RoutePathExistsException(
                (new Message('Unable to register route path %path% at %declare% (path already registered at %register%)'))
                    ->code('%path%', $path)
                    ->code('%declare%', $route->maker()['fileLine'])
                    ->code('%register%', $routeIndexed->maker()['fileLine'])
                    ->toString()
            );
        }
    }

    private function assertUniqueKey(RouteContract $route): void
    {
        $routeId = $this->keys[$route->pathUri()->key()] ?? null;
        if (isset($routeId)) {
            $routeIndexed = $this->properties->routes()[$routeId];
            throw new RouteKeyConflictException(
                (new Message('Router conflict detected for %path% at %declare% (self-assigned internal key %key% is already reserved by %register%)'))
                    ->code('%path%', $route->pathUri()->toString())
                    ->code('%declare%', $route->maker()['fileLine'])
                    ->code('%key%', $route->pathUri()->key())
                    ->code('%register%', $routeIndexed->maker()['fileLine'])
                    ->toString()
            );
        }
    }

    private function assertUniqueName(RouteContract $route): void
    {
        $namedId = $this->properties()->named()[$route->name()->toString()] ?? null;
        if (isset($namedId)) {
            $name = $route->name()->toString();
            $routeExists = $this->properties->routes()[$namedId];
            throw new RouteNameConflictException(
                (new Message('Unable to assign route name %name% for path %path% at %declare% (name assigned to %namedRoutePath% at %register%)'))
                    ->code('%name%', $name)
                    ->code('%path%', $route->pathUri()->toString())
                    ->code('%declare%', $route->maker()['fileLine'])
                    ->code('%namedRoutePath%', $routeExists->pathUri()->toString())
                    ->code('%register%', $routeExists->maker()['fileLine'])
                    ->toString()
            );
        }
    }
}
