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

namespace Chevere\Spec\Specs;

use Chevere\DataStructure\Traits\MapTrait;
use Chevere\Message\Message;
use Chevere\Spec\Interfaces\Specs\RouteEndpointSpecInterface;
use Chevere\Spec\Interfaces\Specs\RouteEndpointSpecsInterface;
use Chevere\Throwable\Errors\TypeError;
use Chevere\Throwable\Exceptions\OutOfBoundsException;

final class RouteEndpointSpecs implements RouteEndpointSpecsInterface
{
    use MapTrait;

    public function withPut(RouteEndpointSpecInterface $routeEndpointSpec): RouteEndpointSpecsInterface
    {
        $new = clone $this;
        $key = $routeEndpointSpec->key();
        $new->map = $new->map->withPut($key, $routeEndpointSpec);

        return $new;
    }

    public function has(string $methodName): bool
    {
        return $this->map->has($methodName);
    }

    /**
     * @throws TypeError
     * @throws OutOfBoundsException
     */
    public function get(string $methodName): RouteEndpointSpecInterface
    {
        try {
            return $this->map->get($methodName);
        }
        // @codeCoverageIgnoreStart
        catch (\TypeError $e) {
            throw new TypeError(previous: $e);
        }
        // @codeCoverageIgnoreEnd
        catch (\OutOfBoundsException $e) {
            throw new OutOfBoundsException(
                (new Message('Method name %methodName% not found'))
                    ->code('%methodName%', $methodName)
            );
        }
    }
}
