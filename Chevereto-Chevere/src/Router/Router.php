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
    private $baseIndex;

    /** @var array [regex => Route id]. */
    private $regexIndex;

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
        $id = array_key_last($this->routes);
        $this->baseIndex[$basename][] = array_key_last($this->routes);
        $powerSet = $route->powerSet;
        if (isset($powerSet)) {
            $ix = $id;
            foreach ($powerSet as $k => $wildcardsIndex) {
                ++$ix;
                $this->routes[] = [$id, $k];
                $this->regexIndex[$route->regex($k)] = $ix;
            }
        } else {
            // n => .. => regex => route
            $this->regexIndex[$route->regex] = $id;
            if (Route::TYPE_STATIC == $route->type) {
                $this->statics[$route->uri] = $id;
            }
        }

        $this->regex = $this->getRegex();
        $this->routeKeys[$uri] = $pointer;
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
     * Resolve routing for the given path info, sets matched arguments.
     *
     * @param string $pathInfo request path
     */
    public function resolve(string $pathInfo): Route
    {
        if (preg_match($this->regex, $pathInfo, $matches)) {
            $id = $matches['MARK'];
            unset($matches['MARK']);
            array_shift($matches);
            $route = $this->routes[$id];
            if (is_array($route)) {
                $set = $route[1];
                $route = $this->routes[$route[0]];
            }
            $resolver = new Resolver($route);
            if ($resolver->isUnserialized) {
                $route = $resolver->get();
                $this->routes[$id] = $route;
            }
            $this->arguments = [];
            foreach ($matches as $k => $v) {
                $wildcardId = $route->powerSet[$set][$k];
                $wildcardKey = $route->wildcards[$wildcardId];
                $this->arguments[$wildcardKey] = $v;
            }

            return $route;
        }
        throw new LogicException(
            (new Message('NO ROUTING!!!!! %s'))->code('%s', 'BURN!!!')->toString()
        );
    }

    private function handleRouteKey(string $key)
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

    private function handleRouteName(?string $name, array $pointer)
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
}
