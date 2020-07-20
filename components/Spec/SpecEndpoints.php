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

namespace Chevere\Components\Spec;

use Chevere\Components\DataStructures\Traits\DsMapTrait;
use Chevere\Exceptions\Core\OutOfBoundsException;
use Chevere\Interfaces\Spec\SpecEndpointsInterface;
use Chevere\Interfaces\Spec\Specs\RouteEndpointSpecInterface;

final class SpecEndpoints implements SpecEndpointsInterface
{
    use DsMapTrait;

    public function withPut(RouteEndpointSpecInterface $routeEndpointSpec): SpecEndpointsInterface
    {
        $new = clone $this;
        $new->map->put(
            $routeEndpointSpec->key(),
            $routeEndpointSpec->jsonPath()
        );

        return $new;
    }

    public function hasKey(string $methodName): bool
    {
        return $this->map->hasKey(/** @scrutinizer ignore-type */ $methodName);
    }

    public function get(string $methodName): string
    {
        try {
            $return = $this->map->get($methodName);
        }
        // @codeCoverageIgnoreStart
        catch (\OutOfBoundsException $e) {
            throw new OutOfBoundsException(null, 0, $e);
        }
        // @codeCoverageIgnoreEnd
        return $return;
    }
}
