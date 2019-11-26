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

interface RoutedContract
{
    /**
     * Creates a new instance.
     */
    public function __construct(RouteContract $route, array $matches);

    /**
     * Provides access to the RouteContract instance.
     */
    public function route(): RouteContract;

    /**
     * Provides access to the wildcard matches array.
     *
     * @return array [wildcardName => matchedValue]
     */
    public function wildcards(): array;
}
