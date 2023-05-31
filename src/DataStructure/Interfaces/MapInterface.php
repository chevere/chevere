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

use Chevere\Common\Interfaces\ToArrayInterface;

/**
 * Describes the component in charge of providing a Map interface.
 *
 * @template TValue
 * @extends MappedInterface<TValue>
 */
interface MapInterface extends MappedInterface, ToArrayInterface
{
    /**
     * @param TValue ...$value
     * @return self<TValue>
     */
    public function withPut(string $key, mixed $value): self;

    /**
     * @return self<TValue>
     */
    public function without(string ...$key): self;

    public function has(string ...$key): bool;

    public function assertHas(string ...$key): void;

    /**
     * @return TValue
     */
    public function get(string $key): mixed;
}
