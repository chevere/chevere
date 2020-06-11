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

namespace Chevere\Interfaces\ExceptionHandler;

use DateTimeInterface;

interface ExceptionHandlerInterface
{
    public function withIsDebug(bool $isDebug): ExceptionHandlerInterface;

    public function dateTimeUtc(): DateTimeInterface;

    public function exceptionRead(): ExceptionReadInterface;

    public function id(): string;

    public function isDebug(): bool;
}
