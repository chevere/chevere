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
use Countable;

/**
 * Describes the component in charge of defining a vector interface.
 * @phpstan-ignore-next-line
 */
interface VectorInterface extends Countable, IntegerKeysInterface, GetIteratorInterface, ToArrayInterface
{
    public function withPush(mixed ...$value): self;

    public function withSet(int $key, mixed $value): self;

    public function withUnshift(mixed ...$value): self;

    public function withInsert(int $key, mixed ...$values): self;

    public function withRemove(int ...$key): self;

    public function has(int ...$key): bool;

    public function get(int $key): mixed;

    public function find(mixed $value): ?int;

    public function contains(mixed ...$value): bool;
}
