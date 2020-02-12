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

namespace Chevere\Components\Writers;

use Chevere\Components\Writers\Interfaces\StreamWriterInterface;

final class SilentStreamWriter implements StreamWriterInterface
{
    public function __construct()
    {
    }

    public function write(string $string): void
    {
    }
}
