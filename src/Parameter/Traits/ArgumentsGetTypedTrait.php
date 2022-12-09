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

namespace Chevere\Parameter\Traits;

trait ArgumentsGetTypedTrait
{
    abstract public function get(string $name): mixed;

    public function getArray(string $name): array
    {
        /** @var array<mixed, mixed> */
        return $this->get($name);
    }

    public function getBoolean(string $name): bool
    {
        /** @var bool */
        return $this->get($name);
    }

    public function getFloat(string $name): float
    {
        /** @var float */
        return $this->get($name);
    }

    public function getInteger(string $name): int
    {
        /** @var int */
        return $this->get($name);
    }

    public function getString(string $name): string
    {
        /** @var string */
        return $this->get($name);
    }
}
