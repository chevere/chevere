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

use Chevere\Components\DataStructures\Traits\DsMapTrait;
use Chevere\Components\Message\Message;
use Chevere\Exceptions\Core\OutOfBoundsException;
use Chevere\Interfaces\Spec\Specs\RoutableSpecInterface;
use Chevere\Interfaces\Spec\Specs\RoutableSpecsInterface;

final class RoutableSpecs implements RoutableSpecsInterface
{
    use DsMapTrait;

    public function withPut(RoutableSpecInterface $routableSpec): RoutableSpecsInterface
    {
        $new = clone $this;
        /** @var \Ds\TKey $key */
        $key = $routableSpec->key();
        $new->map->put($key, $routableSpec);

        return $new;
    }

    public function has(string $routeName): bool
    {
        return $this->map->hasKey($routeName);
    }

    public function get(string $routeName): RoutableSpecInterface
    {
        /** @var RoutableSpecInterface $return */
        try {
            $return = $this->map->get($routeName);
        } catch (\OutOfBoundsException $e) {
            throw new OutOfBoundsException(
                (new Message('Route name %routeName% not found'))
                    ->code('%routeName%', $routeName)
            );
        }

        return $return;
    }
}
