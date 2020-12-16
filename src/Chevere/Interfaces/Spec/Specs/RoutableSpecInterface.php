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

namespace Chevere\Interfaces\Spec\Specs;

use Chevere\Components\Spec\Specs\RouteEndpointSpecs;
use Chevere\Interfaces\Router\RoutableInterface;
use Chevere\Interfaces\Spec\SpecDirInterface;
use Chevere\Interfaces\Spec\SpecInterface;

/**
 * Describes the component in charge of defining a routable spec.
 */
interface RoutableSpecInterface extends SpecInterface
{
    public function __construct(SpecDirInterface $specGroupPath, RoutableInterface $routable);

    /**
     * Provides access to a cloned `RouteEndpointSpecs` which doesn't affects the object instance used in `toArray`.
     */
    public function clonedRouteEndpointSpecs(): RouteEndpointSpecs;
}
