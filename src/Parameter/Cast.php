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

namespace Chevere\Parameter;

use Chevere\Parameter\Interfaces\CastInterface;

final class Cast implements CastInterface
{
    // @phpstan-ignore-next-line
    public function __construct(
        private $argument
    ) {
    }

    public function integer(): int
    {
        return $this->argument;
    }

    public function float(): float
    {
        return $this->argument;
    }

    public function boolean(): bool
    {
        return $this->argument;
    }

    public function string(): string
    {
        return $this->argument;
    }

    // @phpstan-ignore-next-line
    public function array(): array
    {
        return $this->argument;
    }

    public function object(): object
    {
        return $this->argument;
    }

    public function callable(): callable
    {
        return $this->argument;
    }

    // @phpstan-ignore-next-line
    public function iterable(): iterable
    {
        return $this->argument;
    }

    public function mixed(): mixed
    {
        return $this->argument;
    }
}
