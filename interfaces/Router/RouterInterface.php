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

interface RouterInterface
{
    public function withAddedRoutable(RoutableInterface $routable, string $group): RouterInterface;

    public function index(): RouterIndexInterface;

    public function routables(): RoutablesInterface;

    public function dispatch(string $httpMethod, string $uri): RoutedInterface;
}
