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

use Iterator;
use IteratorAggregate;

/**
 * Describes the component in charge of providing access to the iterator.
 *
 * @template-covariant TKey
 * @template-covariant TValue
 * @extends IteratorAggregate<TKey, TValue>
 */
interface GetIteratorInterface extends IteratorAggregate
{
    public function getIterator(): Iterator;
}
