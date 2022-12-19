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

/**
 * Describes the component in charge of providing a Map interface.
 *
 * @template TValue
 * @extends MappedInterface<TValue>
 */
interface MapInterface extends MappedInterface
{
    /**
     * @param TValue ...$value
     * @return self<TValue>
     */
    public function withPut(mixed ...$value): self;

    public function has(string ...$key): bool;

    public function assertHas(string ...$key): void;

    /**
     * @return TValue
     */
    public function get(string $key): mixed;
}
