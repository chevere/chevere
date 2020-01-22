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

namespace Chevere\Components\ExceptionHandler\Interfaces;

interface TraceEntryInterface
{
    const KEYS = ['file', 'line', 'function', 'class', 'type'];

    const MUST_HAVE_KEYS = ['file', 'line', 'function'];

    public function file(): string;

    public function line(): int;

    public function fileLine(): string;

    public function function(): string;

    public function class(): string;

    public function type(): string;

    public function args(): array;
}
