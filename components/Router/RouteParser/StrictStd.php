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

namespace Chevere\Components\Router\RouteParser;

use Chevere\Components\Message\Message;
use Chevere\Exceptions\Core\LogicException;
use FastRoute\BadRouteException;
use FastRoute\RouteParser\Std;

/**
 * Strict version of `FastRoute\RouteParser\Std`, without optional routing.
 */
final class StrictStd extends Std
{
    public function parse($route)
    {
        $routeDatas = parent::parse($route);
        if (count($routeDatas) > 1) {
            throw new LogicException(
                (new Message('Optional routing for route %route% is forbidden'))
                    ->code('%route%', $route)
            );
        }

        return $routeDatas;
    }
}
