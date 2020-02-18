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

namespace Chevere\Components\ExceptionHandler;

use BadMethodCallException;
use DateTimeZone;
use DateTimeInterface;
use DateTimeImmutable;
use Chevere\Components\ExceptionHandler\Interfaces\ExceptionHandlerInterface;
use Chevere\Components\ExceptionHandler\Interfaces\ExceptionInterface;
use Chevere\Components\Http\Interfaces\RequestInterface;
use Chevere\Components\Message\Message;

/**
 * The Chevere exception handler.
 */
final class ExceptionHandler implements ExceptionHandlerInterface
{
    private DateTimeInterface $dateTimeUtc;

    private ExceptionInterface $exception;

    private string $id;

    private RequestInterface $request;

    private bool $isDebug = false;

    private string $logDestination = '/dev/null';

    /**
     * Creates a new instance.
     *
     * @param mixed $args Arguments passed to the error exception (severity, message, file, line; Exception)
     */
    public function __construct(ExceptionInterface $exception)
    {
        $this->dateTimeUtc = new DateTimeImmutable('now', new DateTimeZone('UTC'));
        $this->exception = $exception;
        $this->id = uniqid('', true);
    }

    public function dateTimeUtc(): DateTimeInterface
    {
        return $this->dateTimeUtc;
    }

    public function exception(): ExceptionInterface
    {
        return $this->exception;
    }

    public function id(): string
    {
        return $this->id;
    }

    public function withIsDebug(bool $isDebug): ExceptionHandlerInterface
    {
        $new = clone $this;
        $new->isDebug = $isDebug;

        return $new;
    }

    public function isDebug(): bool
    {
        return $this->isDebug;
    }

    public function withRequest(RequestInterface $request): ExceptionHandlerInterface
    {
        $new = clone $this;
        $new->request = $request;

        return $new;
    }

    public function hasRequest(): bool
    {
        return isset($this->request);
    }

    public function request(): RequestInterface
    {
        $this->assertPropertyMethod();

        return $this->request;
    }

    public function withLogDestination(string $logDestination): ExceptionHandlerInterface
    {
        $new = clone $this;
        $new->logDestination = $logDestination;

        return $new;
    }

    public function logDestination(): string
    {
        return $this->logDestination;
    }

    private function assertPropertyMethod(): void
    {
        $propertyName = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1]['function'];
        if (!isset($this->$propertyName)) {
            throw new BadMethodCallException(
                (new Message('The method %method% must be called only if the property %propertyName% exists in the instance'))
                    ->code('%method%', __CLASS__ . '::' . $propertyName)
                    ->code('%propertyName%', $propertyName)
                    ->toString()
            );
        }
    }
}

// private function setLogFilePathProperties(): void
// {
//     $absolute = (new PathApp('var/logs/'))->absolute();
//     $date = gmdate($this->logDateFolderFormat, $this->data->key('timestamp'));
//     $id = $this->data->key('id');
//     $timestamp = $this->data->key('timestamp');
//     $logFilename = $absolute . $this->loggerLevel . '/' . $date . $timestamp . '_' . $id . '.log';
//     $this->data = $this->data
//         ->withAddedKey('logFilename', $logFilename);
// }

// private function setLogger(): void
// {
//     $lineFormatter = new LineFormatter(null, null, true, true);
//     $logFilename = $this->data->key('logFilename');
//     $streamHandler = new StreamHandler($logFilename);
//     $streamHandler->setFormatter($lineFormatter);
//     $this->logger = new Logger(__NAMESPACE__);
//     $this->logger->setTimezone(new DateTimeZone('UTC'));
//     $this->logger->pushHandler($streamHandler);
//     $this->logger->pushHandler(new FirePHPHandler());
// }

// private function loggerWrite(): void
// {
//     $log = strip_tags($this->output->textPlain());
//     $log .= "\n\n" . str_repeat('=', Formatter::COLUMNS);
//     $this->logger->log($this->loggerLevel, $log);
// }
