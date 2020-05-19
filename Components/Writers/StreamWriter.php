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

use Chevere\Components\Writers\Interfaces\WriterInterface;
use Psr\Http\Message\StreamInterface;

final class StreamWriter implements WriterInterface
{
    private StreamInterface $stream;

    public function __construct(StreamInterface $stream)
    {
        $this->stream = $stream;
    }

    public function write(string $string): void
    {
        $this->stream->write($string);
    }

    public function toString(): string
    {
        return $this->stream->__toString();
    }
}
