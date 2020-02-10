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
use Chevere\Components\Str\StrAssert;
use Chevere\Components\Message\Message;
use Chevere\Components\Router\Interfaces\RouterGroupsInterface;

final class RouterGroups implements RouterGroupsInterface
{
    private array $array = [];

    public function withAdded(string $group, int $id): RouterGroupsInterface
    {
        (new StrAssert($group))->notEmpty()->notCtypeSpace();
        $new = clone $this;
        if (!array_key_exists($group, $new->array)) {
            $new->array[$group] = [];
        }
        $new->array[$group][] = $id;

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

        return $this->array[$group];
    }

    public function getForId(int $id): string
    {
        foreach ($this->array as $group => $routesId) {
            if (in_array($id, $routesId)) {
                return $group;
            }
        }

        return '';
    }

    public function toArray(): array
    {
        return $this->array;
    }
}
