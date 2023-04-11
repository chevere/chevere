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
use Iterator;

trait VectorTrait
{
    private VectorInterface $vector;

    public function __construct()
    {
        $this->vector = new Vector();
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
