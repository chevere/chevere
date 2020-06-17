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

namespace Chevere\Interfaces\ThrowableHandler;

use DateTimeInterface;

interface ThrowableHandlerInterface
{
    public function withIsDebug(bool $isDebug): ThrowableHandlerInterface;

    public function dateTimeUtc(): DateTimeInterface;

    public function throwableRead(): ThrowableReadInterface;

    public function id(): string;

    public function isDebug(): bool;
}
