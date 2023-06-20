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

namespace Chevere\Writer\Interfaces;

use Chevere\Throwable\Exceptions\RuntimeException;
use Stringable;

/**
 * Describes the component in charge of writing strings.
 */
interface WriterInterface extends Stringable
{
    /**
     * Returns the contents written. Must not alter the file cursor.
     */
    public function __toString(): string;

    /**
     * Writes the given string.
     *
     * @throws RuntimeException
     */
    public function write(string $string): void;
}
