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
use Chevere\Exceptions\Core\ErrorException;
use Chevere\Interfaces\ThrowableHandler\ThrowableHandlerDocumentInterface;
use Chevere\Interfaces\ThrowableHandler\ThrowableHandlerInterface;
use Exception;
use function Chevere\Components\Writer\writers;

/**
 * @codeCoverageIgnore
 */
final class ThrowableHandlerFactory
{
    public static function errorsAsExceptions(int $severity, string $message, string $file, int $line): void
    {
        throw new ErrorException(new Message($message), 0, $severity, $file, $line);
    }

    public static function plain(Exception $exception): void
    {
        self::write(new ThrowableHandlerPlainDocument(self::getHandler($exception)));
    }

    public static function console(Exception $exception): void
    {
        self::write(new ThrowableHandlerConsoleDocument(self::getHandler($exception)));

        die(255);
    }

    public static function html(Exception $exception): void
    {
        self::write(new ThrowableHandlerHtmlDocument(self::getHandler($exception)));
    }

    private static function write(ThrowableHandlerDocumentInterface $document): void
    {
        writers()->out()->write($document->toString() . "\n");
    }

    private static function getHandler(Exception $exception): ThrowableHandlerInterface
    {
        return new ThrowableHandler(new ThrowableRead($exception));
    }
}
