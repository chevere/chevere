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

namespace Chevere\Spec\Interfaces\Specs;

use Chevere\Filesystem\Interfaces\DirInterface;
use Chevere\Router\Interfaces\Route\RouteInterface;
use Chevere\Spec\Interfaces\SpecInterface;
use Chevere\Spec\Specs\RouteEndpointSpecs;

/**
 * Describes the component in charge of defining a route spec.
 */
interface RouteSpecInterface extends SpecInterface
{
    public function __construct(
        DirInterface $specDir,
        RouteInterface $route,
        string $repository
    );

    /**
     * Provides access to a cloned `RouteEndpointSpecs` which doesn't affects the object instance used in `toArray`.
     */
    public function clonedRouteEndpointSpecs(): RouteEndpointSpecs;
}
