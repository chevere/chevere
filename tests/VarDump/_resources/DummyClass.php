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

namespace Chevere\Tests\VarDump\_resources;

use Chevere\DataStructure\Map;
use stdClass;

final class DummyClass
{
    public object $public;

    private object $private;

    private object $protected;

    private object $circularReference;

    private object $deep;

    public function withPrivate(): self
    {
        $new = clone $this;
        $new->private = new Map(test: 'some');

        return $new;
    }

    public function withProtected(): self
    {
        $new = clone $this;
        $new->protected = new stdClass();

        return $new;
    }

    public function withPublic(): self
    {
        $new = clone $this;
        $new->public = new stdClass();
        $new->public->string = 'string';
        $new->public->array = [];
        $new->public->int = 1;
        $new->public->bool = true;

        return $new;
    }

    public function withCircularReference(): self
    {
        $new = clone $this;
        $new->circularReference = $new;

        return $new;
    }

    public function withDeep($deep): self
    {
        $new = clone $this;
        $new->deep = $deep;

        return $new;
    }
}
