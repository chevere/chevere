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

use Chevere\DataStructure\Interfaces\VectorInterface;
use Chevere\DataStructure\Vector;
use function Chevere\VariableSupport\deepCopy;
use Iterator;

/**
 * @template TValue
 */
trait VectorTrait
{
    /**
     * @var Vector<TValue>
     */
    private Vector $vector;

    public function __construct()
    {
        $this->vector = new Vector();
    }

    public function __clone()
    {
        /** @var VectorInterface<TValue> $copy */
        $copy = deepCopy($this->vector);
        /** @phpstan-ignore-next-line */
        $this->vector = $copy;
    }

    /**
     * @return array<int>
     */
    public function keys(): array
    {
        return $this->vector->keys();
    }

    public function count(): int
    {
        return $this->vector->count();
    }

    public function getIterator(): Iterator
    {
        return $this->vector->getIterator();
    }
}
