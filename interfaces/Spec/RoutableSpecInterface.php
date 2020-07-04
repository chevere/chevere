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

namespace Chevere\Interfaces\Spec;

use Chevere\Components\Spec\Specs\RouteEndpointSpecs;
use Chevere\Interfaces\Router\RoutableInterface;
use Chevere\Interfaces\To\ToArrayInterface;

interface RoutableSpecInterface extends SpecInterface, ToArrayInterface
{
    public function __construct(SpecPathInterface $specGroupPath, RoutableInterface $routable);

    public function routeEndpointSpecs(): RouteEndpointSpecs;
}
