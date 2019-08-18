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
use Chevere\FileReturn\FileReturn;
use Chevere\FileReturn\Maker as FileReturnMaker;
use Chevere\Path\PathHandle;

/**s
 * Routes takes a bunch of Routes and generates a routing table (php array).
 */
final class Router
{
    const REGEX_TEPLATE = '#^(?%s)$#x';

    /** @var string Regex representation, used when resolving routing */
    private $regex;

    /** @var array Route members (objects, serialized) [id => Route] */
    private $routes;

    /** @var array Contains ['/path' => [id, 'route/key']] */
    private $routesIndex;

    /** @var array Arguments taken from wildcard matches */
    private $arguments;

    public function __construct(Maker $maker = null)
    {
        if (isset($maker)) {
            $this->regex = $maker->regex();
            $this->routes = $maker->routes();
            $this->routesIndex = $maker->routesIndex();
            $maker->cache();
        } else {
            $regex = new FileReturn(new PathHandle('cache/router:regex'));
            $routes = new FileReturn(new PathHandle('cache/router:routes'));
            $routesIndex = new FileReturn(new PathHandle('cache/router:routesIndex'));
            $this->regex = $regex->raw();
            $this->routes = $routes->raw();
            $this->routesIndex = $routesIndex->raw();
        }
    }

    public function arguments(): array
    {
        return $this->arguments ?? [];
    }

    /**
     * {@inheritdoc}
     */
    public function resolve(string $pathInfo): RouteContract
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
            if (is_string($route)) {
                $resolver = new Resolver($route);
                $route = $resolver->get();
                $this->routes[$id] = $route;
            }
            $this->arguments = [];
            foreach ($matches as $k => $v) {
                $wildcardId = $route->keyPowerSet()[$set][$k];
                $wildcardName = $route->wildcardName($wildcardId);
                $this->arguments[$wildcardName] = $v;
            }

            return $route;
        }
        throw new LogicException(
            (new Message('NO ROUTING!!!!! %s'))->code('%s', 'BURN!!!')->toString()
        );
    }
}
