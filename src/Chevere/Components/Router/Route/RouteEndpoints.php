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

namespace Chevere\Components\Router\Route;

use Chevere\Components\DataStructure\Traits\MapTrait;
use Chevere\Components\Message\Message;
use Chevere\Exceptions\Core\OutOfBoundsException;
use Chevere\Interfaces\Router\Route\RouteEndpointInterface;
use Chevere\Interfaces\Router\Route\RouteEndpointsInterface;

final class RouteEndpoints implements RouteEndpointsInterface
{
    use MapTrait;

    public function withPut(RouteEndpointInterface $routeEndpoint): RouteEndpointsInterface
    {
        $new = clone $this;
        $new->map = $new->map->withPut(
            $routeEndpoint->method()->name(),
            $routeEndpoint
        );

        return $new;
    }

    public function hasKey(string $key): bool
    {
        return $this->map->has($key);
    }

    public function get(string $key): RouteEndpointInterface
    {
        try {
            /** @var RouteEndpointInterface $return */
            $return = $this->map->get($key);
        } catch (\OutOfBoundsException $e) {
            throw new OutOfBoundsException(
                (new Message('Key %key% not found'))
                    ->code('%key%', $key)
            );
        }

        return $return;
    }
}
