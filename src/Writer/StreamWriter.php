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

use Chevere\Message\Message;
use Chevere\Writer\Interfaces\WriterInterface;
use Chevere\Throwable\Exceptions\InvalidArgumentException;
use Chevere\Throwable\Exceptions\RuntimeException;
use Psr\Http\Message\StreamInterface;
use Throwable;

/**
 * @codeCoverageIgnore
 * @infection-ignore-all
 */
final class StreamWriter implements WriterInterface
{
    public function __construct(
        private StreamInterface $stream
    ) {
        if (!$this->stream->isWritable()) {
            throw new InvalidArgumentException(
                (new Message('Stream provided is not writable'))
            );
        }
    }

    public function write(string $string): void
    {
        try {
            $this->stream->write($string);
        } catch (Throwable $e) {
            throw new RuntimeException(
                previous: $e,
                message: (new Message('Unable to write provided string')),
            );
        }
    }

    public function __toString(): string
    {
        return $this->stream->__toString();
    }
}