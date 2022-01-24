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
use Chevere\Spec\Interfaces\Specs\RoutableSpecsInterface;
use Chevere\Spec\Interfaces\Specs\RouteSpecInterface;
use Chevere\Throwable\Errors\TypeError;
use Chevere\Throwable\Exceptions\OutOfBoundsException;

final class RoutableSpecs implements RoutableSpecsInterface
{
    use MapTrait;

    public function withPut(RouteSpecInterface $routableSpec): RoutableSpecsInterface
    {
        $new = clone $this;
        $key = $routableSpec->key();
        $new->map = $new->map->withPut($key, $routableSpec);

        return $new;
    }

    public function has(string $routeName): bool
    {
        return $this->map->has($routeName);
    }

    /**
     * @throws TypeError
     * @throws OutOfBoundsException
     */
    public function get(string $routeName): RouteSpecInterface
    {
        try {
            return $this->map->get($routeName);
        }
        // @codeCoverageIgnoreStart
        catch (\TypeError $e) {
            throw new TypeError(previous: $e);
        }
        // @codeCoverageIgnoreEnd
        catch (\OutOfBoundsException $e) {
            throw new OutOfBoundsException(
                (new Message('Route name %routeName% not found'))
                    ->code('%routeName%', $routeName)
            );
        }
    }
}
