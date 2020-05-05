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

use Chevere\Components\Route\Interfaces\RouteNameInterface;
use Chevere\Components\Route\RouteDecorator;
use Chevere\Components\Route\RouteName;

return new class() extends RouteDecorator
{
    public function name(): RouteNameInterface
    {
        return new RouteName('name-alt');
    }
};
