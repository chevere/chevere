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

use Chevere\Components\Message\Message;
use Chevere\Components\Route\Interfaces\RouteInterface;
use Chevere\Components\Router\Interfaces\RouteableInterface;
use Chevere\Components\Router\Interfaces\RouteIdentifierInterface;
use Chevere\Components\Router\Interfaces\RouterIndexInterface;
use Chevere\Components\Str\StrAssert;
use Ds\Map;
use LogicException;

final class RouterIndex implements RouterIndexInterface
{
    /** @var Map [<string>routeName => RouteIdentifier,] */
    private Map $identifiersMap;

    /** @var Map [<string>routeName => <string>groupName,] */
    private Map $groupsIndex;

    /** @var Map [<string>groupName => [<string>routeName],] */
    private Map $groupsMap;

    public function __construct()
    {
        $this->identifiersMap = new Map;
        $this->groupsIndex = new Map;
        $this->groupsMap = new Map;
    }

    public function withAdded(RouteableInterface $routeable, string $group): RouterIndexInterface
    {
        (new StrAssert($group))->notEmpty()->notCtypeSpace();
        $new = clone $this;
        $routeName = $routeable->route()->name()->toString();
        /** @var \Ds\TKey $routeKey */
        $routeKey = $routeName;
        if ($new->groupsIndex->hasKey($routeName)) {
            /** @var string  $groupName*/
            $groupName = $new->groupsIndex->get(/** @scrutinizer ignore-type */ $routeName);
            throw new LogicException(
                (new Message('Route name %routeName% is already bound to group %groupName%'))
                    ->code('%routeName%', $routeName)
                    ->code('%groupName%', $groupName)
                    ->toString()
            );
        }
        /** @var \Ds\TKey $groups */
        $groupKey = $group;
        $new->identifiersMap->put(
            $routeKey,
            new RouteIdentifier($group, $routeName)
        );
        $new->groupsIndex->put($routeKey, $groupKey);
        $names = [];
        if ($new->groupsMap->hasKey($groupKey)) {
            $names = $new->groupsMap->get($groupKey);
        }
        /** @var \Ds\TValue $names */
        $names[] = $routeName;
        $new->groupsMap->put($groupKey, $names);

        return $new;
    }

    public function hasRouteName(string $routeName): bool
    {
        return $this->identifiersMap->hasKey($routeName);
    }

    public function getRouteIdentifier(string $routeName): RouteIdentifierInterface
    {
        /**
         * @var \Ds\TKey $routeName
         * @var RouteIdentifierInterface $return
         */
        $return = $this->identifiersMap->get($routeName);

        return $return;
    }

    public function hasGroup(string $group): bool
    {
        return $this->groupsMap->hasKey($group);
    }

    public function getGroupRouteNames(string $group): array
    {
        /**
         * @var \Ds\TKey $group
         * @var array $return
         */
        $return = $this->groupsMap->get($group);

        return $return;
    }

    public function getRouteGroup(string $routeName): string
    {
        /**
         * @var \Ds\TKey $routeName
         * @var string $return
         */
        $return = $this->groupsIndex->get($routeName);

        return $return;
    }

    public function toArray(): array
    {
        $array = [];
        /** @var RouteIdentifierInterface $routeIdentifier */
        foreach ($this->identifiersMap as $routePath => $routeIdentifier) {
            $array[$routePath] = $routeIdentifier->toArray();
        }

        return $array;
    }
}
