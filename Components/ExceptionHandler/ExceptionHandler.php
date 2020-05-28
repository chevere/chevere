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
use Ds\Set;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Http\Message\RequestInterface;
use function DeepCopy\deep_copy;

/**
 * The Chevere exception handler.
 */
final class ExceptionHandler implements ExceptionHandlerInterface
{
    private DateTimeInterface $dateTimeUtc;

    private ExceptionReadInterface $exception;

    private string $id;

    private RequestInterface $request;

    private bool $isDebug = false;

    private Logger $logger;

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

    public function withRequest(RequestInterface $request): ExceptionHandlerInterface
    {
        $new = clone $this;
        $new->request = $request;

        return $new;
    }

    public function withLogger(Logger $logger): ExceptionHandlerInterface
    {
        $new = clone $this;
        $new->logger = $logger;

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

    public function hasRequest(): bool
    {
        return isset($this->request);
    }

    public function request(): RequestInterface
    {
        return $this->request;
    }

    public function loggers(): Set
    {
        return $this->loggers;
    }
}