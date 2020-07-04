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

use Chevere\Interfaces\Route\RouteNameInterface;

/**
 * Describes the component in charge of defining a resolved route.
 */
interface RoutedInterface
{
    public function __construct(RouteNameInterface $routeName, array $wildcards);

    public function name(): RouteNameInterface;

    /**
     * @return array [wildcardName => resolvedValue]
     */
    public function arguments(): array;
}
