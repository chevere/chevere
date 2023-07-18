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

namespace Chevere\DataStructure\Traits;

use Chevere\DataStructure\Map;

trait MapToArrayTrait
{
    private Map $map;

    public function toArray(): array
    {
        // @phpstan-ignore-next-line
        return iterator_to_array($this->map->getIterator(), true);
    }
}
