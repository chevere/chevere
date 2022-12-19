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
    /**
     * @param TValue ...$value
     */
    public function withPush(mixed ...$value): static;

    /**
     * @param TValue ...$value
     */
    public function withSet(int $pos, mixed $value): static;

    /**
     * @param TValue ...$value
     */
    public function withUnshift(mixed ...$value): static;

    public function withInsert(int $key, mixed ...$values): static;

    public function withRemove(int ...$key): static;

    public function has(int ...$key): bool;

    public function get(int $key): mixed;

    /**
     * @param TValue $value
     */
    public function find(mixed $value): ?int;

    /**
     * @param TValue ...$value
     */
    public function contains(mixed ...$value): bool;
}
