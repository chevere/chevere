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

namespace Chevere\Components\DataStructures;

use Countable;
use Iterator;
use SplObjectStorage;

/**
 * Provides read-only for SplObjectStorage
 *
 * @codeCoverageIgnore
 */
abstract class SplObjectStorageRead implements Countable, Iterator
{
    protected SplObjectStorage $objects;

    abstract public function current(): object;

    public function getInfo()
    {
        return $this->objects->getInfo();
    }

    final public function __construct(SplObjectStorage $objects)
    {
        $this->objects = $objects;
    }

    final public function count(): int
    {
        return $this->objects->count();
    }

    final public function key(): int
    {
        return $this->objects->key();
    }

    final public function next(): void
    {
        $this->objects->next();
    }

    final public function rewind(): void
    {
        $this->objects->rewind();
    }

    final public function valid(): bool
    {
        return $this->objects->valid();
    }
}
