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
use Chevere\Components\Cache\Cache;
use Chevere\Components\Message\Message;
use Chevere\Components\Path\Path;
use Chevere\Components\Route\Route;
use Chevere\Components\Type\Type;
use Chevere\Contracts\Route\RouteContract;
use Chevere\Contracts\Router\MakerContract;

/**
 * Maker takes a bunch of Routes and generates a routing table (php array).
 */
final class Maker implements MakerContract
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

    /** @var Cache */
    private $cache;

    /**
     * {@inheritdoc}
     */
    public function withAddedRoute(RouteContract $route, string $group): MakerContract
    {
        $new = clone $this;
        $route = $route->withFiller();
        $new->route = $route;
        $new->assertUniqueRoutePath();
        $new->assertUniqueRouteKey();
        $id = empty($new->routes) ? 0 : (array_key_last($new->routes) + 1);
        if ($new->route->hasName()) {
            $new->assertUniqueNamedRoute();
            $new->named[$new->route->name()] = $id;
        }
        $new->routes[] = $new->route;
        // n => .. => regex => route
        $new->regexIndex[$new->route->regex()] = $id;
        if (Route::TYPE_STATIC == $route->type()) {
            $new->statics[$new->route->path()] = $id;
        }
        $new->regex = $new->getRegex();
        $new->routesKeys[$new->route->key()] = $id;
        $new->routesIndex[$new->route->path()] = [
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
    public function withAddedRouteFiles(...$routeFiles): MakerContract
    {
        $new = clone $this;
        foreach ($routeFiles as $fileHandleString) {
            $path = new Path($fileHandleString);
            $arrayFile = (new ArrayFile($path))
                ->withMembersType(new Type(RouteContract::class));
            foreach ($arrayFile->toArray() as $route) {
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

    public function routesIndex(): array
    {
        return $this->routesIndex;
    }

    public function withCache(Cache $cache): MakerContract
    {
        $new = clone $this;
        $new->cache = $cache;
        $new->cache->put('regex', $new->regex)
            ->makeCache();
        $new->cache->put('routes', $new->routes)
            ->makeCache();
        $new->cache->put('routesIndex', $new->routesIndex)
            ->makeCache();

        return $new;
    }

    public function cache(): Cache
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

        return sprintf(MakerContract::REGEX_TEPLATE, implode('', $regex));
    }

    private function assertUniqueRouteKey(): void
    {
        $routeId = $this->routesKeys[$this->route->key()] ?? null;
        if (isset($routeId)) {
            $routeIndexed = $this->routes[$routeId];
            throw new InvalidArgumentException(
                (new Message('Router conflict detected for %path% at %declare% (self-assigned internal key %key% is already reserved by %register%)'))
                    ->code('%path%', $this->route->path())
                    ->code('%declare%', $this->route->maker()['fileLine'])
                    ->code('%key%', $this->route->key())
                    ->code('%register%', $routeIndexed->maker()['fileLine'])
                    ->toString()
            );
        }
    }

    private function assertUniqueRoutePath(): void
    {
        $routeIndex = $this->routesIndex[$this->route->path()] ?? null;
        if (isset($routeIndex)) {
            $routeIndexed = $this->routes[$routeIndex['id']];
            throw new InvalidArgumentException(
                (new Message('Unable to register route path %path% at %declare% (path already registered at %register%)'))
                    ->code('%path%', $this->route->path())
                    ->code('%declare%', $this->route->maker()['fileLine'])
                    ->code('%register%', $routeIndexed->maker()['fileLine'])
                    ->toString()
            );
        }
    }

    private function assertUniqueNamedRoute(): void
    {
        $namedId = $this->named[$this->route->name()] ?? null;
        if (isset($namedId)) {
            $name = $this->route->name();
            $routeExists = $this->routes[$namedId];
            throw new InvalidArgumentException(
                (new Message('Unable to assign route name %name% for path %path% at %declare% (name assigned to %namedRoutePath% at %register%)'))
                    ->code('%name%', $name)
                    ->code('%path%', $this->route->path())
                    ->code('%declare%', $this->route->maker()['fileLine'])
                    ->code('%namedRoutePath%', $routeExists->path())
                    ->code('%register%', $routeExists->maker()['fileLine'])
                    ->toString()
            );
        }
    }
}
