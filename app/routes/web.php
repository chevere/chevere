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

use Chevere\Components\Route\Route;
use Chevere\Components\Http\Method;
use Chevere\Components\Http\MethodController;
use Chevere\Components\Route\PathUri;

return [
    (new Route(new PathUri('/home/{wildcard}')))
        ->withAddedMethodController(
            new MethodController(new Method('GET'), Controllers\Home::class)
        )
        ->withName('web.home'),
    (new Route(new PathUri('/')))
        ->withAddedMethodController(
            new MethodController(new Method('GET'), Controllers\Index::class)
        )
        ->withName('web.root')
        ->withAddedMiddlewareName(Middlewares\RoleBanned::class)
        ->withAddedMiddlewareName(Middlewares\RoleAdmin::class),
];
