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

use BadMethodCallException;
use Chevere\Components\Message\Message;
use Chevere\Components\Router\Interfaces\RouterGroupsInterface;
use Chevere\Components\Str\StrAssert;

final class RouterGroups implements RouterGroupsInterface
{
    /** @var array <string>$group => [$id,]  */
    private array $array = [];

    /** @var array <int>$id => <string>$group */
    private array $index = [];

    public function withAdded(string $group, int $id): RouterGroupsInterface
    {
        (new StrAssert($group))->notEmpty()->notCtypeSpace();
        $new = clone $this;
        if (!isset($this->array[$group])) {
            $new->array[$group] = [];
        }
        $new->array[$group][] = $id;
        $new->index[$id] = $group;

        return $new;
    }

    public function has(string $group): bool
    {
        return isset($this->array[$group]);
    }

    public function get(string $group): array
    {
        $get = $this->array[$group] ?? null;
        if ($get === null) {
            throw new BadMethodCallException(
                (new Message("Group %group% doesn't exists"))
                    ->code('%group%', $group)
                    ->toString()
            );
        }

        return $get;
    }

    public function getForId(int $id): string
    {
        return $this->index[$id] ?? '';
    }

    public function toArray(): array
    {
        return $this->array;
    }
}
