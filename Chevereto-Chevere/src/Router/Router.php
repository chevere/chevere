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

/**
 * Routes takes a bunch of Routes and generates a routing table (php array).
 */
class Router
{
    const PRIORITY_ORDER = [Route::TYPE_STATIC, Route::TYPE_DYNAMIC];

    const ID = 'id';
    const SET = 'set';

    const REGEX_TEPLATE = '#^(?%s)$#x';

    /** @var array Route members (objects, serialized) [id => Route] */
    public $routes;

    /** @var array Contains ['/route/key' => [id, 'route/key']] */
    public $routeKeys;

    /** @var array An array containing the named routes [name => [id, fileHandle]] */
    public $namedRoutes;

    /** @var string Regex representation, used when resolving routing. */
    public $regex;

    /** @var array Arguments taken from wildcard matches. */
    public $arguments;

    /** @var array [basename => [route id,]]. */
    protected $baseIndex;

    /** @var array [regex => Route id]. */
    protected $regexIndex;

    public function addRoute(Route $route, string $basename)
    {
        $route->fill();
        $id = $route->id;
        $uri = $route->uri;
        $this->handleRouteKey($uri);
        $pointer = [$id, $basename];
        $name = $route->name;
        $this->handleRouteName($name, $pointer);
        $this->routes[] = $route;
        $this->baseIndex[$basename][] = array_key_last($this->routes);
        $powerSet = $route->powerSet;
        if (isset($powerSet)) {
            foreach ($powerSet as $k => $wildcardsIndex) {
                // n => .. => regex => [route, wildcards]
                $this->routing($route); // $route->regex($k)
            }
        } else {
            // n => .. => regex => route
            $this->routing($route);
        }
        $this->regex = $this->getRegex();
        $this->routeKeys[$uri] = $pointer;
    }

    protected function handleRouteKey(string $key)
    {
        $keyedRoute = $this->routeKeys[$key] ?? null;
        if (isset($keyedRoute)) {
            throw new LogicException(
                (new Message('Route key %s has been already declared by %r.'))
                    ->code('%s', $key)
                    ->code('%r', $keyedRoute[0].'@'.$keyedRoute[1])
                    ->toString()
            );
        }
    }

    protected function handleRouteName(?string $name, array $pointer)
    {
        if (isset($name)) {
            $namedRoute = $this->namedRoutes[$name] ?? null;
            if (isset($namedRoute)) {
                throw new LogicException(
                    (new Message('Route name %s has been already taken by %r.'))
                        ->code('%s', $name)
                        ->code('%r', $namedRoute[0].'@'.$namedRoute[1])
                        ->toString()
                );
            }
            $this->namedRoutes[$name] = $pointer;
        }
    }

    /**
     * Group a Route into the routing table, sets regexIndex and statics.
     *
     * @param Route $route route object
     */
    protected function routing(Route $route): void
    {
        $id = array_key_last($this->routes);
        $this->regexIndex[$route->regex] = $id;
        if (Route::TYPE_STATIC == $route->type) {
            $this->statics[$route->uri] = $id;
        }
    }

    public function getRegex(): string
    {
        $regex = [];
        foreach ($this->regexIndex as $k => $v) {
            preg_match('#\^(.*)\$#', $k, $matches);
            $regex[] = '|'.$matches[1]." (*:$v)";
        }

        return sprintf(static::REGEX_TEPLATE, implode('', $regex));
    }

    /**
     * Resolve routing for the given path info.
     *
     * @param string $pathInfo request path
     */
    public function resolve(string $pathInfo): Route
    {
        $requestTrim = ltrim($pathInfo, '/');
        if (preg_match($this->regex, $pathInfo, $matches)) {
            $id = $matches['MARK'];
            $matches = array_slice($matches, 2);
            // dd($matches);
            // $this->arguments = $matches;
            $resolver = new Resolver($this->routes[$id]);
            if ($resolver->isUnserialized) {
                $this->routes[$id] = $resolver->get();
            }

            return $resolver->get();
        }
        dd('eso es todo <code>amiwos</code>!');
        throw new LogicException(
            (new Message('NO ROUTING!!!!! %s'))->code('%s', 'BURN!!!')->toString()
        );
    }

    protected function getComponents(string $requestTrim): array
    {
        return null == $requestTrim ? [] : explode('/', $requestTrim);
    }

    protected function getRoutesTable(int $key, string $priority): ?array
    {
        dd($this->regexIndex, $this->statics);

        return $this->routing[$key][$priority] ?? null;
    }
}
