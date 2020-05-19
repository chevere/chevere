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

namespace Chevere\Interfaces\Router;

interface RouterCacheInterface
{
    const KEY_REGEX = 'regex';

    const KEY_INDEX = 'index';

    public function routesCache(): RoutesCacheInterface;

    public function hasRegex(): bool;

    public function hasIndex(): bool;

    public function getRegex(): RouterRegexInterface;

    public function getIndex(): RouterIndexInterface;

    public function put(RouterInterface $router): void;

    public function remove(): void;

    public function puts(): array;
}
