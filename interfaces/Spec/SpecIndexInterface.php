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

use Chevere\Components\Spec\Specs\RouteEndpointSpec;
use Chevere\Interfaces\DataStructures\DsMapInterface;

interface SpecIndexInterface extends DsMapInterface
{
    public function withOffset(
        string $routeName,
        RouteEndpointSpec $routeEndpointSpec
    ): SpecIndexInterface;

    public function specIndexMap(): SpecIndexMapInterface;

    public function has(string $routeName, string $methodName): bool;

    public function get(string $routeName, string $methodName): string;
}
