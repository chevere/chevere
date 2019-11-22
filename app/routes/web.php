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

namespace App;

use Chevere\Components\Controller\ControllerName;
use Chevere\Components\Route\Route;
use Chevere\Components\Http\Method;
use Chevere\Components\Middleware\MiddlewareName;
use Chevere\Components\Route\PathUri;
use Chevere\Components\Route\RouteName;

return [
    (new Route(new PathUri('/home/{wildcard}')))
        ->withAddedMethod(
            new Method('GET'),
            new ControllerName(Controllers\Home::class)
        )
        ->withName(
            new RouteName('web.home')
        ),
    (new Route(new PathUri('/')))
        ->withAddedMethod(
            new Method('GET'),
            new ControllerName(Controllers\Index::class)
        )
        ->withName(
            new RouteName('web.root')
        )
        ->withAddedMiddlewareName(
            new MiddlewareName(Middlewares\RoleBanned::class)
        )
        ->withAddedMiddlewareName(
            new MiddlewareName(Middlewares\RoleAdmin::class)
        ),
];
