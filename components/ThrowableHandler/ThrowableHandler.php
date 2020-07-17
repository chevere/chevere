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

use Chevere\Exceptions\Core\RuntimeException;
use Chevere\Interfaces\ThrowableHandler\ThrowableHandlerInterface;
use Chevere\Interfaces\ThrowableHandler\ThrowableReadInterface;
use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use Exception;

final class ThrowableHandler implements ThrowableHandlerInterface
{
    private DateTimeInterface $dateTimeUtc;

    private ThrowableReadInterface $throwableRead;

    private string $id;

    private bool $isDebug = true;

    public function __construct(ThrowableReadInterface $throwableRead)
    {
        try {
            $timezone = new DateTimeZone('UTC');
            $this->dateTimeUtc = new DateTimeImmutable('now', $timezone);
        } catch (Exception $e) {
            throw new RuntimeException(
                null,
                $e->getCode(),
                $e
            );
        }
        $this->throwableRead = $throwableRead;
        $this->id = uniqid('', true);
    }

    public function withIsDebug(bool $isDebug): ThrowableHandlerInterface
    {
        $new = clone $this;
        $new->isDebug = $isDebug;

        return $new;
    }

    public function isDebug(): bool
    {
        return $this->isDebug;
    }

    public function dateTimeUtc(): DateTimeInterface
    {
        return $this->dateTimeUtc;
    }

    public function throwableRead(): ThrowableReadInterface
    {
        return $this->throwableRead;
    }

    public function id(): string
    {
        return $this->id;
    }
}
