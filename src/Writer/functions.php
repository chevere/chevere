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

use function Chevere\Message\message;
use Chevere\Throwable\Exceptions\InvalidArgumentException;
use Chevere\Throwable\Exceptions\RuntimeException;
use Chevere\Writer\Interfaces\WritersInterface;
use Laminas\Diactoros\Stream;
use Psr\Http\Message\StreamInterface;
use function Safe\fopen;
use function Safe\fwrite;
use function Safe\rewind;
use Throwable;

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
function streamFor(string $stream, string $mode): StreamInterface
{
    try {
        return new Stream($stream, $mode);
    } catch (Throwable $e) {
        throw new InvalidArgumentException(
            previous: $e,
            message: message('Unable to create a stream for %stream% %mode%')
                ->withCode('%stream%', $stream)
                ->withCode('%mode%', $mode),
        );
    }
}

/**
 * @codeCoverageIgnore
 *
 * @throws RuntimeException
 */
function streamTemp(string $content = ''): StreamInterface
{
    $stream = 'php://temp';

    try {
        $resource = fopen($stream, 'r+');
        fwrite($resource, $content);
        rewind($resource);
    } catch (Throwable $e) {
        throw new RuntimeException(
            previous: $e,
            message: message('Unable to handle %stream% as stream resource')
                ->withCode('%stream%', $stream),
        );
    }
    if (!is_resource($resource)) {
        throw new RuntimeException(
            message('Unable to create resource at %stream%')
                ->withCode('%stream%', $stream)
        );
    }
    if (get_resource_type($resource) !== 'stream') {
        throw new RuntimeException(
            message('Resource at %stream% is not of type %type%')
                ->withCode('%stream%', $stream)
                ->withCode('%type%', 'stream')
        );
    }

    return new Stream($resource);
}
