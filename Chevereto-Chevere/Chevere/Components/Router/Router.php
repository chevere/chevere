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
use Chevere\Components\Serialize\Unserialize;
use Chevere\Contracts\Route\RouteContract;
use Chevere\Contracts\Router\RouterContract;
use Chevere\Contracts\Router\RouterPropertiesContract;
use Psr\Http\Message\UriInterface;
use TypeError;

/**
 * Router does routing.
 */
final class Router implements RouterContract
{
    /** @var array Arguments taken from wildcard matches */
    private $arguments;

    /** @var RouterPropertiesContract */
    private $properties;

    /**
     * {@inheritdoc}
     */
    public function __construct()
    {
        $this->arguments = [];
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
    public function arguments(): array
    {
        return $this->arguments;
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
    public function resolve(UriInterface $uri): RouteContract
    {
        if (preg_match($this->properties->regex()->toString(), $uri->getPath(), $matches)) {
            return $this->resolver($matches);
        }
        throw new RouteNotFoundException(
            (new Message('No route defined for %path%'))
                ->code('%path%', $uri->getPath())
                ->toString()
        );
    }

    private function resolver(array $matches): RouteContract
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
        $this->arguments = [];
        if ($route->hasWildcardCollection()) {
            foreach ($matches as $pos => $val) {
                $this->arguments[$route->wildcardCollection()->getPos($pos)->name()] = $val;
            }
        }

        return $route;
    }
}
