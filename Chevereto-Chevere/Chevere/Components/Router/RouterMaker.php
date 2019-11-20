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

use InvalidArgumentException;
use Chevere\Components\ArrayFile\ArrayFile;
use Chevere\Components\Cache\CacheKey;
use Chevere\Components\File\File;
use Chevere\Components\File\FilePhp;
use Chevere\Components\Message\Message;
use Chevere\Components\Path\Path;
use Chevere\Components\Type\Type;
use Chevere\Components\Variable\VariableExport;
use Chevere\Contracts\Cache\CacheContract;
use Chevere\Contracts\Route\RouteContract;
use Chevere\Contracts\Router\CacheKeysContract;
use Chevere\Contracts\Router\RouterMakerContract;

/**
 * Maker takes a bunch of Routes and generates a routing table (php array).
 */
final class RouterMaker implements RouterMakerContract
{
    /** @var string Regex representation, used when resolving routing */
    private $regex;

    /** @var array [regex => Route id]. */
    private $regexIndex;

    /** @var array Route members (objects, serialized) [id => RouteContract] */
    private $routes;

    /** @var array [/path/{param} => [id => $id, group => group]] */
    private $routesIndex;

    /** @var array [/path/{0} => $id] */
    private $routesKeys;

    /** @var array Named routes [routeName => $id] */
    private $named;

    /** @var array Static routes */
    private $statics;

    /** @var RouteContract */
    private $route;

    /** @var CacheContract */
    private $cache;

    public function __construct()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function withAddedRoute(RouteContract $route, string $group): RouterMakerContract
    {
        $new = clone $this;
        $new->route = $route;
        $new->assertRouteMethodControllerNameCollection();
        $new->assertRouteUniquePath();
        $new->assertRouteUniqueKey();
        $id = empty($new->routes) ? 0 : (array_key_last($new->routes) + 1);
        if ($new->route->hasName()) {
            $new->assertUniqueNamedRoute();
            $new->named[$new->route->name()->toString()] = $id;
        }
        $new->routes[] = $new->route;
        // n => .. => regex => route
        $new->regexIndex[$new->route->regex()] = $id;
        if (!$route->hasWildcardCollection()) {
            $new->statics[$new->route->pathUri()->path()] = $id;
        }
        $new->regex = $new->getRegex();
        $new->routesKeys[$new->route->pathUri()->key()] = $id;
        $new->routesIndex[$new->route->pathUri()->path()] = [
            'id' => $id,
            'group' => $group,
        ];

        return $new;
    }

    /**
     * Adds routes (ArrayFile) specified by path handle.
     *
     * @param string $routeFiles Routes relative to app, like 'routes/web.php', 'routes/dashboard.php',
     */
    public function withAddedRouteFiles(...$routeFiles): RouterMakerContract
    {
        $new = clone $this;
        foreach ($routeFiles as $fileHandleString) {
            $arrayFile =
                (new ArrayFile(
                    new FilePhp(
                        new File(
                            new Path($fileHandleString)
                        )
                    )
                ))
                    ->withMembersType(new Type(RouteContract::class));
            foreach ($arrayFile->array() as $route) {
                $new = $new->withAddedRoute($route, $fileHandleString);
            }
        }

        return $new;
    }

    public function regex(): string
    {
        return $this->regex;
    }

    public function routes(): array
    {
        return $this->routes;
    }

    public function index(): array
    {
        return $this->routesIndex;
    }

    public function withCache(CacheContract $cache): RouterMakerContract
    {
        $cache = $cache
            ->withPut(
                new CacheKey(CacheKeysContract::REGEX),
                new VariableExport($this->regex)
            )
            ->withPut(
                new CacheKey(CacheKeysContract::ROUTES),
                new VariableExport($this->routes)
            )
            ->withPut(
                new CacheKey(CacheKeysContract::INDEX),
                new VariableExport($this->routesIndex)
            );

        $new = clone $this;
        $new->cache = $cache;

        return $new;
    }

    public function cache(): CacheContract
    {
        return $this->cache;
    }

    private function getRegex(): string
    {
        $regex = [];
        foreach ($this->regexIndex as $k => $v) {
            preg_match('#\^(.*)\$#', $k, $matches);
            $regex[] = '|' . $matches[1] . " (*:$v)";
        }

        return sprintf(RouterMakerContract::REGEX_TEPLATE, implode('', $regex));
    }

    private function assertRouteUniqueKey(): void
    {
        $routeId = $this->routesKeys[$this->route->pathUri()->key()] ?? null;
        if (isset($routeId)) {
            $routeIndexed = $this->routes[$routeId];
            throw new InvalidArgumentException(
                (new Message('Router conflict detected for %path% at %declare% (self-assigned internal key %key% is already reserved by %register%)'))
                    ->code('%path%', $this->route->pathUri()->path())
                    ->code('%declare%', $this->route->maker()['fileLine'])
                    ->code('%key%', $this->route->pathUri()->key())
                    ->code('%register%', $routeIndexed->maker()['fileLine'])
                    ->toString()
            );
        }
    }

    private function assertRouteMethodControllerNameCollection(): void
    {
        if (!$this->route->methodControllerNameCollection()->hasAny()) {
            throw new InvalidArgumentException(
                (new Message("Instance of %className% doesn't contain any method controller"))
                    ->code('%className%', RouteContract::class)
                    ->toString()
            );
        }
    }

    private function assertRouteUniquePath(): void
    {
        $routeIndex = $this->routesIndex[$this->route->pathUri()->path()] ?? null;
        if (isset($routeIndex)) {
            $routeIndexed = $this->routes[$routeIndex['id']];
            throw new InvalidArgumentException(
                (new Message('Unable to register route path %path% at %declare% (path already registered at %register%)'))
                    ->code('%path%', $this->route->pathUri()->path())
                    ->code('%declare%', $this->route->maker()['fileLine'])
                    ->code('%register%', $routeIndexed->maker()['fileLine'])
                    ->toString()
            );
        }
    }

    private function assertUniqueNamedRoute(): void
    {
        $namedId = $this->named[$this->route->name()->toString()] ?? null;
        if (isset($namedId)) {
            $name = $this->route->name()->toString();
            $routeExists = $this->routes[$namedId];
            throw new InvalidArgumentException(
                (new Message('Unable to assign route name %name% for path %path% at %declare% (name assigned to %namedRoutePath% at %register%)'))
                    ->code('%name%', $name)
                    ->code('%path%', $this->route->pathUri()->path())
                    ->code('%declare%', $this->route->maker()['fileLine'])
                    ->code('%namedRoutePath%', $routeExists->pathUri()->path())
                    ->code('%register%', $routeExists->maker()['fileLine'])
                    ->toString()
            );
        }
    }
}
