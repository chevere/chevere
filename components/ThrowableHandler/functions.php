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

use Chevere\Components\Instances\WritersInstance;
use Chevere\Components\Message\Message;
use Chevere\Components\ThrowableHandler\Documents\ThrowableHandlerConsoleDocument;
use Chevere\Components\ThrowableHandler\Documents\ThrowableHandlerHtmlDocument;
use Chevere\Components\ThrowableHandler\Documents\ThrowableHandlerPlainDocument;
use Chevere\Components\Writer\StreamWriterFromString;
use Chevere\Exceptions\Core\ErrorException;
use Chevere\Exceptions\Core\LogicException as CoreLogicException;
use Chevere\Interfaces\ThrowableHandler\ThrowableHandlerDocumentInterface;
use Chevere\Interfaces\ThrowableHandler\ThrowableHandlerInterface;
use ReflectionClass;
use Throwable;

function errorsAsExceptions(int $severity, string $message, string $file, int $line): void
{
    throw new ErrorException(new Message($message), 0, $severity, $file, $line);
}

function plainHandler(Throwable $throwable): void
{
    handleExceptionAs($throwable, ThrowableHandlerPlainDocument::class);
}

function consoleHandler(Throwable $throwable): void
{
    handleExceptionAs($throwable, ThrowableHandlerConsoleDocument::class);
}

function htmlHandler(Throwable $throwable): void
{
    handleExceptionAs($throwable, ThrowableHandlerHtmlDocument::class);
}

function handleExceptionAs(Throwable $throwable, string $document): ThrowableHandlerInterface
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
    } catch (CoreLogicException $e) {
        $writer = new StreamWriterFromString('php://stderr', 'w');
    }
    $writer->write($document->toString() . "\n");

    die(255);
}
