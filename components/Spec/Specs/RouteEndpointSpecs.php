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

namespace Chevere\Components\Spec\Specs;

use Chevere\Components\DataStructures\Traits\MapTrait;
use Chevere\Components\Message\Message;
use Chevere\Components\Spec\Specs\RouteEndpointSpec;
use Chevere\Exceptions\Core\OutOfBoundsException;
use Chevere\Interfaces\Spec\Specs\RouteEndpointSpecInterface;
use Chevere\Interfaces\Spec\Specs\RouteEndpointSpecsInterface;

final class RouteEndpointSpecs implements RouteEndpointSpecsInterface
{
    use MapTrait;

    public function withPut(RouteEndpointSpecInterface $routeEndpointSpec): RouteEndpointSpecsInterface
    {
        $new = clone $this;
        /** @var \Ds\TKey $key */
        $key = $routeEndpointSpec->key();
        $new->map->put($key, $routeEndpointSpec);

        return $new;
    }

    public function has(string $methodName): bool
    {
        return $this->map->hasKey($methodName);
    }

    public function get(string $methodName): RouteEndpointSpecInterface
    {
        /** @var RouteEndpointSpec $return */
        try {
            $return = $this->map->get($methodName);
        } catch (\OutOfBoundsException $e) {
            throw new OutOfBoundsException(
                (new Message('Method name %methodName% not found'))
                    ->code('%methodName%', $methodName)
            );
        }

        return $return;
    }
}
