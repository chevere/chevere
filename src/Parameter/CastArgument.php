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

use ArrayAccess;
use Chevere\Parameter\Interfaces\CastArgumentInterface;

final class CastArgument implements CastArgumentInterface
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

    /**
     * @infection-ignore-all
     * @phpstan-ignore-next-line
     */
    public function array(): array
    {
        return $this->argument instanceof ArrayAccess
            ? (array) $this->argument
            : $this->argument;
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

    public function nullInteger(): ?int
    {
        return $this->argument ?? null;
    }

    public function nullFloat(): ?float
    {
        return $this->argument ?? null;
    }

    public function nullBoolean(): ?bool
    {
        return $this->argument ?? null;
    }

    public function nullString(): ?string
    {
        return $this->argument ?? null;
    }

    // @phpstan-ignore-next-line
    public function nullArray(): ?array
    {
        return $this->argument ?? null;
    }

    public function nullObject(): ?object
    {
        return $this->argument ?? null;
    }

    public function nullCallable(): ?callable
    {
        return $this->argument ?? null;
    }

    // @phpstan-ignore-next-line
    public function nullIterable(): ?iterable
    {
        return $this->argument ?? null;
    }

    public function mixed(): mixed
    {
        return $this->argument;
    }
}
