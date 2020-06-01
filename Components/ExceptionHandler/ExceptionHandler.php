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

use Chevere\Interfaces\ExceptionHandler\ExceptionHandlerInterface;
use Chevere\Interfaces\ExceptionHandler\ExceptionReadInterface;
use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

/**
 * The Chevere exception handler.
 */
final class ExceptionHandler implements ExceptionHandlerInterface
{
    private DateTimeInterface $dateTimeUtc;

    private ExceptionReadInterface $exception;

    private string $id;

    private bool $isDebug = false;

    public function __construct(ExceptionReadInterface $exception)
    {
        $timezone = new DateTimeZone('UTC');
        $this->dateTimeUtc = new DateTimeImmutable('now', $timezone);
        $this->exception = $exception;
        $this->id = uniqid('', true);
        $this->logger = new Logger(__CLASS__);
        $streamHandler = new StreamHandler('php://stderr');
        $streamHandler->setFormatter(new LineFormatter(null, null, true, true));
        $this->logger
            ->setTimezone($timezone)
            ->pushHandler($streamHandler);
    }

    public function withIsDebug(bool $isDebug): ExceptionHandlerInterface
    {
        $new = clone $this;
        $new->isDebug = $isDebug;

        return $new;
    }

    public function dateTimeUtc(): DateTimeInterface
    {
        return $this->dateTimeUtc;
    }

    public function exception(): ExceptionReadInterface
    {
        return $this->exception;
    }

    public function id(): string
    {
        return $this->id;
    }

    public function isDebug(): bool
    {
        return $this->isDebug;
    }
}
