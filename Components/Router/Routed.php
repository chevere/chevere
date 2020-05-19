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

use Chevere\Interfaces\Route\RouteNameInterface;
use Chevere\Interfaces\Router\RoutedInterface;

//
final class Routed implements RoutedInterface
{
    private RouteNameInterface $name;

    private array $arguments;

    public function __construct(RouteNameInterface $name, array $arguments)
    {
        $this->name = $name;
        $this->arguments = $arguments;
    }

    public function name(): RouteNameInterface
    {
        return $this->name;
    }

    public function arguments(): array
    {
        return $this->arguments;
    }
}
