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

use Chevere\Components\Http\Interfaces\MethodInterface;
use Chevere\Components\Spec\RouteEndpointSpec;
use Countable;

interface SpecIndexInterface extends Countable
{
    public function withOffset(
        int $id,
        RouteEndpointSpec $routeEndpointSpec
    ): SpecIndexInterface;

    public function has(int $id, MethodInterface $method): bool;

    public function get(int $id, MethodInterface $method): string;
}
