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

use Chevere\Components\DataStructure\Traits\MapTrait;
use Chevere\Components\Message\Message;
use Chevere\Exceptions\Core\OutOfBoundsException;
use Chevere\Exceptions\Core\TypeException;
use Chevere\Interfaces\Spec\Specs\RouteEndpointSpecInterface;
use Chevere\Interfaces\Spec\Specs\RouteEndpointSpecsInterface;
use TypeError;

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
     * @throws TypeException
     * @throws OutOfBoundsException
     */
    public function get(string $methodName): RouteEndpointSpecInterface
    {
        try {
            return $this->map->get($methodName);
        }
        // @codeCoverageIgnoreStart
        catch (TypeError $e) {
            throw new TypeException(previous: $e);
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
