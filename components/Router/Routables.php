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

namespace Chevere\Components\Router;

use Chevere\Components\DataStructures\Traits\DsMapTrait;
use Chevere\Interfaces\Router\RoutableInterface;
use Chevere\Interfaces\Router\RoutablesInterface;

final class Routables implements RoutablesInterface
{
    use DsMapTrait;

    public function withPut(RoutableInterface $routable): RoutablesInterface
    {
        $new = clone $this;
        /** @var \Ds\TKey $key */
        $key = $routable->route()->name()->toString();
        $new->map->put($key, $routable);

        return $new;
    }

    public function hasKey(string $name): bool
    {
        /** @var \Ds\TKey $key */
        $key = $name;

        return $this->map->hasKey($key);
    }

    /**
     * @throws OutOfBoundsException
     */
    public function get(string $name): RoutableInterface
    {
        /**
         * @var RoutableInterface $return
         * @var \Ds\TKey $name
         */
        $return = $this->map->get($name);

        return $return;
    }
}
