<?php

namespace App;

use Chevere\Route\Route;
use Chevere\Http\Method;

return [
    (new Route('/home', Controllers\Home::class))
        ->withName('homepageHtml'),
    'index' => (new Route('/', Controllers\Index::class))
        ->withName('homepage'),
    // ->addMiddleware(Middlewares\RoleAdmin::class)
    // ->addMiddleware(Middlewares\RoleBanned::class),
    (new Route('/cache/{llave?}-{cert}-{user?}'))
        ->withWhere('llave', '[0-9]+')
        ->withAddedMethod(
            (new Method('GET'))
                ->withController(Controllers\Cache::class)
        )
        ->withAddedMethod(
            (new Method('POST'))
                ->withController(Controllers\Cache::class)
        )
        ->withName('cache'),
];
