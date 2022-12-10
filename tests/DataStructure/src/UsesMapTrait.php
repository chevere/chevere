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

namespace Chevere\Tests\DataStructure\src;

use Chevere\DataStructure\Map;
use Chevere\DataStructure\Traits\MapTrait;

final class UsesMapTrait
{
    use MapTrait;

    public function withPut(object ...$object): static
    {
        $new = clone $this;
        $new->map = $new->map->withPut(...$object);

        return $new;
    }

    public function map(): Map
    {
        return $this->map;
    }
}
