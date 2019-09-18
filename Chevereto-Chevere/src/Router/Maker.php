<?php

declare(strict_types=1);

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Chevere\Router;

use LogicException;
use Chevere\ArrayFile\ArrayFile;
use Chevere\ArrayFile\ArrayFileCallback;
use Chevere\Message\Message;
use Chevere\Route\Route;
use Chevere\Contracts\Route\RouteContract;
use Chevere\Path\PathHandle;
use Chevere\Cache\Cache;
use Chevere\Type\Type;

/**
 * Maker takes a bunch of Routes and generates a routing table (php array).
 */
final class Maker
{
    // const ID = 'id';
    // const SET = 'set';

    const REGEX_TEPLATE = '#^(?%s)$#x';

    /** @var string Regex representation, used when resolving routing */
    private $regex;

    /** @var array Route members (objects, serialized) [id => Route] */
    private $routes;

    /** @var array Contains ['/path' => [id, 'route/key']] */
    private $routesIndex;

    /** @var array An array containing the named routes [name => [id, fileHandle]] */
    private $named;

    /** @var array [basename => [route id,]]. */
    private $baseIndex;

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

    public function __construct()
    { }

    /**
     * {@inheritdoc}
     */
    public function addRoute(RouteContract $route, string $group): void
    {
        $this->route = $route->fill();
        $this->routeMap = ['group' => $group, 'id' => $this->route->id()];
        $this->validateUniqueRoutePath();
        $this->handleRouteName();
        $this->routes[] = $route;
        $id = array_key_last($this->routes);
        $this->baseIndex[$group][] = array_key_last($this->routes);
        $keyPowerSet = $route->keyPowerSet();
        if (!empty($keyPowerSet)) {
            $ix = $id;
            foreach ($keyPowerSet as $set => $index) {
                ++$ix;
                $this->routes[] = [$id, (string) $set];
                $this->regexIndex[$route->getRegex((string) $set)] = $ix;
            }
        } else {
            // n => .. => regex => route
            $this->regexIndex[$route->regex()] = $id;
            if (Route::TYPE_STATIC == $route->type()) {
                $this->statics[$route->path()] = $id;
            }
        }

        $this->regex = $this->getRegex();
        $this->routesIndex[$this->route->path()] = $this->routeMap;
    }

    /**
     * Adds routes (ArrayFile) specified by path handle.
     *
     * @param array $paramRoutes ['routes:web', 'routes:dashboard']
     */
    public function addRoutesArrays(array $paramRoutes): void
    {
        foreach ($paramRoutes as $fileHandleString) {
            $arrayFile = new ArrayFile(
                new PathHandle($fileHandleString)
            );
            $arrayFile->setMembersType(new Type(RouteContract::class));
            $arrayFileWrap = new ArrayFileCallback($arrayFile, function ($k, $route) {
                $route->setId((string) $k);
            });
            foreach ($arrayFileWrap as $route) {
                $this->addRoute($route, $fileHandleString);
            }
        }
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

    public function setCache()
    {
        $this->cache = new Cache('router');
        $regex = $this->cache->put('regex', $this->regex);
        $routes = $this->cache->put('routes', $this->routes);
        $routesIndex = $this->cache->put('routesIndex', $this->routesIndex);
        opcache_compile_file($regex->path());
        opcache_compile_file($routes->path());
        opcache_compile_file($routesIndex->path());
    }

    public function cache(): Cache
    {
        return $this->cache;
    }

    /**
     * {@inheritdoc}
     */
    private function getRegex(): string
    {
        $regex = [];
        foreach ($this->regexIndex as $k => $v) {
            preg_match('#\^(.*)\$#', $k, $matches);
            $regex[] = '|' . $matches[1] . " (*:$v)";
        }

        return sprintf(static::REGEX_TEPLATE, implode('', $regex));
    }

    private function validateUniqueRoutePath(): void
    {
        $keyedRoute = $this->routesIndex[$this->route->path()] ?? null;
        if (isset($keyedRoute)) {
            throw new LogicException(
                (new Message('Route key %s has been already declared by %r.'))
                    ->code('%s', $this->route->path())
                    ->code('%r', $keyedRoute['id'] . '@' . $keyedRoute['group'])
                    ->toString()
            );
        }
    }

    private function handleRouteName(): void
    {
        $name = $this->route->hasName() ? $this->route->name() : null;
        if (!isset($name)) {
            return;
        }
        $namedRoute = $this->named[$name] ?? null;
        if (isset($namedRoute)) {
            throw new LogicException(
                (new Message('Route name %s has been already taken by %r.'))
                    ->code('%s', $name)
                    ->code('%r', $namedRoute[0] . '@' . $namedRoute[1])
                    ->toString()
            );
        }
        $this->named[$name] = $this->routeMap;
    }
}
