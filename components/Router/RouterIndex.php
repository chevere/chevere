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
use Chevere\Exceptions\Core\Exception;
use Chevere\Exceptions\Core\InvalidArgumentException;
use Chevere\Exceptions\Core\OutOfBoundsException;
use Chevere\Exceptions\Core\OverflowException;
use Chevere\Interfaces\Router\RoutableInterface;
use Chevere\Interfaces\Router\RouteIdentifierInterface;
use Chevere\Interfaces\Router\RouterIndexInterface;
use Ds\Map;
use OutOfBoundsException as GlobalOutOfBoundsException;

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

    public function withAddedRoutable(RoutableInterface $routable, string $group): RouterIndexInterface
    {
        try {
            (new StrAssert($group))->notEmpty()->notCtypeSpace();
        }
        // @codeCoverageIgnoreStart
        catch (Exception $e) {
            throw new InvalidArgumentException(
                (new Message('Invalid argument %argument% provided'))
                    ->code('%argument%', $group),
                0,
                $e
            );
        }
        // @codeCoverageIgnoreEnd
        $new = clone $this;
        $routeName = $routable->route()->name()->toString();
        /** @var \Ds\TKey $routeKey */
        $routeKey = $routeName;
        if ($new->groupsIndex->hasKey($routeKey)) {
            /** @var string  $groupName*/
            $groupName = $new->groupsIndex->get(/** @scrutinizer ignore-type */ $routeName);
            throw new OverflowException(
                (new Message('Route name %routeName% is already bound to group %groupName%'))
                    ->code('%routeName%', $routeName)
                    ->code('%groupName%', $groupName)
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

    public function hasRouteName(string $name): bool
    {
        /** @var \Ds\TKey $key */
        $key = $name;

        return $this->identifiersMap->hasKey($key);
    }

    public function getRouteIdentifier(string $routeName): RouteIdentifierInterface
    {
        /** @var RouteIdentifierInterface $return */
        try {
            $return = $this->identifiersMap->get($routeName);
        }
        // @codeCoverageIgnoreStart
        catch (\OutOfBoundsException $e) {
            throw new OutOfBoundsException(
                (new Message('Route name %routeName% not found'))
                    ->code('%routeName%', $routeName)
            );
        }
        // @codeCoverageIgnoreEnd

        return $return;
    }

    public function hasGroup(string $group): bool
    {
        return $this->groupsMap->hasKey($group);
    }

    /**
     *
     * @throws GlobalOutOfBoundsException
     */
    public function getGroupRouteNames(string $group): array
    {
        /** @var array $return */
        try {
            $return = $this->groupsMap->get($group);
        }
        // @codeCoverageIgnoreStart
        catch (\OutOfBoundsException $e) {
            throw new OutOfBoundsException(
                (new Message('Group %group% not found'))
                    ->code('%group%', $group)
            );
        }
        // @codeCoverageIgnoreEnd

        return $return;
    }

    public function getRouteGroup(string $group): string
    {
        /** @var string $return */
        try {
            $return = $this->groupsIndex->get($group);
        }
        // @codeCoverageIgnoreStart
        catch (\OutOfBoundsException $e) {
            throw new OutOfBoundsException(
                (new Message('Group %group% not found'))
                    ->code('%group%', $group)
            );
        }
        // @codeCoverageIgnoreEnd

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
