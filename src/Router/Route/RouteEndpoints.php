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

namespace Chevere\Router\Route;

use Chevere\DataStructure\Traits\MapTrait;
use Chevere\Message\Message;
use Chevere\Router\Interfaces\Route\RouteEndpointInterface;
use Chevere\Router\Interfaces\Route\RouteEndpointsInterface;
use Chevere\Throwable\Exceptions\OutOfBoundsException;

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
