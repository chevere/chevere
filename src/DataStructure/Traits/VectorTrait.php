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

use function Chevere\VariableSupport\deepCopy;
use Iterator;

trait VectorTrait
{
    private array $vector = [];

    private int $count = 0;

    public function __clone()
    {
        /** @var array<mixed> $copy */
        $copy = deepCopy($this->vector);
        /** @phpstan-ignore-next-line */
        $this->vector = $copy;
    }

    public function keys(): array
    {
        if ($this->count === 0) {
            return [];
        }

        return range(0, $this->count - 1);
    }

    public function count(): int
    {
        return $this->count;
    }

    public function getIterator(): Iterator
    {
        foreach ($this->vector as $value) {
            yield $value;
        }
    }
}
