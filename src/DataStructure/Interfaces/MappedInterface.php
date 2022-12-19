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
 * Describes the component in charge of defining a mapped interface.
 *
 * @template TValue
 * @extends IteratorAggregate<string, TValue>
 */
interface MappedInterface extends Countable, StringKeysInterface, IteratorAggregate
{
    public function count(): int;

    /**
     * Indicates if the provided key is contained in the map.
     */
    public function has(string ...$key): bool;

    /**
     * Asserts that the provided key is contained in the map.
     */
    public function assertHas(string ...$key): void;

    /**
     * Indicates if the provided value is contained in the map.
     *
     * @param TValue ...$value
     */
    public function contains(mixed ...$value): bool;

    /**
     * Asserts that the provided value is contained in the map.
     *
     * @param TValue ...$value
     */
    public function assertContains(mixed ...$value): void;

    /**
     * Returns the key associated with the provided value.
     */
    public function find(mixed $value): ?string;

    /**
     * @return TValue
     */
    public function get(string $key): mixed;
}
