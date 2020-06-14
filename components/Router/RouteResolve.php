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

use Chevere\Components\Route\RouteWildcards;
use Chevere\Interfaces\Controller\ControllerInterface;
use Chevere\Interfaces\Route\RouteNameInterface;
use Chevere\Interfaces\Route\RouteWildcardsInterface;

final class RouteResolve
{
    private RouteNameInterface $name;

    private RouteWildcards $wildcards;

    public function __construct(RouteNameInterface $name, RouteWildcardsInterface $wildcards)
    {
        $this->name = $name;
        $this->wildcards = $wildcards;
    }

    public function name(): RouteNameInterface
    {
        return $this->name;
    }

    public function wildcards(): RouteWildcardsInterface
    {
        return $this->wildcards;
    }
}
