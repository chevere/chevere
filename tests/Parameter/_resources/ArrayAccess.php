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

namespace Chevere\Tests\Parameter\_resources;

abstract class ArrayAccess implements \ArrayAccess
{
    public function offsetSet($offset, $value): void
    {
        $this->{$offset} = $value;
    }

    public function offsetExists($offset): bool
    {
        return isset($this->{$offset});
    }

    public function offsetUnset($offset): void
    {
        unset($this->{$offset});
    }

    public function offsetGet($offset): mixed
    {
        return $this->{$offset} ?? null;
    }
}
