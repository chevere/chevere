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
use Chevere\Interfaces\ThrowableHandler\ThrowableHandlerInterface;
use LogicException;
use Throwable;

// @codeCoverageIgnoreStart

function errorsAsExceptions(int $severity, string $message, string $file, int $line): void
{
    throw new ErrorException(new Message($message), 0, $severity, $file, $line);
}

function plainHandler(Throwable $throwable): void
{
    handleExceptionAs(
        plainHandlerDocument($throwable)
    );
}

function plainHandlerDocument(Throwable $throwable): ThrowableHandlerPlainDocument
{
    return new ThrowableHandlerPlainDocument(
        throwableHandler($throwable)
    );
}

function consoleHandler(Throwable $throwable): void
{
    handleExceptionAs(
        consoleHandlerDocument($throwable)
    );
}

function consoleHandlerDocument(Throwable $throwable): ThrowableHandlerConsoleDocument
{
    return new ThrowableHandlerConsoleDocument(
        throwableHandler($throwable)
    );
}

function htmlHandler(Throwable $throwable): void
{
    if (!headers_sent()) {
        http_response_code(500);
    }
    handleExceptionAs(
        htmlHandlerDocument($throwable)
    );
}

function htmlHandlerDocument(Throwable $throwable): ThrowableHandlerHtmlDocument
{
    return new ThrowableHandlerHtmlDocument(
        throwableHandler($throwable)
    );
}

function throwableHandler(Throwable $throwable): ThrowableHandlerInterface
{
    return new ThrowableHandler(new ThrowableRead($throwable));
}

function handleExceptionAs(ThrowableHandlerDocumentInterface $document): void
{
    try {
        $writer = WritersInstance::get()->error();
    } catch (LogicException $e) {
        $writer = new StreamWriter(streamFor('php://stderr', 'w'));
    }
    $writer->write($document->toString() . "\n");

    die(255);
}

function fatalErrorHandler(): void
{
    $error = error_get_last();
    if ($error === null) {
        return;
    }
    $handler = set_exception_handler(function () {
    });
    restore_exception_handler();
    $handler(
        new ErrorException(
            message: new Message($error["message"]),
            code: 0,
            severity: $error["type"],
            filename: $error["file"],
            lineno: $error["line"]
        )
    );
}

// @codeCoverageIgnoreEnd
