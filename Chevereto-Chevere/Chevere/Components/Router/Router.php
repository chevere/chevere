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
use Chevere\Components\Router\Exception\RegexPropertyRequiredException;
use Chevere\Components\Router\Exception\RouteNotFoundException;
use Chevere\Components\Serialize\Unserialize;
use Chevere\Contracts\Route\RouteContract;
use Chevere\Contracts\Router\RouterContract;
use Chevere\Contracts\Router\RouterPropertiesContract;
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

    public function __construct()
    {
        $this->arguments = [];
    }

    public function withProperties(RouterPropertiesContract $properties): RouterContract
    {
        $new = clone $this;
        $new->properties = $properties;

        return $new;
    }

    public function hasProperties(): bool
    {
        return isset($this->properties);
    }

    public function arguments(): array
    {
        return $this->arguments;
    }

    public function canResolve(): bool
    {
        return $this->hasProperties() && '' != $this->properties->regex();
    }

    public function resolve(string $pathInfo): RouteContract
    {
        // if (!$this->canResolve()) {
        //     throw new RegexPropertyRequiredException(
        //         (new Message('Instance of %className% requires a %property% property when calling %method%'))
        //             ->code('%className%', __CLASS__)
        //             ->code('%property%', 'regex')
        //             ->code('%method%', __METHOD__)
        //             ->toString()
        //     );
        // }
        if (preg_match($this->properties->regex(), $pathInfo, $matches)) {
            return $this->resolver($matches);
        }
        throw new RouteNotFoundException(
            (new Message('No route defined for %path%'))
                ->code('%path%', '' != $pathInfo ? $pathInfo : '(empty string)')
                ->toString()
        );
    }

    private function resolver(array $matches): RouteContract
    {
        $id = $matches['MARK'];
        unset($matches['MARK']);
        array_shift($matches);
        $route = $this->properties->routes()[$id];
        // is string when the route is cached
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
            $this->properties->routes()[$id] = $route;
        }
        $this->arguments = [];
        if ($route->hasWildcardCollection()) {
            foreach ($matches as $pos => $val) {
                $wildcard = $route->wildcardCollection()->getPos($pos);
                $this->arguments[$wildcard->name()] = $val;
            }
        }

        return $route;
    }
}
