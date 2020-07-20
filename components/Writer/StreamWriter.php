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

use Chevere\Components\Message\Message;
use Chevere\Exceptions\Core\InvalidArgumentException;
use Chevere\Exceptions\Core\RuntimeException;
use Chevere\Interfaces\Writer\WriterInterface;
use Psr\Http\Message\StreamInterface;

/**
 * @codeCoverageIgnore
 */
final class StreamWriter implements WriterInterface
{
    private StreamInterface $stream;

    public function __construct(StreamInterface $stream)
    {
        if (!$stream->isWritable()) {
            throw new InvalidArgumentException(
                (new Message('Stream provided is not writable'))
            );
        }
        $this->stream = $stream;
    }

    public function write(string $string): void
    {
        try {
            $this->stream->write($string);
        } catch (\RuntimeException $e) {
            throw new RuntimeException(null, 0, $e);
        }
    }

    public function toString(): string
    {
        return $this->stream->__toString();
    }
}
