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

namespace Chevere\Components\ThrowableHandler;

use Chevere\Components\Message\Message;
use Chevere\Components\ThrowableHandler\Documents\ThrowableHandlerConsoleDocument;
use Chevere\Components\ThrowableHandler\Documents\ThrowableHandlerHtmlDocument;
use Chevere\Components\ThrowableHandler\Documents\ThrowableHandlerPlainDocument;
use function Chevere\Components\Writer\streamFor;
use Chevere\Components\Writer\StreamWriter;
use Chevere\Components\Writer\WritersInstance;
use Chevere\Exceptions\Core\ErrorException;
use Chevere\Interfaces\ThrowableHandler\ThrowableHandlerDocumentInterface;
use LogicException;
use ReflectionClass;
use RuntimeException;
use Throwable;

/**
 * @codeCoverageIgnore
 * @throws ErrorException
 */
function errorsAsExceptions(int $severity, string $message, string $file, int $line): void
{
    throw new ErrorException(new Message($message), 0, $severity, $file, $line);
}

/**
 * @codeCoverageIgnore
 * @throws RuntimeException
 */
function plainHandler(Throwable $throwable): void
{
    handleExceptionAs($throwable, ThrowableHandlerPlainDocument::class);
}

/**
 * @codeCoverageIgnore
 * @throws RuntimeException
 */
function consoleHandler(Throwable $throwable): void
{
    handleExceptionAs($throwable, ThrowableHandlerConsoleDocument::class);
}

/**
 * @codeCoverageIgnore
 * @throws RuntimeException
 */
function htmlHandler(Throwable $throwable): void
{
    handleExceptionAs($throwable, ThrowableHandlerHtmlDocument::class);
}

/**
 * @codeCoverageIgnore
 * @throws RuntimeException
 */
function handleExceptionAs(Throwable $throwable, string $document): void
{
    $reflection = new ReflectionClass($document);
    if (!$reflection->implementsInterface(ThrowableHandlerDocumentInterface::class)) {
        trigger_error(
            (new Message('Document %document% must implement %interface%'))
                ->code('%document%', $document)
                ->code('%interface%', ThrowableHandlerDocumentInterface::class)
                ->toString()
        );
    }
    /** @var ThrowableHandlerDocumentInterface $document */
    $document = new $document(
        new ThrowableHandler(new ThrowableRead($throwable))
    );

    try {
        $writer = WritersInstance::get()->error();
    } catch (LogicException $e) {
        $writer = new StreamWriter(streamFor('php://stderr', 'r+'));
    }
    $writer->write($document->toString() . "\n");

    die(255);
}
