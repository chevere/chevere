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

namespace Chevere\Router;

use Chevere\Message\Message;
use Chevere\Router\Interfaces\Route\RouteInterface;
use Chevere\Router\Interfaces\RouteIdentifierInterface;
use Chevere\Router\Interfaces\RouterIndexInterface;
use Chevere\Throwable\Errors\TypeError;
use Chevere\Throwable\Exceptions\OutOfBoundsException;
use Chevere\Throwable\Exceptions\OverflowException;
use Ds\Map;

final class RouterIndex implements RouterIndexInterface
{
    /**
     * @var Map [<string>routeName => RouteIdentifier,]
     */
    private Map $identifiersMap;

    /**
     * @var Map [<string>routeName => <string>groupName,]
     */
    private Map $groupsIndex;

    /**
     * @var Map [<string>groupName => [<string>routeName],]
     */
    private Map $groupsMap;

    public function __construct()
    {
        $this->identifiersMap = new Map();
        $this->groupsIndex = new Map();
        $this->groupsMap = new Map();
    }

    public function withAddedRoute(RouteInterface $route, string $group): RouterIndexInterface
    {
        $new = clone $this;
        $routeName = $route->path()->__toString();
        $routeIdentifier = new RouteIdentifier($group, $routeName);
        $routeKey = $routeName;
        if ($new->groupsIndex->hasKey($routeKey)) {
            /** @var string $groupName */
            $groupName = $new->groupsIndex->get($routeName);

            throw new OverflowException(
                (new Message('Route name %routeName% is already bound to group %groupName%'))
                    ->code('%routeName%', $routeName)
                    ->code('%groupName%', $groupName)
            );
        }
        $groupKey = $group;
        $groupValue = $group;
        $new->identifiersMap->put($routeKey, $routeIdentifier);
        $new->groupsIndex->put($routeKey, $groupValue);
        $names = [];
        if ($new->groupsMap->hasKey($groupKey)) {
            $names = $new->groupsMap->get($groupKey);
        }
        $names[] = $routeName;
        $new->groupsMap->put($groupKey, $names);

        return $new;
    }

    public function hasRouteName(string $name): bool
    {
        $key = $name;

        return $this->identifiersMap->hasKey($key);
    }

    /**
     * @throws TypeError
     * @throws OutOfBoundsException
     */
    public function getRouteIdentifier(string $name): RouteIdentifierInterface
    {
        try {
            return $this->identifiersMap->get($name);
        }
        // @codeCoverageIgnoreStart
        // @infection-ignore-all
        catch (\TypeError $e) {
            throw new TypeError(previous: $e);
        }
        // @codeCoverageIgnoreEnd
        catch (\OutOfBoundsException $e) {
            throw new OutOfBoundsException(
                (new Message('Route name %routeName% not found'))
                    ->code('%routeName%', $name)
            );
        }
    }

    public function hasGroup(string $group): bool
    {
        return $this->groupsMap->hasKey($group);
    }

    /**
     * @throws TypeError
     * @throws OutOfBoundsException
     */
    public function getGroupRouteNames(string $group): array
    {
        try {
            return $this->groupsMap->get($group);
        }
        // @codeCoverageIgnoreStart
        // @infection-ignore-all
        catch (\TypeError $e) {
            throw new TypeError(previous: $e);
        }
        // @codeCoverageIgnoreEnd
        catch (\OutOfBoundsException $e) {
            throw new OutOfBoundsException(
                (new Message('Group %group% not found'))
                    ->code('%group%', $group)
            );
        }
    }

    /**
     * @throws TypeError
     * @throws OutOfBoundsException
     */
    public function getRouteGroup(string $group): string
    {
        try {
            return $this->groupsIndex->get($group);
        }
        // @codeCoverageIgnoreStart
        // @infection-ignore-all
        catch (\TypeError $e) {
            throw new TypeError(previous: $e);
        }
        // @codeCoverageIgnoreEnd
        catch (\OutOfBoundsException $e) {
            throw new OutOfBoundsException(
                (new Message('Group %group% not found'))
                    ->code('%group%', $group)
            );
        }
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
