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

use Chevere\Components\Route\Interfaces\RouteInterface;

interface RoutedInterface
{
    public function __construct(RouteInterface $route, array $matches);

    /**
     * Provides access to the RouteInterface instance.
     */
    public function route(): RouteInterface;

    /**
     * Provides access to the wildcard matches array.
     *
     * @return array [wildcardName => matchedValue]
     */
    public function wildcards(): array;
}
