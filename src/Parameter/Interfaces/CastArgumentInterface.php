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

namespace Chevere\Parameter\Interfaces;

/**
 * Describes the component in charge of casting an argument.
 */
interface CastArgumentInterface
{
    public function integer(): int;

    public function float(): float;

    public function boolean(): bool;

    public function string(): string;

    // @phpstan-ignore-next-line
    public function array(): array;

    public function object(): object;

    public function callable(): callable;

    // @phpstan-ignore-next-line
    public function iterable(): iterable;

    public function nullInteger(): ?int;

    public function nullFloat(): ?float;

    public function nullBoolean(): ?bool;

    public function nullString(): ?string;

    // @phpstan-ignore-next-line
    public function nullArray(): ?array;

    public function nullObject(): ?object;

    public function nullCallable(): ?callable;

    // @phpstan-ignore-next-line
    public function nullIterable(): ?iterable;

    public function mixed(): mixed;
}
