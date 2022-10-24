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

use Ds\Vector;
use Iterator;

trait VectorTrait
{
    private Vector $vector;

    public function __construct()
    {
        $this->vector = new Vector();
    }

    public function __clone()
    {
        /** @phpstan-ignore-next-line */
        $this->vector = $this->vector->copy();
    }

    public function keys(): array
    {
        if (count($this->vector) === 0) {
            return [];
        }

        return range(0, $this->vector->count() - 1);
    }

    public function count(): int
    {
        return $this->vector->count();
    }

    public function getIterator(): Iterator
    {
        foreach ($this->vector->toArray() as $value) {
            yield $value;
        }
    }
}
