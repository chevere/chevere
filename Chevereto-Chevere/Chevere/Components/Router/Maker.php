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

use LogicException;

use Chevere\Components\ArrayFile\ArrayFile;
use Chevere\Components\Cache\Cache;
use Chevere\Components\Message\Message;
use Chevere\Components\Path\PathHandle;
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

    /** @var array Route members (objects, serialized) [id => Route] */
    private $routes;

    /** @var array Contains ['/path' => [id, 'route/key']] */
    private $routesIndex;

    /** @var array An array containing the named routes [name => [id, fileHandle]] */
    private $named;

    /** @var array [regex => Route id]. */
    private $regexIndex;

    /** @var array Static routes */
    private $statics;

    /** @var RouteContract */
    private $route;

    /** @var array Stores the map for a given route ['group' => group, 'id' => routeId] */
    private $routeMap;

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
        $new->routeMap = [
            'route' => $new->route,
            'group' => $group,
        ];
        $new->validateUniqueRoutePath();
        $id = empty($new->routes) ? 0 : (array_key_last($new->routes) + 1);
        if ($new->route->hasName()) {
            $new->assertNamedRoute();
            $new->named[$new->route->name()] = $id;
        }
        $new->routes[] = $new->route;
        // n => .. => regex => route
        $new->regexIndex[$new->route->regex()] = $id;
        if (Route::TYPE_STATIC == $route->type()) {
            $new->statics[$new->route->path()] = $id;
        }
        $new->regex = $new->getRegex();
        $new->routesIndex[$new->route->path()] = $new->routeMap;

        return $new;
    }

    /**
     * Adds routes (ArrayFile) specified by path handle.
     *
     * @param array $routeIdentifiers ['routes:web', 'routes:dashboard']
     * FIXME: Use ... $pathHandle
     */
    public function withAddedRouteIdentifiers(array $routeIdentifiers): MakerContract
    {
        $new = clone $this;
        foreach ($routeIdentifiers as $fileHandleString) {
            $arrayFile = new ArrayFile(
                (new PathHandle($fileHandleString))
                    ->path()
            );
            $arrayFile = $arrayFile
                ->withMembersType(new Type(RouteContract::class));
            foreach ($arrayFile as $route) {
                $new = $new->withAddedRoute($route, $fileHandleString);
            }
        }

        return $new;
    }

    /**
     * {@inheritdoc}
     */
    public function regex(): string
    {
        return $this->regex;
    }

    /**
     * {@inheritdoc}
     */
    public function routes(): array
    {
        return $this->routes;
    }

    /**
     * {@inheritdoc}
     */
    public function routesIndex(): array
    {
        return $this->routesIndex;
    }

    /**
     * {@inheritdoc}
     */
    public function withCache(): MakerContract
    {
        $new = clone $this;
        $new->cache = new Cache('router');
        $new->cache->put('regex', $new->regex)
            ->makeCache();
        $new->cache->put('routes', $new->routes)
            ->makeCache();
        $new->cache->put('routesIndex', $new->routesIndex)
            ->makeCache();

        return $new;
    }

    /**
     * {@inheritdoc}
     */
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

    private function validateUniqueRoutePath(): void
    {
        $keyedRoute = $this->routesIndex[$this->route->path()] ?? null;
        if (isset($keyedRoute)) {
            throw new LogicException(
                (new Message('Route key %s has been already declared by %r'))
                    ->code('%s', $this->route->path())
                    ->code('%r', $keyedRoute['id'] . '@' . $keyedRoute['group'])
                    ->toString()
            );
        }
    }

    private function assertNamedRoute(): void
    {
        $namedId = $this->named[$this->route->name()] ?? null;
        if (isset($namedId)) {
            $name = $this->route->name();
            $routeExists = $this->routes[$namedId];
            throw new LogicException(
                (new Message('Route name %name% has been already taken by %path% at %fileWithLine%'))
                    ->code('%name%', $name)
                    ->code('%path%', $routeExists->path())
                    ->code('%fileWithLine%', $routeExists->maker()['file'] . ':' . $routeExists->maker()['line'])
                    ->toString()
            );
        }
    }
}
