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
use Chevere\Components\Str\StrAssert;
use Chevere\Interfaces\Router\RoutableInterface;
use Chevere\Interfaces\Router\RouteIdentifierInterface;
use Chevere\Interfaces\Router\RouterIndexInterface;
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

    public function withAdded(RoutableInterface $routable, string $group): RouterIndexInterface
    {
        (new StrAssert($group))->notEmpty()->notCtypeSpace();
        $new = clone $this;
        $routeName = $routable->route()->name()->toString();
        /** @var \Ds\TKey $routeKey */
        $routeKey = $routeName;
        if ($new->groupsIndex->hasKey($routeKey)) {
            /** @var string  $groupName*/
            $groupName = $new->groupsIndex->get(/** @scrutinizer ignore-type */ $routeName);
            throw new LogicException(
                (new Message('Route name %routeName% is already bound to group %groupName%'))
                    ->code('%routeName%', $routeName)
                    ->code('%groupName%', $groupName)
                    ->toString()
            );
        }
        /** @var \Ds\TKey $groupKey */
        $groupKey = $group;
        /** @var \Ds\TValue $groupValue */
        $groupValue = $group;
        $new->identifiersMap->put(
            $routeKey,
            new RouteIdentifier($group, $routeName)
        );
        $new->groupsIndex->put($routeKey, $groupValue);
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
        /** @var \Ds\TKey $key */
        $key = $routeName;

        return $this->identifiersMap->hasKey($key);
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
        /**
         * @var \Ds\TKey $group
         * @var bool $return
         */
        $return = $this->groupsMap->hasKey($group);

        return $return;
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
