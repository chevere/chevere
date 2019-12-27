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

use Chevere\Components\Message\Message;
use Chevere\Components\Router\Exception\RouteNotFoundException;
use Chevere\Components\Router\Exceptions\RouterException;
use Chevere\Components\Serialize\Unserialize;
use Chevere\Contracts\Route\RouteContract;
use Chevere\Contracts\Router\RoutedContract;
use Chevere\Contracts\Router\RouterContract;
use Chevere\Contracts\Router\RouterPropertiesContract;
use Psr\Http\Message\UriInterface;
use Throwable;
use TypeError;

/**
 * Router does routing.
 */
final class Router implements RouterContract
{
    private ?RouterPropertiesContract $properties;

    /**
     * {@inheritdoc}
     */
    public function __construct()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function withProperties(RouterPropertiesContract $properties): RouterContract
    {
        $new = clone $this;
        $new->properties = $properties;

        return $new;
    }

    /**
     * {@inheritdoc}
     */
    public function hasProperties(): bool
    {
        return isset($this->properties);
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
    public function canResolve(): bool
    {
        return $this->hasProperties() && $this->properties->hasRegex();
    }

    /**
     * {@inheritdoc}
     */
    public function resolve(UriInterface $uri): RoutedContract
    {
        try {
            $pregMatch = preg_match($this->properties->regex(), $uri->getPath(), $matches);
        } catch (Throwable $e) {
            throw new RouterException($e->getMessage());
        }
        if ($pregMatch) {
            if (!isset($matches['MARK'])) {
                throw new RouterException(
                    (new Message('Invalid regex pattern, missing %mark% member'))
                        ->code('%mark%', 'MARK')
                        ->toString()
                );
            }

            return $this->resolver($matches);
        }
        throw new RouteNotFoundException(
            (new Message('No route defined for %path%'))
                ->code('%path%', $uri->getPath())
                ->toString()
        );
    }

    /**
     * @throws UnserializeException if the route string object can't be unserialized
     * @throws TypeError            if the found route doesn't implement the RouteContract
     */
    private function resolver(array $matches): RoutedContract
    {
        $id = $matches['MARK'];
        unset($matches['MARK']);
        array_shift($matches);
        $route = $this->properties->routes()[$id];
        // is string when the route is serialized (cached)
        if (is_string($route)) {
            $unserialize = new Unserialize($route);
            $route = $unserialize->var();
            if (!($route instanceof RouteContract)) {
                throw new TypeError(
                    (new Message("Serialized variable doesn't implements %contract%, type %provided% provided"))
                        ->code('%contract%', RouteContract::class)
                        ->code('%provided%', $unserialize->type()->typeHinting())
                        ->toString()
                );
            }
            $routes = $this->properties->routes();
            $routes[$id] = $route;
            $this->properties = $this->properties
                ->withRoutes($routes);
        }
        $wildcards = [];
        if ($route->hasWildcardCollection()) {
            foreach ($matches as $pos => $val) {
                $wildcards[$route->wildcardCollection()->getPos($pos)->name()] = $val;
            }
        }

        return new Routed($route, $wildcards);
    }
}
