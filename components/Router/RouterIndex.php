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
        if ($new->groupsIndex->hasKey($routeName)) {
            throw new LogicException(
                (new Message('Route name %routeName% is already bound to group %groupName%'))
                    ->code('%routeName%', $routeName)
                    ->code('%groupName%', $new->groupsIndex->get($routeName))
                    ->toString()
            );
        }
        $new->identifiersMap->put(
            $routeName,
            new RouteIdentifier($group, $routeName)
        );
        $new->groupsIndex->put($routeName, $group);
        $names = [];
        if ($new->groupsMap->hasKey($group)) {
            $names = $new->groupsMap->get($group);
        }
        $names[] = $routeName;
        $new->groupsMap->put($group, $names);

        return $new;
    }

    public function hasRouteName(string $routeName): bool
    {
        return $this->identifiersMap->hasKey($routeName);
    }

    public function getRouteIdentifier(string $routeName): RouteIdentifierInterface
    {
        return $this->identifiersMap->get($routeName);
    }

    public function hasGroup(string $group): bool
    {
        return $this->groupsMap->hasKey($group);
    }

    public function getGroupRouteNames(string $group): array
    {
        return $this->groupsMap->get($group);
    }

    public function getRouteGroup(string $routeName): string
    {
        return $this->groupsIndex->get($routeName);
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
