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

namespace Chevere\Router\Interfaces\Route;

/**
 * Describes the component in charge of decorate a route.
 */
interface RouteDecoratorInterface
{
    public function __construct(RouteLocatorInterface $locator);

    /**
     * Provides access to the route name.
     */
    public function locator(): RouteLocatorInterface;

    /**
     * Return an instance with the specified `$wildcards` instance.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified `$wildcards` instance.
     */
    public function withWildcards(RouteWildcardsInterface $wildcards): self;

    /**
     * Provides access to the route wildcards.
     */
    public function wildcards(): RouteWildcardsInterface;
}
