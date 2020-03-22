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
use Chevere\Components\Message\Message;
use Chevere\Components\Router\Interfaces\RouterGroupsInterface;
use Chevere\Components\Str\StrAssert;
use Ds\Map;
use LogicException;
use OutOfBoundsException;

final class RouterGroups implements RouterGroupsInterface
{
    /** @var Map <string>$groupName => [$routeName,]  */
    private Map $iterator;

    /** @var Map <string>$routeName => <string>$group */
    private Map $index;

    public function __construct()
    {
        $this->iterator = new Map;
        $this->index = new Map;
    }

    public function withAdded(string $group, string $routeName): RouterGroupsInterface
    {
        (new StrAssert($group))->notEmpty()->notCtypeSpace();
        (new StrAssert($routeName))->notEmpty()->notCtypeSpace();
        $new = clone $this;
        if ($new->index->hasKey($routeName)) {
            throw new LogicException(
                (new Message('Route name %routeName% is already bound to group %groupName%'))
                    ->code('%routeName%', $routeName)
                    ->code('%groupName%', $new->index->get($routeName))
                    ->toString()
            );
        }
        if ($new->iterator->hasKey($group)) {
            $names = $new->iterator->get($group);
        }
        $names[] = $routeName;
        $new->iterator->put($group, $names);
        $new->index->put($routeName, $group);

        return $new;
    }

    public function has(string $group): bool
    {
        return $this->iterator->hasKey($group);
    }

    /**
     * @throws OutOfBoundsException
     */
    public function get(string $group): array
    {
        return $this->iterator->get($group);
    }

    public function getForRouteName(string $routeName): string
    {
        return $this->index->hasKey($routeName)
            ? $this->index->get($routeName)
            : '';
    }

    public function toArray(): array
    {
        $array = [];
        foreach ($this->iterator as $group => $routeNames) {
            $array[$group] = $routeNames;
        }

        return $array;
    }
}
