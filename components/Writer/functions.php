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

use Chevere\Components\Instances\WritersInstance;
use Chevere\Components\Message\Message;
use Chevere\Exceptions\Core\Exception;
use Chevere\Exceptions\Core\LogicException;
use Chevere\Exceptions\Core\RuntimeException;
use Chevere\Exceptions\Filesystem\FilesystemException;
use Chevere\Interfaces\Filesystem\FileInterface;
use Chevere\Interfaces\Writer\WriterInterface;
use Chevere\Interfaces\Writer\WritersInterface;
use Laminas\Diactoros\Stream;
use Psr\Http\Message\StreamInterface;
use Throwable;
use function Safe\fopen;
use function Safe\fwrite;
use function Safe\rewind;

/**
 * @codeCoverageIgnore
 */
function writers(): WritersInterface
{
    return WritersInstance::get();
}

/**
 * @throws LogicException
 */
function streamFor(string $stream, string $mode): StreamInterface
{
    try {
        return new Stream(...func_get_args());
    } catch (Throwable $e) {
        throw new LogicException(null, 0, $e);
    }
}

/**
 * @throws RuntimeException
 */
function streamForString(string $content = ''): StreamInterface
{
    $type = 'stream';
    $stream = 'php://temp';
    try {
        $resource = fopen($stream, 'r+');
        fwrite($resource, $content);
        rewind($resource);
    } catch (Throwable $e) {
        throw new RuntimeException(
            null,
            $e->getCode(),
            $e
        );
    }
    if (!is_resource($resource)) {
        throw new RuntimeException(
            (new Message('Unable to create resource at %stream%'))
                ->code('%stream%', $stream)
        );
    }
    if (get_resource_type($resource) !== $type) {
        throw new RuntimeException(
            (new Message('Resource at %stream% is not of type %type%'))
                ->code('%stream%', $stream)
                ->code('%type%', $type)
        );
    }

    return new Stream($resource);
}

/**
 * @throws FilesystemException
 * @throws LogicException
 */
function writerForFile(FileInterface $file, string $mode): WriterInterface
{
    try {
        if (!$file->exists()) {
            $file->create();
        }
        $file->assertExists();
    } catch (Exception $e) {
        throw new FilesystemException(null, $e->getCode(), $e);
    }
    if (!$file->path()->isWritable()) {
        throw new LogicException(
            (new Message('File %filename% is not writable'))
                ->code('%filename%', $file->path()->absolute())
        );
    }
    try {
        return new StreamWriter(
            new Stream($file->path()->absolute(), $mode)
        );
    } catch (Throwable $e) {
        throw new RuntimeException(null, $e->getCode(), $e);
    }
}
