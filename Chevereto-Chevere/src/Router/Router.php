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
use Chevere\Message;
use Chevere\Route\Route;
use Chevere\Contracts\Route\RouteContract;
use Chevere\Contracts\Router\RouterContract;
use Chevere\FileReturn\FileReturnWrite;
use Chevere\Path\PathHandle;

/**
 * Routes takes a bunch of Routes and generates a routing table (php array).
 */
final class Router
{
    const PRIORITY_ORDER = [Route::TYPE_STATIC, Route::TYPE_DYNAMIC];

    const ID = 'id';
    const SET = 'set';

    const REGEX_TEPLATE = '#^(?%s)$#x';

    /** @var array Route members (objects, serialized) [id => Route] */
    private $routes;

    /** @var array Contains ['/path' => [id, 'route/key']] */
    private $routesIndex;

    /** @var array An array containing the named routes [name => [id, fileHandle]] */
    private $named;

    /** @var string Regex representation, used when resolving routing */
    private $regex;

    /** @var array Arguments taken from wildcard matches */
    private $arguments;

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
                $this->routes[] = [$id, $set];
                $this->regexIndex[$route->getRegex($set)] = $ix;
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

    public function arguments(): array
    {
        return $this->arguments ?? [];
    }

    public function cache()
    {
        $regexHandle = new PathHandle('cache:router-regex');
        $routesHandle = new PathHandle('cache:router-routes');
        $indexHandle = new PathHandle('cache:router-index');
        $regex = new FileReturnWrite($regexHandle);
        $routes = new FileReturnWrite($routesHandle);
        $index = new FileReturnWrite($indexHandle);
        $regex->set($this->regex);
        $routes->set($this->routes);
        $index->set($this->routesIndex);
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
                    ->code('%r', $keyedRoute[0] . '@' . $keyedRoute[1])
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
