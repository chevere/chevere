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
use Chevere\Components\Route\PathUri;

return [
    (new Route(new PathUri('/home')))
        ->withAddedMethod(
            (new Method('GET'))
                ->withControllerName(Controllers\Home::class)
        )
        ->withName('web.home'),
    (new Route(new PathUri('/')))
        ->withAddedMethod(
            (new Method('GET'))
                ->withControllerName(Controllers\Index::class)
        )
        ->withName('web.root')
        ->withAddedMiddlewareName(Middlewares\RoleBanned::class)
        ->withAddedMiddlewareName(Middlewares\RoleAdmin::class),
    // ->withAddedMiddlewareName(Middlewares\RoleBanned::class),
    // (new Route('/cache/{llave?}-{cert}-{user?}'))
    //     ->withWhere('llave', '[0-9]+')
    //     ->withAddedMethod(
    //         (new Method('GET'))
    //             ->withControllerName(Controllers\Cache::class)
    //     )
    //     ->withAddedMethod(
    //         (new Method('POST'))
    //             ->withControllerName(Controllers\Cache::class)
    //     )
    //     ->withName('cache'),
];
