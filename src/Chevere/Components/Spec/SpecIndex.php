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

use Chevere\Components\DataStructure\Traits\MapTrait;
use Chevere\Components\Message\Message;
use Chevere\Exceptions\Core\OutOfBoundsException;
use Chevere\Interfaces\Spec\SpecIndexInterface;
use Chevere\Interfaces\Spec\Specs\RouteEndpointSpecInterface;

final class SpecIndex implements SpecIndexInterface
{
    use MapTrait;

    public function withAddedRoute(string $routeName, RouteEndpointSpecInterface $routeEndpointSpec): SpecIndexInterface
    {
        $new = clone $this;
        if ($new->map->has($routeName)) {
            /** @var SpecEndpoints $specEndpoints */
            $specEndpoints = $new->map->get($routeName);
        } else {
            $specEndpoints = new SpecEndpoints();
            $new->map = $new->map->withPut($routeName, $specEndpoints);
        }
        $specEndpoints = $specEndpoints->withPut($routeEndpointSpec);
        $new->map = $new->map->withPut($routeName, $specEndpoints);

        return $new;
    }

    public function has(string $routeName, string $methodName): bool
    {
        if ($this->map->has($routeName)) {
            /** @var SpecEndpoints $specEndpoints */
            $specEndpoints = $this->map->get($routeName);

            return $specEndpoints->has($methodName);
        }

        return false;
    }

    public function get(string $routeName, string $methodName): string
    {
        /** @var SpecEndpoints $specEndpoints */
        try {
            $specEndpoints = $this->map->get($routeName);

            return $specEndpoints->get($methodName);
        } catch (\OutOfBoundsException $e) {
            throw new OutOfBoundsException(
                (new Message('Method name %methodName% not found for route name %routeName%'))
                    ->code('%methodName%', $methodName)
                    ->code('%routeName%', $routeName)
            );
        }
    }
}
