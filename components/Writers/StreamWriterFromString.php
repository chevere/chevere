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

use Chevere\Interfaces\Writers\WriterInterface;
use Laminas\Diactoros\Stream;
use Laminas\Diactoros\StreamFactory;
use Psr\Http\Message\StreamInterface;

/**
 * @codeCoverageIgnore
 */
final class StreamWriterFromString implements WriterInterface
{
    private StreamInterface $stream;

    public function __construct(string $string, string $mode)
    {
        if ($string == '') {
            $this->stream = (new StreamFactory)->createStream('');
        } else {
            $this->stream = new Stream($string, $mode);
        }
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
