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

use Chevere\Interfaces\DataStructures\DsMapInterface;
use Generator;

interface RouteEndpointSpecsInterface extends DsMapInterface
{
    /**
     * @return Generator<string, RouteEndpointSpecInterface>
     */
    public function getGenerator(): Generator;

    public function withPut(RouteEndpointSpecInterface $routeEndpointSpec): RouteEndpointSpecsInterface;

    public function hasKey(string $key): bool;

    public function get(string $key): RouteEndpointSpecInterface;
}
