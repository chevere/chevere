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

/**
 * Describes the component in charge of defining a mapped interface.
 */
// @phpstan-ignore-next-line
interface MappedInterface extends Countable, StringKeysInterface, GetIteratorInterface
{
    public function keys(): array;

    public function count(): int;

    public function getIterator(): Iterator;
}
