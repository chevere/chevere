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

namespace Chevere\Components\Router\Interfaces;

interface RouterCacheInterface
{
    const KEY_REGEX = 'regex';

    const KEY_INDEX = 'index';

    const KEY_NAMED = 'named';

    const KEY_GROUPS = 'groups';

    public function routeCache(): RouteCacheInterface;

    public function hasRegex(): bool;

    public function hasIndex(): bool;

    public function hasNamed(): bool;

    public function hasGroups(): bool;

    public function getRegex(): RouterRegexInterface;

    public function getIndex(): RouterIndexInterface;

    public function getNamed(): RouterNamedInterface;

    public function getGroups(): RouterGroupsInterface;

    public function put(RouterInterface $router): RouterCacheInterface;

    public function remove(): RouterCacheInterface;

    public function puts(): array;
}
