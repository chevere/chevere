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

namespace Chevere\Components\Router\Interfaces;

use Chevere\Components\Regex\Interfaces\RegexInterface;
use TypeError;
use Chevere\Components\Serialize\Exceptions\UnserializeException;
use Chevere\Components\Router\Exceptions\RouteNotFoundException;
use Chevere\Components\Router\Exceptions\RouterException;
use Psr\Http\Message\UriInterface;

interface RouterInterface
{
    const CACHE_ID = 'router';

    /**
     * Return an instance with the specified RegexInterface.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified RegexInterface.
     */
    public function withRegex(RouterRegexInterface $regex): RouterInterface;

    public function hasRegex(): bool;

    /**
     * Provides access to the instance regex.
     */
    public function regex(): RouterRegexInterface;

    /**
     * Return an instance with the specified index.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified index.
     */
    public function withIndex(RouterIndexInterface $index): RouterInterface;

    public function hasIndex(): bool;

    /**
     * Provides access to the instance index.
     */
    public function index(): RouterIndexInterface;

    /**
     * Return an instance with the specified named.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified named.
     */
    public function withNamed(RouterNamedInterface $name): RouterInterface;

    public function hasNamed(): bool;

    /**
     * Provides access to the instance index.
     */
    public function named(): RouterNamedInterface;

    /**
     * Return an instance with the specified group.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified group.
     */
    public function withGroups(RouterGroupsInterface $groups): RouterInterface;

    public function hasGroups(): bool;

    /**
     * Provides access to the instance group.
     */
    public function groups(): RouterGroupsInterface;

    /**
     * Returns a boolean indicating whether the instance can try to resolve routing.
     */
    public function canResolve(): bool;

    /**
     * Returns a RoutedInterface for the given UriInterface.
     *
     * @throws RouterException        if the router encounters any fatal error
     * @throws UnserializeException   if the route string object can't be unserialized
     * @throws TypeError              if the found route doesn't implement the RouteInterface
     * @throws RouteNotFoundException if no route resolves the given UriInterface
     */
    public function resolve(UriInterface $uri): RoutedInterface;
}
