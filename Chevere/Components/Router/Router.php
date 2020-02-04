<?php

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevere.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Chevere\Components\Router;

use Chevere\Components\Message\Message;
use Chevere\Components\Router\Exceptions\RouteNotFoundException;
use Chevere\Components\Router\Exceptions\RouterException;
use Chevere\Components\Serialize\Unserialize;
use Chevere\Components\Route\Interfaces\RouteInterface;
use Chevere\Components\Router\Interfaces\RoutedInterface;
use Chevere\Components\Router\Interfaces\RouterGroupsInterface;
use Chevere\Components\Router\Interfaces\RouterIndexInterface;
use Chevere\Components\Router\Interfaces\RouterInterface;
use Chevere\Components\Router\Interfaces\RouterNamedInterface;
use Chevere\Components\Router\Interfaces\RouterRegexInterface;
use LogicException;
use Psr\Http\Message\UriInterface;
use Throwable;
use TypeError;

/**
 * The Chevere Router
 */
final class Router implements RouterInterface
{
    private RouterRegexInterface $regex;

    private RouterIndexInterface $index;

    private RouterNamedInterface $named;

    private RouterGroupsInterface $groups;

    public function withRegex(RouterRegexInterface $regex): RouterInterface
    {
        $new = clone $this;
        $new->regex = $regex;

        return $new;
    }

    public function hasRegex(): bool
    {
        return isset($this->regex);
    }

    public function regex(): RouterRegexInterface
    {
        return $this->regex;
    }

    public function withIndex(RouterIndexInterface $index): RouterInterface
    {
        $new = clone $this;
        $new->index = $index;

        return $new;
    }

    public function hasIndex(): bool
    {
        return isset($this->index);
    }

    public function index(): RouterIndexInterface
    {
        return $this->index;
    }

    public function withNamed(RouterNamedInterface $name): RouterInterface
    {
        $new = clone $this;
        $new->named = $name;

        return $new;
    }

    public function hasNamed(): bool
    {
        return isset($this->named);
    }

    public function named(): RouterNamedInterface
    {
        return $this->named;
    }

    public function withGroups(RouterGroupsInterface $groups): RouterInterface
    {
        $new = clone $this;
        $new->groups = $groups;

        return $new;
    }

    public function hasGroups(): bool
    {
        return isset($this->groups);
    }

    public function groups(): RouterGroupsInterface
    {
        return $this->groups;
    }

    public function canResolve(): bool
    {
        return isset($this->regex);
    }

    public function resolve(UriInterface $uri): RoutedInterface
    {
        try {
            if (preg_match($this->properties->regex(), $uri->getPath(), $matches)) {
                if (!isset($matches['MARK'])) {
                    throw new LogicException(
                        (new Message('Invalid regex pattern, missing %mark% member'))
                            ->code('%mark%', 'MARK')
                            ->toString()
                    );
                }

                return $this->resolver($matches);
            }
        } catch (Throwable $e) {
            throw new RouterException($e->getMessage(), $e->getCode(), $e);
        }
        throw new RouteNotFoundException(
            (new Message('No route defined for %path%'))
                ->code('%path%', $uri->getPath())
                ->toString()
        );
    }

    /**
     * @throws UnserializeException if the route string object can't be unserialized
     * @throws TypeError            if the found route doesn't implement the RouteInterface
     */
    private function resolver(array $matches): RoutedInterface
    {
        $id = $matches['MARK'];
        unset($matches['MARK']);
        array_shift($matches);
        // $route = $this->properties->routes()[$id];
        // is string when the route is serialized (cached)
        if (is_string($route)) {
            $unserialize = new Unserialize($route);
            $route = $unserialize->var();
            if (!($route instanceof RouteInterface)) {
                throw new TypeError(
                    (new Message("Serialized variable doesn't implements %contract%, type %provided% provided"))
                        ->code('%contract%', RouteInterface::class)
                        ->code('%provided%', $unserialize->type()->typeHinting())
                        ->toString()
                );
            }
            // $routes = $this->properties->routes();
            // $routes[$id] = $route;
            // $this->properties = $this->properties
            //     ->withRoutes($routes);
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
