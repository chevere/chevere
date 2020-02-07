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
use Chevere\Components\Router\Exceptions\RouteCacheNotFoundException;
use Chevere\Components\Router\Interfaces\RouteCacheInterface;
use Chevere\Components\Router\Interfaces\RoutedInterface;
use Chevere\Components\Router\Interfaces\RouterGroupsInterface;
use Chevere\Components\Router\Interfaces\RouterIndexInterface;
use Chevere\Components\Router\Interfaces\RouterInterface;
use Chevere\Components\Router\Interfaces\RouterNamedInterface;
use Chevere\Components\Router\Interfaces\RouterRegexInterface;
use Psr\Http\Message\UriInterface;
use Throwable;

/**
 * The Chevere Router
 */
final class Router implements RouterInterface
{
    private RouteCacheInterface $cache;

    private RouterRegexInterface $regex;

    private RouterIndexInterface $index;

    private RouterNamedInterface $named;

    private RouterGroupsInterface $groups;

    public function __construct(RouteCacheInterface $cache)
    {
        $this->cache = $cache;
    }

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

    /**
     * @throws RouterException
     * @throws RouteNotFoundException
     */
    public function resolve(UriInterface $uri): RoutedInterface
    {
        try {
            if (preg_match($this->regex->regex()->toString(), $uri->getPath(), $matches)) {
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
     * @throws RouteCacheNotFoundException
     */
    private function resolver(array $matches): RoutedInterface
    {
        $id = (int) $matches['MARK'];
        unset($matches['MARK']);
        array_shift($matches);
        $route = $this->cache->get($id);
        $wildcards = [];
        if ($route->hasWildcardCollection()) {
            foreach ($matches as $pos => $val) {
                $wildcards[$route->wildcardCollection()->getPos($pos)->name()] = $val;
            }
        }

        return new Routed($route, $wildcards);
    }
}
