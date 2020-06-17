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

use Chevere\Interfaces\Message\MessageInterface;
use Throwable;

interface ExceptionInterface
{
    public function message(): MessageInterface;

    public function getMessage(): string;

    public function getPrevious(): Throwable;

    public function getCode();

    public function getFile(): string;

    public function getLine(): int;

    public function getTrace(): array;

    public function getTraceAsString(): string;

    public function __toString(): string;
}
