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

namespace Chevere\DataStructure\Interfaces;

use Countable;
use IteratorAggregate;

/**
 * Describes the component in charge of defining a vector interface.
 *
 * @template TValue
 * @extends IteratorAggregate<TValue>
 */
interface VectorInterface extends Countable, IntegerKeysInterface, IteratorAggregate
{
    public function withPush(mixed ...$value): static;

    public function withSet(int $key, mixed $value): static;

    public function withUnshift(mixed ...$value): static;

    public function withInsert(int $key, mixed ...$values): static;

    public function withRemove(int ...$key): static;

    public function has(int ...$key): bool;

    public function get(int $key): mixed;

    public function find(mixed $value): ?int;

    public function contains(mixed ...$value): bool;
}
