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

use Chevere\Writer\Interfaces\WritersInterface;
use InvalidArgumentException;
use Nyholm\Psr7\Stream;
use Psr\Http\Message\StreamInterface;
use Throwable;
use function Chevere\Message\message;
use function Safe\fopen;

/**
 * @codeCoverageIgnore
 */
function writers(): WritersInterface
{
    return WritersInstance::get();
}

/**
 * @codeCoverageIgnore
 *
 * @throws InvalidArgumentException
 */
function streamFor(string $uri, string $mode): StreamInterface
{
    try {
        return Stream::create(fopen($uri, $mode));
    } catch (Throwable $e) {
        throw new InvalidArgumentException(
            previous: $e,
            message: (string) message(
                'Unable to create stream for `%uri%`',
                uri: $uri
            )
        );
    }
}

/**
 * @codeCoverageIgnore
 */
function streamTemp(string $content = ''): StreamInterface
{
    try {
        return Stream::create($content);
    } catch (Throwable $e) {
        throw new InvalidArgumentException(
            previous: $e,
            message: (string) message('Unable to create temp stream')
        );
    }
}
