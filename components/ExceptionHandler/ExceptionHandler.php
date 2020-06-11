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

final class ExceptionHandler implements ExceptionHandlerInterface
{
    private DateTimeInterface $dateTimeUtc;

    private ExceptionReadInterface $exceptionRead;

    private string $id;

    private bool $isDebug = false;

    public function __construct(ExceptionReadInterface $exceptionRead)
    {
        $timezone = new DateTimeZone('UTC');
        $this->dateTimeUtc = new DateTimeImmutable('now', $timezone);
        $this->exceptionRead = $exceptionRead;
        $this->id = uniqid('', true);
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

    public function exceptionRead(): ExceptionReadInterface
    {
        return $this->exceptionRead;
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
