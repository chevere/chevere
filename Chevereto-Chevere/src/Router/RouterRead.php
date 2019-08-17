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

/**
 * Routes takes a bunch of Routes and generates a routing table (php array).
 */
final class RouterRead implements RouterContract
{
    const PRIORITY_ORDER = [Route::TYPE_STATIC, Route::TYPE_DYNAMIC];

    const ID = 'id';
    const SET = 'set';

    const REGEX_TEPLATE = '#^(?%s)$#x';

    /** @var array Route members (objects, serialized) [id => Route] */
    private $routes;

    /** @var string Regex representation, used when resolving routing */
    private $regex;

    /** @var array Arguments taken from wildcard matches */
    private $arguments;

    public function __construct(Router $router = null)
    {
        if (isset($router)) {
            $this->routes = $router;
        } else {
            $pathHandle = new PathHandle('cache:router');
            $cache = new FileReturnRead($pathHandle);
            $this->routes = $cache->raw();
        }
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
            $resolver = new Resolver($route);
            if ($resolver->isUnserialized) {
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
