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

use Chevere\Components\Router\Interfaces\RouterGroupsInterface;

final class RouterGroups implements RouterGroupsInterface
{
    private array $array = [];

    public function withAdded(string $group, int $id): RouterGroupsInterface
    {
        $new = clone $this;
        if (array_key_exists($group, $this->array)) {
            $new->array[$group] = [$id];
        } else {
            $new->array[$group][] = $id;
        }

        return $new;
    }

    public function toArray(): array
    {
        return $this->array;
    }
}
