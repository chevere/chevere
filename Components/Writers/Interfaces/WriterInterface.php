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

namespace Chevere\Components\Writers\Interfaces;

use Chevere\Components\Common\Interfaces\ToStringInterface;

interface WriterInterface extends ToStringInterface
{
    /**
     * Writes the given string.
     */
    public function write(string $string): void;

    /**
     * Returns the contents written. Should not alter the file cursor.
     */
    public function toString(): string;
}
