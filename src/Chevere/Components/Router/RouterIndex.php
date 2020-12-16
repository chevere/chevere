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
use Chevere\Exceptions\Core\TypeException;
use Chevere\Interfaces\Router\RoutableInterface;
use Chevere\Interfaces\Router\RouteIdentifierInterface;
use Chevere\Interfaces\Router\RouterIndexInterface;
use Ds\Map;
use TypeError;

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
        $this->identifiersMap = new Map();
        $this->groupsIndex = new Map();
        $this->groupsMap = new Map();
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
        $routeName = $routable->route()->path()->toString();
        $routeKey = $routeName;
        if ($new->groupsIndex->hasKey($routeKey)) {
            /** @var string $groupName*/
            $groupName = $new->groupsIndex->get($routeName);

            throw new OverflowException(
                (new Message('Route name %routeName% is already bound to group %groupName%'))
                    ->code('%routeName%', $routeName)
                    ->code('%groupName%', $groupName)
            );
        }
        $groupKey = $group;
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
     * @throws TypeException
     * @throws OutOfBoundsException
     */
    public function getRouteIdentifier(string $name): RouteIdentifierInterface
    {
        try {
            return $this->identifiersMap->get($name);
        }
        // @codeCoverageIgnoreStart
        catch (TypeError $e) {
            throw new TypeException(new Message($e->getMessage()));
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
     * @throws TypeException
     * @throws OutOfBoundsException
     */
    public function getGroupRouteNames(string $group): array
    {
        try {
            return $this->groupsMap->get($group);
        }
        // @codeCoverageIgnoreStart
        catch (TypeError $e) {
            throw new TypeException(new Message($e->getMessage()));
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
     * @throws TypeException
     * @throws OutOfBoundsException
     */
    public function getRouteGroup(string $group): string
    {
        try {
            return $this->groupsIndex->get($group);
        }
        // @codeCoverageIgnoreStart
        catch (TypeError $e) {
            throw new TypeException(new Message($e->getMessage()));
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
