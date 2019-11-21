<?php

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Chevere\Contracts\Router;

use Chevere\Contracts\Route\RouteContract;
use Psr\Http\Message\UriInterface;

interface RouterContract
{
    const CACHE_ID = 'router';

    public function withProperties(RouterPropertiesContract $properties): RouterContract;

    public function arguments(): array;

    public function canResolve(): bool;

    public function resolve(UriInterface $uri): RouteContract;
}
