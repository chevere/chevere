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

namespace Chevere\DataStructure\Interfaces;

use Countable;
use Iterator;
use IteratorAggregate;
use Traversable;

/**
 * Describes the component in charge of defining a mapped interface.
 *
 * @template-covariant TValue
 * @extends IteratorAggregate<string, TValue>
 */
interface MappedInterface extends Countable, StringKeysInterface, IteratorAggregate
{
    /**
     * @return Traversable<string, TValue>
     * @phpstan-return Iterator<string, TValue>
     */
    public function getIterator(): Iterator;
}
