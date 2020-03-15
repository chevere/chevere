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

namespace Chevere\Components\Spec;

use Chevere\Components\Http\Interfaces\MethodInterface;
use Chevere\Components\Spec\Interfaces\SpecIndexInterface;
use SplDoublyLinkedList;

/**
 * Maps route id (internal) to endpoint method spec paths.
 */
final class SpecIndex implements SpecIndexInterface
{
    private SplDoublyLinkedList $list;

    public function __construct()
    {
        $this->list = new SplDoublyLinkedList();
    }

    public function withAdded(
        int $id,
        MethodInterface $method,
        string $specPath
    ): SpecIndexInterface {
        $new = clone $this;
        $methods = [];
        if ($new->list->offsetExists($id)) {
            $methods = $new->list->offsetGet($id);
        } else {
            $new->list->add($id, $methods);
        }
        $methods[$method->name()] = $specPath;
        $new->list->offsetSet($id, $methods);

        return $new;
    }

    public function count(): int
    {
        return $this->list->count();
    }

    public function hasAny(): bool
    {
        return $this->list->count() > 0;
    }

    public function has(int $id, MethodInterface $method): bool
    {
        return isset($this->list[$id])
            && isset($this->list[$id][$method->name()]);
    }

    public function get(int $id, MethodInterface $method): string
    {
        return $this->list[$id][$method->name()];
    }
}
