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

namespace Chevere\Writer;

use Chevere\Writer\Interfaces\WriterInterface;

final class NullWriter implements WriterInterface
{
    public function __construct()
    {
        // null
    }

    public function __toString(): string
    {
        return '';
    }

    public function write(string $string): void
    {
        // null
    }
}
