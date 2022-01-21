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

namespace Chevere\Components\Writer;

use Chevere\Interfaces\Writer\WriterInterface;

final class NullWriter implements WriterInterface
{
    public function __construct()
    {
        // null
    }

    public function write(string $string): void
    {
        // null
    }

    public function __toString(): string
    {
        return '';
    }
}
