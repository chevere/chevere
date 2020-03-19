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

namespace Chevere\Components\Spec\Interfaces;

use Chevere\Components\Spec\RouteEndpointSpec;
use Chevere\Components\Spec\SpecIndexMap;

interface SpecIndexInterface
{
    public function withOffset(
        int $id,
        RouteEndpointSpec $routeEndpointSpec
    ): SpecIndexInterface;

    public function specIndexMap(): SpecIndexMap;

    public function has(int $id, string $methodName): bool;

    public function get(int $id, string $methodName): string;
}
