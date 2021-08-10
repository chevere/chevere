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
use Chevere\Interfaces\Spec\Specs\RoutableSpecsInterface;
use Chevere\Interfaces\Spec\Specs\RouteSpecInterface;
use TypeError;

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
     * @throws TypeException
     * @throws OutOfBoundsException
     */
    public function get(string $routeName): RouteSpecInterface
    {
        try {
            return $this->map->get($routeName);
        }
        // @codeCoverageIgnoreStart
        catch (TypeError $e) {
            throw new TypeException(previous: $e);
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
