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

namespace Chevere\Interfaces\Writer;

use Chevere\Interfaces\To\ToStringInterface;

/**
 * Describes the component in charge of writing strings.
 */
interface WriterInterface extends ToStringInterface
{
    /**
     * Writes the given string.
     */
    public function write(string $string): void;

    /**
     * Returns the contents written. Must not alter the file cursor.
     */
    public function toString(): string;
}
