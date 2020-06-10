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
use Ds\Set;
use Monolog\Logger;

interface ExceptionHandlerInterface
{
    public function withIsDebug(bool $isDebug): ExceptionHandlerInterface;

    public function dateTimeUtc(): DateTimeInterface;

    public function exception(): ExceptionReadInterface;

    public function id(): string;

    public function isDebug(): bool;
}
