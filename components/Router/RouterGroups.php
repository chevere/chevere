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

namespace Chevere\Components\Router;

use ArrayIterator;
use BadMethodCallException;
use Chevere\Components\Message\Message;
use Chevere\Components\Router\Interfaces\RouterGroupsInterface;
use Chevere\Components\Str\StrAssert;
use LogicException;

final class RouterGroups implements RouterGroupsInterface
{
    /** @var ArrayIterator <string>$group => [$id,]  */
    private ArrayIterator $iterator;

    /** @var ArrayIterator <int>$id => <string>$group */
    private ArrayIterator $index;

    public function __construct()
    {
        $this->iterator = new ArrayIterator();
        $this->index = new ArrayIterator();
    }

    public function withAdded(string $group, int $id): RouterGroupsInterface
    {
        (new StrAssert($group))->notEmpty()->notCtypeSpace();
        $new = clone $this;
        if ($new->index->offsetExists($id)) {
            throw new LogicException(
                (new Message('Id %id% is already bound to group %groupName%'))
                    ->code('%id%', (string) $id)
                    ->code('%groupName%', $new->index->offsetGet($id))
                    ->toString()
            );
        }
        if ($new->iterator->offsetExists($group)) {
            $ids = $new->iterator->offsetGet($group);
        }
        $ids[] = $id;
        $new->iterator->offsetSet($group, $ids);
        $new->index->offsetSet($id, $group);

        return $new;
    }

    public function has(string $group): bool
    {
        return $this->iterator->offsetExists($group);
    }

    public function get(string $group): array
    {
        if (!$this->iterator->offsetExists($group)) {
            throw new BadMethodCallException(
                (new Message("Group %group% doesn't exists"))
                    ->code('%group%', $group)
                    ->toString()
            );
        }

        return $this->iterator->offsetGet($group);
    }

    public function getForId(int $id): string
    {
        return $this->index->offsetExists($id)
            ? $this->index->offsetGet($id)
            : '';
    }

    public function iterator(): ArrayIterator
    {
        return new ArrayIterator($this->iterator->getArrayCopy());
    }

    public function toArray(): array
    {
        $array = [];
        $this->iterator->rewind();
        while ($this->iterator->valid()) {
            $array[$this->iterator->key()] = $this->iterator->current();
            $this->iterator->next();
        }

        return $array;
    }
}
