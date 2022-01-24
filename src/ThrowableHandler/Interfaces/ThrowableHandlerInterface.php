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

namespace Chevere\ThrowableHandler\Interfaces;

use DateTimeInterface;

/**
 * Describes the component in charge of handling throwables.
 */
interface ThrowableHandlerInterface
{
    public function __construct(ThrowableReadInterface $throwableRead);

    public function withIsDebug(bool $isDebug): self;

    public function isDebug(): bool;

    public function dateTimeUtc(): DateTimeInterface;

    public function throwableRead(): ThrowableReadInterface;

    public function id(): string;
}
