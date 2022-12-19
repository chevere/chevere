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
use IteratorAggregate;

/**
 * Describes the component in charge of defining a mapped interface.
 *
 * @template TValue
 * @extends IteratorAggregate<string, TValue>
 */
interface MappedInterface extends Countable, StringKeysInterface, IteratorAggregate
{
    public function count(): int;
}
